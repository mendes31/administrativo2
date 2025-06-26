<?php

namespace App\adms\Controllers\trainings;

use App\adms\Models\Repository\UsersRepository;
use App\adms\Models\Repository\PositionsRepository;
use App\adms\Models\Repository\TrainingPositionsRepository;
use App\adms\Models\Repository\TrainingUsersRepository;

class TrainingMatrixService
{
    public function updateMatrixForUser(int $userId): void
    {
        $usersRepo = new UsersRepository();
        $positionsRepo = new PositionsRepository();
        $trainingPositionsRepo = new TrainingPositionsRepository();
        $trainingUsersRepo = new TrainingUsersRepository();

        $user = $usersRepo->getUser($userId);
        $userPositions = $positionsRepo->getPositionsByUser($userId); // array de IDs
        $mandatoryTrainings = [];
        foreach ($userPositions as $positionId) {
            $trainings = $trainingPositionsRepo->getTrainingsByPosition($positionId); // array de IDs obrigatórios
            $mandatoryTrainings = array_merge($mandatoryTrainings, $trainings);
        }
        $mandatoryTrainings = array_unique($mandatoryTrainings);
        // Atualiza matriz: insere/atualiza obrigatórios
        foreach ($mandatoryTrainings as $trainingId) {
            $trainingUsersRepo->insertOrUpdate($userId, $trainingId, 'pendente');
        }
        // Remove vínculos que não são mais obrigatórios
        $trainingUsersRepo->deleteByUserAndNotInTrainings($userId, $mandatoryTrainings);
    }

    public function updateMatrixForAllUsers(): void
    {
        $usersRepo = new UsersRepository();
        $users = $usersRepo->getAllUsers();
        foreach ($users as $user) {
            $this->updateMatrixForUser($user['id']);
        }
    }
} 