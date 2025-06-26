<?php

namespace App\adms\Models\Repository;

use App\adms\Models\Services\DbConnection;
use PDO;

class TrainingsRepository extends DbConnection
{
    public function getAllTrainings(int $page = 1, int $limit = 20): array
    {
        $offset = max(0, ($page - 1) * $limit);
        $sql = 'SELECT * FROM adms_trainings ORDER BY id ASC LIMIT :limit OFFSET :offset';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTraining(int|string $id): array|bool
    {
        $sql = 'SELECT * FROM adms_trainings WHERE id = :id LIMIT 1';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createTraining(array $data): bool|int
    {
        $sql = 'INSERT INTO adms_trainings (nome, codigo, versao, validade, tipo, instrutor, carga_horaria, ativo, created_at) VALUES (:nome, :codigo, :versao, :validade, :tipo, :instrutor, :carga_horaria, :ativo, NOW())';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(':nome', $data['nome'], PDO::PARAM_STR);
        $stmt->bindValue(':codigo', $data['codigo'], PDO::PARAM_STR);
        $stmt->bindValue(':versao', $data['versao'], PDO::PARAM_STR);
        $stmt->bindValue(':validade', $data['validade'], PDO::PARAM_STR);
        $stmt->bindValue(':tipo', $data['tipo'], PDO::PARAM_STR);
        $stmt->bindValue(':instrutor', $data['instrutor'], PDO::PARAM_STR);
        $stmt->bindValue(':carga_horaria', $data['carga_horaria'], PDO::PARAM_INT);
        $stmt->bindValue(':ativo', $data['ativo'] ?? 1, PDO::PARAM_BOOL);
        $stmt->execute();
        return $this->getConnection()->lastInsertId();
    }

    public function updateTraining(int|string $id, array $data): bool
    {
        $sql = 'UPDATE adms_trainings SET nome = :nome, codigo = :codigo, versao = :versao, validade = :validade, tipo = :tipo, instrutor = :instrutor, carga_horaria = :carga_horaria, ativo = :ativo, updated_at = NOW() WHERE id = :id';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(':nome', $data['nome'], PDO::PARAM_STR);
        $stmt->bindValue(':codigo', $data['codigo'], PDO::PARAM_STR);
        $stmt->bindValue(':versao', $data['versao'], PDO::PARAM_STR);
        $stmt->bindValue(':validade', $data['validade'], PDO::PARAM_STR);
        $stmt->bindValue(':tipo', $data['tipo'], PDO::PARAM_STR);
        $stmt->bindValue(':instrutor', $data['instrutor'], PDO::PARAM_STR);
        $stmt->bindValue(':carga_horaria', $data['carga_horaria'], PDO::PARAM_INT);
        $stmt->bindValue(':ativo', $data['ativo'] ?? 1, PDO::PARAM_BOOL);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteTraining(int|string $id): bool
    {
        $sql = 'DELETE FROM adms_trainings WHERE id = :id LIMIT 1';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getLinkedPositionsCount(int $trainingId): int
    {
        $sql = 'SELECT COUNT(*) FROM adms_training_positions WHERE adms_training_id = :training_id AND obrigatorio = 1';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(':training_id', $trainingId, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function getAllTrainingsSelect(): array
    {
        $sql = 'SELECT id, nome as name FROM adms_trainings ORDER BY nome ASC';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 