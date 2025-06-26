<?php

namespace App\adms\Models\Repository;

use App\adms\Models\Services\DbConnection;
use PDO;
use App\adms\Helpers\GenerateLog;

class TrainingPositionsRepository extends DbConnection
{
    /**
     * Retorna um array com os IDs dos cargos vinculados a um treinamento
     */
    public function getPositionsByTraining(int $trainingId): array
    {
        $sql = 'SELECT adms_position_id FROM adms_training_positions WHERE adms_training_id = :training_id AND obrigatorio = 1';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(':training_id', $trainingId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0) ?: [];
    }

    /**
     * Salva os vínculos entre um treinamento e os cargos (remove antigos e insere novos)
     */
    public function saveTrainingPositions(int $trainingId, array $obrigatorio): void
    {
        $positionsRepo = new PositionsRepository();
        $allPositions = $positionsRepo->getAllPositions(1, 1000);

        // Buscar todos os vínculos existentes para o treinamento
        $sql = 'SELECT id, adms_position_id FROM adms_training_positions WHERE adms_training_id = :training_id';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(':training_id', $trainingId, PDO::PARAM_INT);
        $stmt->execute();
        $existingLinks = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $existingLinks[$row['adms_position_id']] = $row['id'];
        }

        foreach ($allPositions as $position) {
            $cargoId = $position['id'];
            $isObrigatorio = isset($obrigatorio[$cargoId]) ? 1 : 0;
            if (isset($existingLinks[$cargoId])) {
                // UPDATE
                $sqlUpdate = 'UPDATE adms_training_positions SET obrigatorio = :obrigatorio, updated_at = NOW() WHERE id = :id';
                $stmtUpdate = $this->getConnection()->prepare($sqlUpdate);
                $stmtUpdate->bindValue(':obrigatorio', $isObrigatorio, PDO::PARAM_INT);
                $stmtUpdate->bindValue(':id', $existingLinks[$cargoId], PDO::PARAM_INT);
                $stmtUpdate->execute();
            } else {
                // INSERT
                $sqlInsert = 'INSERT INTO adms_training_positions (adms_training_id, adms_position_id, obrigatorio, created_at, updated_at) VALUES (:training_id, :position_id, :obrigatorio, NOW(), NOW())';
                $stmtInsert = $this->getConnection()->prepare($sqlInsert);
                $stmtInsert->bindValue(':training_id', $trainingId, PDO::PARAM_INT);
                $stmtInsert->bindValue(':position_id', $cargoId, PDO::PARAM_INT);
                $stmtInsert->bindValue(':obrigatorio', $isObrigatorio, PDO::PARAM_INT);
                $stmtInsert->execute();
            }
        }
    }

    public function getTrainingsByPosition(int $positionId): array
    {
        $sql = 'SELECT adms_training_id FROM adms_training_positions WHERE adms_position_id = :position_id AND obrigatorio = 1';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(':position_id', $positionId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0) ?: [];
    }
} 