<?php

namespace App\adms\Models\Repository;

use App\adms\Models\Services\DbConnection;
use PDO;

class TrainingUsersRepository extends DbConnection
{
    public function getByUser(int $userId): array
    {
        $sql = 'SELECT * FROM adms_training_users WHERE adms_user_id = :user_id';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByTraining(int $trainingId): array
    {
        $sql = 'SELECT * FROM adms_training_users WHERE adms_training_id = :training_id';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(':training_id', $trainingId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertOrUpdate(int $userId, int $trainingId, string $status = 'pendente'): void
    {
        $sql = 'INSERT INTO adms_training_users (adms_user_id, adms_training_id, status, created_at, updated_at) 
                VALUES (:user_id, :training_id, :status, NOW(), NOW()) 
                ON DUPLICATE KEY UPDATE status = :status, updated_at = NOW()';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':training_id', $trainingId, PDO::PARAM_INT);
        $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function deleteByUserAndNotInTrainings(int $userId, array $trainingIds): void
    {
        if (empty($trainingIds)) {
            $sql = "DELETE FROM adms_training_users WHERE adms_user_id = ?";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->bindValue(1, $userId, PDO::PARAM_INT);
            $stmt->execute();
            return;
        }
        $in = implode(',', array_fill(0, count($trainingIds), '?'));
        $sql = "DELETE FROM adms_training_users WHERE adms_user_id = ? AND adms_training_id NOT IN ($in)";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        foreach ($trainingIds as $k => $tid) {
            $stmt->bindValue($k+2, $tid, PDO::PARAM_INT);
        }
        $stmt->execute();
    }

    public function getTrainingStatusByUser(array $filters = []): array
    {
        // Busca todos os vínculos de treinamentos dos usuários
        $sql = 'SELECT 
                    u.id as user_id, 
                    u.name as user_name, 
                    d.name as department, 
                    p.name as position,
                    t.id as training_id, 
                    t.codigo, 
                    t.nome as training_name, 
                    t.validade, 
                    t.reciclagem, 
                    t.reciclagem_periodo,
                    tu.id as training_user_id,
                    tu.status,
                    tu.created_at as vinculo_created_at
                FROM adms_training_users tu
                INNER JOIN adms_users u ON u.id = tu.adms_user_id
                INNER JOIN adms_departments d ON u.user_department_id = d.id
                INNER JOIN adms_positions p ON u.user_position_id = p.id
                INNER JOIN adms_trainings t ON t.id = tu.adms_training_id
                WHERE 1=1';
        
        $params = [];
        if (!empty($filters['colaborador'])) {
            $sql .= ' AND u.id = ?';
            $params[] = $filters['colaborador'];
        }
        if (!empty($filters['departamento'])) {
            $sql .= ' AND d.id = ?';
            $params[] = $filters['departamento'];
        }
        if (!empty($filters['cargo'])) {
            $sql .= ' AND p.id = ?';
            $params[] = $filters['cargo'];
        }
        if (!empty($filters['treinamento'])) {
            $sql .= ' AND t.id = ?';
            $params[] = $filters['treinamento'];
        }
        
        $sql .= ' ORDER BY u.name ASC, t.nome ASC';
        
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Buscar dados da última aplicação para cada vínculo
        $appRepo = new \App\adms\Models\Repository\TrainingApplicationsRepository();
        foreach ($results as &$result) {
            // Buscar última aplicação após o vínculo
            $history = $appRepo->getHistoryAfter(
                $result['user_id'], 
                $result['training_id'], 
                $result['vinculo_created_at']
            );
            
            $lastApplication = $history[0] ?? null;
            
            // Adicionar dados da aplicação
            $result['data_realizacao'] = $lastApplication['data_realizacao'] ?? null;
            $result['data_agendada'] = $lastApplication['data_agendada'] ?? null;
            $result['nota'] = $lastApplication['nota'] ?? null;
            $result['observacoes'] = $lastApplication['observacoes'] ?? null;
            $result['instrutor_nome'] = $lastApplication['instrutor_nome'] ?? null;
            $result['instrutor_email'] = $lastApplication['instrutor_email'] ?? null;
            $result['aplicado_por'] = $lastApplication['aplicado_por'] ?? null;
            $result['application_id'] = $lastApplication['id'] ?? null;
            
            // Calcular status dinâmico
            $result['status_dinamico'] = $this->calculateStatus($result);
        }
        
        return $results;
    }

    /**
     * Retorna estatísticas de usuários para um treinamento específico
     */
    public function getTrainingUserStats(int $trainingId): array
    {
        $sql = 'SELECT 
                    COUNT(*) as total_users,
                    SUM(CASE WHEN status = "pendente" THEN 1 ELSE 0 END) as pendente_count,
                    SUM(CASE WHEN status = "concluido" THEN 1 ELSE 0 END) as concluido_count,
                    SUM(CASE WHEN status = "vencido" THEN 1 ELSE 0 END) as vencido_count,
                    AVG(CASE WHEN nota IS NOT NULL THEN nota ELSE NULL END) as media_nota,
                    COUNT(CASE WHEN nota IS NOT NULL THEN 1 ELSE NULL END) as total_avaliacoes
                FROM adms_training_users 
                WHERE adms_training_id = :training_id';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(':training_id', $trainingId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Retorna lista de usuários com status para um treinamento específico
     */
    public function getUsersByTraining(int $trainingId): array
    {
        $sql = 'SELECT tu.*, u.name as user_name, u.email, d.name as department_name, p.name as position_name, tp.reciclagem_periodo
                FROM adms_training_users tu
                INNER JOIN adms_users u ON u.id = tu.adms_user_id
                INNER JOIN adms_departments d ON u.user_department_id = d.id
                INNER JOIN adms_positions p ON u.user_position_id = p.id
                INNER JOIN adms_training_positions tp ON tp.adms_training_id = tu.adms_training_id AND tp.adms_position_id = u.user_position_id
                WHERE tu.adms_training_id = :training_id
                ORDER BY u.name ASC';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(':training_id', $trainingId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Retorna o total de entradas na matriz de treinamentos
     */
    public function getTotalMatrixEntries(): int
    {
        $sql = 'SELECT COUNT(*) as total FROM adms_training_users';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    /**
     * Retorna estatísticas por status
     */
    public function getStatusStatistics(): array
    {
        $sql = 'SELECT 
                    status,
                    COUNT(*) as count
                FROM adms_training_users 
                GROUP BY status 
                ORDER BY count DESC';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Busca treinamentos próximos do vencimento para notificação
     */
    public function getExpiringTrainingsForNotification(int $daysAhead = 30): array
    {
        $sql = 'SELECT 
                    tu.adms_user_id as user_id,
                    tu.adms_training_id as training_id,
                    u.name as user_name,
                    u.email as user_email,
                    t.nome as training_name,
                    t.codigo,
                    t.reciclagem,
                    t.reciclagem_periodo,
                    tu.data_realizacao,
                    tu.status,
                    DATE_ADD(tu.data_realizacao, INTERVAL t.reciclagem_periodo MONTH) as expiry_date
                FROM adms_training_users tu
                INNER JOIN adms_users u ON u.id = tu.adms_user_id
                INNER JOIN adms_trainings t ON t.id = tu.adms_training_id
                WHERE tu.status = "concluido" 
                AND t.reciclagem = 1 
                AND t.reciclagem_periodo > 0
                AND tu.data_realizacao IS NOT NULL
                AND DATE_ADD(tu.data_realizacao, INTERVAL t.reciclagem_periodo MONTH) BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
                AND (tu.last_notification_expiring IS NULL OR tu.last_notification_expiring < DATE_SUB(CURDATE(), INTERVAL 7 DAY))';
        
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(1, $daysAhead, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Busca treinamentos vencidos para notificação
     */
    public function getExpiredTrainingsForNotification(): array
    {
        $sql = 'SELECT 
                    tu.adms_user_id as user_id,
                    tu.adms_training_id as training_id,
                    u.name as user_name,
                    u.email as user_email,
                    t.nome as training_name,
                    t.codigo,
                    t.reciclagem,
                    t.reciclagem_periodo,
                    tu.data_realizacao,
                    tu.status,
                    DATE_ADD(tu.data_realizacao, INTERVAL t.reciclagem_periodo MONTH) as expiry_date
                FROM adms_training_users tu
                INNER JOIN adms_users u ON u.id = tu.adms_user_id
                INNER JOIN adms_trainings t ON t.id = tu.adms_training_id
                WHERE tu.status = "concluido" 
                AND t.reciclagem = 1 
                AND t.reciclagem_periodo > 0
                AND tu.data_realizacao IS NOT NULL
                AND DATE_ADD(tu.data_realizacao, INTERVAL t.reciclagem_periodo MONTH) < CURDATE()
                AND (tu.last_notification_expired IS NULL OR tu.last_notification_expired < DATE_SUB(CURDATE(), INTERVAL 7 DAY))';
        
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Marca como notificado
     */
    public function markAsNotified(int $userId, int $trainingId, string $type): bool
    {
        $column = $type === 'expired' ? 'last_notification_expired' : 'last_notification_expiring';
        $sql = "UPDATE adms_training_users SET {$column} = NOW() WHERE adms_user_id = ? AND adms_training_id = ?";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $trainingId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Calcula o status dinâmico baseado na última aplicação
     */
    private function calculateStatus(array $trainingUser): string
    {
        $dataRealizacao = $trainingUser['data_realizacao'] ?? null;
        $dataAgendada = $trainingUser['data_agendada'] ?? null;
        $validade = $trainingUser['validade'] ?? null;
        $reciclagem = $trainingUser['reciclagem'] ?? null;
        $reciclagemPeriodo = $trainingUser['reciclagem_periodo'] ?? null;
        
        // Se não tem aplicação, está pendente
        if (!$dataRealizacao && !$dataAgendada) {
            return 'pendente';
        }
        
        // Se tem agendamento futuro, está agendado
        if ($dataAgendada && $dataAgendada > date('Y-m-d')) {
            return 'agendado';
        }
        
        // Se tem realização, calcular validade
        if ($dataRealizacao) {
            $dataVencimento = null;
            
            if ($reciclagem && $reciclagemPeriodo) {
                // Reciclagem: data de realização + período
                $dataVencimento = date('Y-m-d', strtotime($dataRealizacao . " +{$reciclagemPeriodo} months"));
            } elseif ($validade) {
                // Validade fixa do treinamento
                $dataVencimento = $validade;
            }
            
            if ($dataVencimento) {
                $hoje = date('Y-m-d');
                $diasParaVencimento = (strtotime($dataVencimento) - strtotime($hoje)) / (60 * 60 * 24);
                
                if ($diasParaVencimento < 0) {
                    return 'vencido';
                } elseif ($diasParaVencimento <= 30) {
                    return 'proximo_vencimento';
                } else {
                    return 'em_dia';
                }
            }
        }
        
        return 'pendente';
    }

    /**
     * Atualiza o status dinâmico de todos os treinamentos
     */
    public function updateDynamicStatuses(): int
    {
        $sql = 'SELECT 
                    tu.*,
                    t.reciclagem,
                    t.reciclagem_periodo
                FROM adms_training_users tu
                INNER JOIN adms_trainings t ON t.id = tu.adms_training_id
                WHERE t.reciclagem = 1 AND t.reciclagem_periodo > 0';
        
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
        $trainings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $updated = 0;
        foreach ($trainings as $training) {
            $newStatus = $this->calculateStatus($training);
            if ($newStatus !== ($training['status'] ?? 'pendente')) {
                $this->updateStatus($training['adms_user_id'], $training['adms_training_id'], $newStatus);
                $updated++;
            }
        }
        
        return $updated;
    }

    /**
     * Atualiza o status de um treinamento específico
     */
    public function updateStatus(int $userId, int $trainingId, string $status): bool
    {
        $sql = 'UPDATE adms_training_users SET status = ?, updated_at = NOW() WHERE adms_user_id = ? AND adms_training_id = ?';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(1, $status, PDO::PARAM_STR);
        $stmt->bindValue(2, $userId, PDO::PARAM_INT);
        $stmt->bindValue(3, $trainingId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Busca treinamento específico de um usuário
     */
    public function getByUserAndTraining(int $userId, int $trainingId): ?array
    {
        $sql = 'SELECT * FROM adms_training_users WHERE adms_user_id = ? AND adms_training_id = ?';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $trainingId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Aplica um treinamento para um usuário
     */
    public function applyTraining(int $userId, int $trainingId, array $data): bool
    {
        // Atualiza o vínculo na tabela de usuários
        $sql = 'INSERT INTO adms_training_users (adms_user_id, adms_training_id, data_realizacao, data_agendada, nota, observacoes, status, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ON DUPLICATE KEY UPDATE
                data_realizacao = VALUES(data_realizacao),
                data_agendada = VALUES(data_agendada),
                nota = VALUES(nota),
                observacoes = VALUES(observacoes),
                status = VALUES(status),
                updated_at = NOW()';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $trainingId, PDO::PARAM_INT);
        $stmt->bindValue(3, $data['data_realizacao'], PDO::PARAM_STR);
        $stmt->bindValue(4, $data['data_agendada'], PDO::PARAM_STR);
        $stmt->bindValue(5, $data['nota'], PDO::PARAM_STR);
        $stmt->bindValue(6, $data['observacoes'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(7, $data['status'], PDO::PARAM_STR);
        $ok = $stmt->execute();

        // Sempre cria um registro na tabela de aplicações
        if ($ok) {
            $appRepo = new \App\adms\Models\Repository\TrainingApplicationsRepository();
            $appRepo->insert([
                'adms_user_id' => $userId,
                'adms_training_id' => $trainingId,
                'data_realizacao' => $data['data_realizacao'] ?? null,
                'data_agendada' => $data['data_agendada'] ?? null,
                'instrutor_nome' => $data['instrutor_nome'] ?? null,
                'instrutor_email' => $data['instrutor_email'] ?? null,
                'aplicado_por' => $data['aplicado_por'] ?? null,
                'nota' => $data['nota'] ?? null,
                'observacoes' => $data['observacoes'] ?? null,
                'status' => $data['status'] ?? 'agendado',
            ]);
        }
        return $ok;
    }

    /**
     * Retorna todas as aplicações/agendamentos de um usuário para um treinamento
     */
    public function getAllApplications(int $userId, int $trainingId): array
    {
        $sql = 'SELECT * FROM adms_training_users WHERE adms_user_id = ? AND adms_training_id = ? ORDER BY data_realizacao DESC, data_agendada DESC, id DESC';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $trainingId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Busca uma aplicação específica pelo id
     */
    public function getById(int $id): ?array
    {
        $sql = 'SELECT * FROM adms_training_users WHERE id = ?';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(1, $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Sincroniza vínculos obrigatórios para um usuário baseado no cargo
     */
    public function syncUserTrainingLinks(int $userId, int $positionId): bool
    {
        try {
            // Buscar treinamentos obrigatórios para o cargo
            $sql = 'SELECT adms_training_id FROM adms_training_positions 
                    WHERE adms_position_id = ? AND obrigatorio = 1';
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->bindValue(1, $positionId, PDO::PARAM_INT);
            $stmt->execute();
            $requiredTrainings = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
            
            if (empty($requiredTrainings)) {
                // Se não há treinamentos obrigatórios, remove todos os vínculos
                $this->deleteByUserAndNotInTrainings($userId, []);
                return true;
            }
            
            // Criar vínculos para treinamentos obrigatórios que não existem
            foreach ($requiredTrainings as $trainingId) {
                $this->insertOrUpdate($userId, $trainingId, 'pendente');
            }
            
            // Remover vínculos de treinamentos que não são mais obrigatórios
            $this->deleteByUserAndNotInTrainings($userId, $requiredTrainings);
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Retorna estatísticas resumidas
     */
    public function getSummary(): array
    {
        // Primeiro, vamos atualizar os status dinâmicos
        $this->updateDynamicStatuses();
        
        $sql = 'SELECT 
                    COUNT(DISTINCT tu.adms_user_id) as total_users,
                    COUNT(*) as total_entries,
                    SUM(CASE WHEN tu.status = "pendente" THEN 1 ELSE 0 END) as pendente_count,
                    SUM(CASE WHEN tu.status = "concluido" THEN 1 ELSE 0 END) as concluido_count,
                    SUM(CASE WHEN tu.status = "vencido" THEN 1 ELSE 0 END) as vencido_count,
                    SUM(CASE WHEN tu.status = "agendado" THEN 1 ELSE 0 END) as agendado_count,
                    SUM(CASE WHEN tu.status = "proximo_vencimento" THEN 1 ELSE 0 END) as proximo_vencimento_count,
                    SUM(CASE WHEN tu.status = "em_dia" THEN 1 ELSE 0 END) as em_dia_count
                FROM adms_training_users tu';
        
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Retorna treinamentos vencidos ou próximos do vencimento
     */
    public function getExpiringTrainings(int $daysAhead = 30): array
    {
        $sql = 'SELECT 
                    u.id as user_id,
                    u.name as user_name,
                    u.email as user_email,
                    d.name as department,
                    p.name as position,
                    t.id as training_id,
                    t.nome as training_name,
                    t.codigo as training_code,
                    tu.status,
                    tu.created_at as vinculo_created_at,
                    ta.data_realizacao,
                    ta.data_agendada
                FROM adms_training_users tu
                INNER JOIN adms_users u ON u.id = tu.adms_user_id
                INNER JOIN adms_departments d ON u.user_department_id = d.id
                INNER JOIN adms_positions p ON u.user_position_id = p.id
                INNER JOIN adms_trainings t ON t.id = tu.adms_training_id
                INNER JOIN adms_training_positions tp ON tp.adms_training_id = t.id 
                    AND tp.adms_position_id = u.user_position_id 
                    AND tp.obrigatorio = 1
                LEFT JOIN adms_training_applications ta ON ta.adms_user_id = tu.adms_user_id 
                    AND ta.adms_training_id = tu.adms_training_id
                    AND ta.created_at >= tu.created_at
                WHERE tu.status IN ("vencido", "proximo_vencimento")
                ORDER BY tu.status DESC, u.name ASC, t.nome ASC';
        
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Marca um vínculo como concluído e cria novo ciclo se necessário
     */
    public function markAsCompleted(int $userId, int $trainingId, bool $createNewCycle = true): bool
    {
        try {
            $this->getConnection()->beginTransaction();
            
            // Marcar vínculo atual como concluído
            $sql = 'UPDATE adms_training_users SET status = "concluido", updated_at = NOW() 
                    WHERE adms_user_id = ? AND adms_training_id = ?';
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->bindValue(1, $userId, PDO::PARAM_INT);
            $stmt->bindValue(2, $trainingId, PDO::PARAM_INT);
            $stmt->execute();
            
            // Se deve criar novo ciclo e o treinamento tem reciclagem
            if ($createNewCycle) {
                $sql = 'SELECT reciclagem, reciclagem_periodo FROM adms_trainings WHERE id = ?';
                $stmt = $this->getConnection()->prepare($sql);
                $stmt->bindValue(1, $trainingId, PDO::PARAM_INT);
                $stmt->execute();
                $training = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($training && $training['reciclagem'] && $training['reciclagem_periodo']) {
                    // Criar novo vínculo para o próximo ciclo
                    $this->insertOrUpdate($userId, $trainingId, 'pendente');
                }
            }
            
            $this->getConnection()->commit();
            return true;
        } catch (\Exception $e) {
            $this->getConnection()->rollBack();
            return false;
        }
    }

    /**
     * Retorna todos os vínculos obrigatórios de treinamentos por colaborador, ordenados por nome
     */
    public function getMandatoryMatrixByUser(array $filters = []): array
    {
        $sql = 'SELECT 
                    u.id as user_id,
                    u.name as user_name,
                    d.name as department,
                    p.name as position,
                    t.id as training_id,
                    t.nome as training_name,
                    t.codigo,
                    t.reciclagem,
                    t.reciclagem_periodo,
                    t.validade,
                    tu.status,
                    tu.created_at as vinculo_created_at
                FROM adms_training_users tu
                INNER JOIN adms_users u ON u.id = tu.adms_user_id
                INNER JOIN adms_departments d ON u.user_department_id = d.id
                INNER JOIN adms_positions p ON u.user_position_id = p.id
                INNER JOIN adms_trainings t ON t.id = tu.adms_training_id
                INNER JOIN adms_training_positions tp ON tp.adms_training_id = t.id 
                    AND tp.adms_position_id = u.user_position_id 
                    AND tp.obrigatorio = 1
                WHERE 1=1';
        $params = [];
        if (!empty($filters['colaborador'])) {
            $sql .= ' AND u.id = ?';
            $params[] = $filters['colaborador'];
        }
        if (!empty($filters['departamento'])) {
            $sql .= ' AND d.id = ?';
            $params[] = $filters['departamento'];
        }
        if (!empty($filters['cargo'])) {
            $sql .= ' AND p.id = ?';
            $params[] = $filters['cargo'];
        }
        if (!empty($filters['treinamento'])) {
            $sql .= ' AND t.id = ?';
            $params[] = $filters['treinamento'];
        }
        $sql .= ' ORDER BY u.name ASC, t.nome ASC';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        // Buscar dados da última aplicação para cada vínculo
        $appRepo = new \App\adms\Models\Repository\TrainingApplicationsRepository();
        foreach ($results as &$result) {
            $history = $appRepo->getHistoryAfter(
                $result['user_id'],
                $result['training_id'],
                $result['vinculo_created_at']
            );
            $lastApplication = $history[0] ?? null;
            $result['data_realizacao'] = $lastApplication['data_realizacao'] ?? null;
            $result['data_agendada'] = $lastApplication['data_agendada'] ?? null;
            $result['nota'] = $lastApplication['nota'] ?? null;
            $result['observacoes'] = $lastApplication['observacoes'] ?? null;
            $result['instrutor_nome'] = $lastApplication['instrutor_nome'] ?? null;
            $result['instrutor_email'] = $lastApplication['instrutor_email'] ?? null;
            $result['aplicado_por'] = $lastApplication['aplicado_por'] ?? null;
            $result['application_id'] = $lastApplication['id'] ?? null;
            $result['status_dinamico'] = $this->calculateStatus($result);
        }
        unset($result);
        return $results;
    }

    /**
     * Retorna contagem de treinamentos por status (para gráfico de pizza)
     */
    public function getStatusCounts(): array
    {
        $sql = 'SELECT status, COUNT(*) as count FROM adms_training_users GROUP BY status';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $counts = [
            'pendente' => 0,
            'em_dia' => 0,
            'proximo_vencimento' => 0,
            'vencido' => 0,
            'concluido' => 0
        ];
        foreach ($result as $row) {
            $counts[$row['status']] = (int)$row['count'];
        }
        return $counts;
    }

    /**
     * Retorna quantidade de treinamentos realizados por mês (últimos 12 meses)
     */
    public function getMonthlyRealizations(): array
    {
        $sql = "SELECT DATE_FORMAT(data_realizacao, '%Y-%m') as mes, COUNT(*) as total FROM adms_training_applications WHERE data_realizacao IS NOT NULL AND data_realizacao >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH) GROUP BY mes ORDER BY mes ASC";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $data = [];
        foreach ($result as $row) {
            $data[$row['mes']] = (int)$row['total'];
        }
        return $data;
    }

    /**
     * Retorna top 5 usuários com mais treinamentos pendentes
     */
    public function getTopPendingUsers(): array
    {
        $sql = "SELECT u.name, COUNT(*) as pendentes FROM adms_training_users tu INNER JOIN adms_users u ON u.id = tu.adms_user_id WHERE tu.status = 'pendente' GROUP BY u.id ORDER BY pendentes DESC, u.name ASC LIMIT 5";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retorna top 5 treinamentos com mais pendências/vencidos
     */
    public function getTopCriticalTrainings(): array
    {
        $sql = "SELECT t.nome as training_name, SUM(CASE WHEN tu.status = 'pendente' THEN 1 ELSE 0 END) as pendentes, SUM(CASE WHEN tu.status = 'vencido' THEN 1 ELSE 0 END) as vencidos FROM adms_training_users tu INNER JOIN adms_trainings t ON t.id = tu.adms_training_id GROUP BY t.id ORDER BY (pendentes + vencidos) DESC, t.nome ASC LIMIT 5";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 