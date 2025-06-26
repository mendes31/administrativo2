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

    public function insertOrUpdate(int $userId, int $trainingId, string $status = 'pendente')
    {
        $sql = 'INSERT INTO adms_training_users (adms_user_id, adms_training_id, status, created_at, updated_at) VALUES (:user_id, :training_id, :status, NOW(), NOW()) ON DUPLICATE KEY UPDATE status = :status, updated_at = NOW()';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':training_id', $trainingId, PDO::PARAM_INT);
        $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function deleteByUserAndNotInTrainings(int $userId, array $trainingIds)
    {
        if (empty($trainingIds)) return;
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
        $sql = 'SELECT u.id as user_id, u.name as user_name, d.name as department, p.name as position, t.id as training_id, t.codigo, t.nome as training_name, t.validade, tu.status, tu.data_realizacao, tu.nota
                FROM adms_training_users tu
                INNER JOIN adms_users u ON u.id = tu.adms_user_id
                INNER JOIN adms_departments d ON u.user_department_id = d.id
                INNER JOIN adms_positions p ON u.user_position_id = p.id
                INNER JOIN adms_trainings t ON t.id = tu.adms_training_id
                INNER JOIN adms_training_positions tp ON tp.adms_training_id = t.id AND tp.adms_position_id = u.user_position_id AND tp.obrigatorio = 1
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
        if (!empty($filters['status'])) {
            $sql .= ' AND tu.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['treinamento'])) {
            $sql .= ' AND t.id = ?';
            $params[] = $filters['treinamento'];
        }
        $sql .= ' ORDER BY u.name ASC, t.nome ASC';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 