<?php

namespace App\adms\Models\Repository;

use App\adms\Models\Services\DbConnection;
use App\adms\Helpers\GenerateLog;
use PDO;
use Exception;

class TrainingApplicationsRepository extends DbConnection
{
    /**
     * Insere um novo registro de aplicação/agendamento de treinamento
     */
    public function insert(array $data): int|bool
    {
        try {
            $sql = 'INSERT INTO adms_training_applications (
                        adms_user_id, adms_training_id, data_realizacao, data_agendada, instrutor_nome, instrutor_email, aplicado_por, nota, observacoes, status, created_at, updated_at
                    ) VALUES (
                        :adms_user_id, :adms_training_id, :data_realizacao, :data_agendada, :instrutor_nome, :instrutor_email, :aplicado_por, :nota, :observacoes, :status, NOW(), NOW()
                    )';
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->bindValue(':adms_user_id', $data['adms_user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':adms_training_id', $data['adms_training_id'], PDO::PARAM_INT);
            $stmt->bindValue(':data_realizacao', $data['data_realizacao'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':data_agendada', $data['data_agendada'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':instrutor_nome', $data['instrutor_nome'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':instrutor_email', $data['instrutor_email'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':aplicado_por', $data['aplicado_por'] ?? null, PDO::PARAM_INT);
            $stmt->bindValue(':nota', $data['nota'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':observacoes', $data['observacoes'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':status', $data['status'] ?? 'agendado', PDO::PARAM_STR);
            $stmt->execute();
            $novoId = $this->getConnection()->lastInsertId();
            
            // Log de inserção
            if ($novoId) {
                $dadosDepois = [
                    'id' => $novoId,
                    'adms_user_id' => $data['adms_user_id'],
                    'adms_training_id' => $data['adms_training_id'],
                    'data_realizacao' => $data['data_realizacao'] ?? null,
                    'data_agendada' => $data['data_agendada'] ?? null,
                    'instrutor_nome' => $data['instrutor_nome'] ?? null,
                    'instrutor_email' => $data['instrutor_email'] ?? null,
                    'aplicado_por' => $data['aplicado_por'] ?? null,
                    'nota' => $data['nota'] ?? null,
                    'observacoes' => $data['observacoes'] ?? null,
                    'status' => $data['status'] ?? 'agendado',
                ];
                \App\adms\Models\Services\LogAlteracaoService::registrarAlteracao(
                    'adms_training_applications',
                    $novoId,
                    $_SESSION['user_id'] ?? 0,
                    'insert',
                    [],
                    $dadosDepois
                );
            }
            return $novoId;
        } catch (Exception $e) {
            GenerateLog::generateLog("error", "Aplicação de treinamento não cadastrada.", [
                'user_id' => $data['adms_user_id'] ?? '',
                'training_id' => $data['adms_training_id'] ?? '',
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Atualiza um registro de aplicação/agendamento
     */
    public function update(int $id, array $data): bool
    {
        try {
            // Captura os dados antigos antes da alteração
            $dadosAntes = $this->getById($id);
            
            $sql = 'UPDATE adms_training_applications SET
                        data_realizacao = :data_realizacao,
                        data_agendada = :data_agendada,
                        instrutor_nome = :instrutor_nome,
                        instrutor_email = :instrutor_email,
                        aplicado_por = :aplicado_por,
                        nota = :nota,
                        observacoes = :observacoes,
                        status = :status,
                        updated_at = NOW()
                    WHERE id = :id';
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->bindValue(':data_realizacao', $data['data_realizacao'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':data_agendada', $data['data_agendada'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':instrutor_nome', $data['instrutor_nome'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':instrutor_email', $data['instrutor_email'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':aplicado_por', $data['aplicado_por'] ?? null, PDO::PARAM_INT);
            $stmt->bindValue(':nota', $data['nota'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':observacoes', $data['observacoes'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':status', $data['status'] ?? 'agendado', PDO::PARAM_STR);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();
            
            // Se atualização bem-sucedida, registra o log de alteração
            if ($result && $dadosAntes) {
                $dadosDepois = [
                    'id' => $id,
                    'adms_user_id' => $dadosAntes['adms_user_id'],
                    'adms_training_id' => $dadosAntes['adms_training_id'],
                    'data_realizacao' => $data['data_realizacao'] ?? null,
                    'data_agendada' => $data['data_agendada'] ?? null,
                    'instrutor_nome' => $data['instrutor_nome'] ?? null,
                    'instrutor_email' => $data['instrutor_email'] ?? null,
                    'aplicado_por' => $data['aplicado_por'] ?? null,
                    'nota' => $data['nota'] ?? null,
                    'observacoes' => $data['observacoes'] ?? null,
                    'status' => $data['status'] ?? 'agendado',
                ];
                \App\adms\Models\Services\LogAlteracaoService::registrarAlteracao(
                    'adms_training_applications',
                    $id,
                    $_SESSION['user_id'] ?? 0,
                    'update',
                    $dadosAntes,
                    $dadosDepois
                );
            }
            return $result;
        } catch (Exception $e) {
            GenerateLog::generateLog("error", "Aplicação de treinamento não editada.", [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Busca o histórico de aplicações/agendamentos de um usuário para um treinamento
     */
    public function getHistory(int $userId, int $trainingId): array
    {
        $sql = 'SELECT * FROM adms_training_applications WHERE adms_user_id = ? AND adms_training_id = ? ORDER BY data_realizacao DESC, data_agendada DESC, id DESC';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $trainingId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Busca o histórico de aplicações/agendamentos de um usuário para um treinamento após uma data específica
     */
    public function getHistoryAfter(int $userId, int $trainingId, ?string $dataVinculo): array
    {
        $sql = 'SELECT * FROM adms_training_applications WHERE adms_user_id = ? AND adms_training_id = ?';
        $params = [$userId, $trainingId];
        if ($dataVinculo) {
            $sql .= ' AND created_at >= ?';
            $params[] = $dataVinculo;
        }
        $sql .= ' ORDER BY data_realizacao DESC, data_agendada DESC, id DESC';
        $stmt = $this->getConnection()->prepare($sql);
        foreach ($params as $i => $param) {
            $stmt->bindValue($i+1, $param, is_int($param) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Busca um registro específico pelo id
     */
    public function getById(int $id): ?array
    {
        $sql = 'SELECT * FROM adms_training_applications WHERE id = ?';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(1, $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Retorna as aplicações mais recentes
     */
    public function getRecentApplications(int $limit = 10): array
    {
        $sql = 'SELECT 
                    ta.*,
                    u.name as user_name,
                    t.nome as training_name,
                    t.codigo as training_code
                FROM adms_training_applications ta
                INNER JOIN adms_users u ON u.id = ta.adms_user_id
                INNER JOIN adms_trainings t ON t.id = ta.adms_training_id
                ORDER BY ta.created_at DESC
                LIMIT ?';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
} 