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
        $trainingPositionsRepo = new TrainingPositionsRepository();
        $trainingUsersRepo = new TrainingUsersRepository();

        $user = $usersRepo->getUser($userId);
        $userPosition = $user['user_position_id']; // cargo único do usuário

        // Treinamentos obrigatórios para o cargo do usuário
        $mandatoryTrainings = $trainingPositionsRepo->getTrainingsByPosition($userPosition);
        $mandatoryTrainings = array_unique($mandatoryTrainings);

        // Remove todos os vínculos que não são mais obrigatórios
        $trainingUsersRepo->deleteByUserAndNotInTrainings($userId, $mandatoryTrainings);

        // Garante que todos os obrigatórios estejam na matriz
        foreach ($mandatoryTrainings as $trainingId) {
            $trainingUsersRepo->insertOrUpdate($userId, $trainingId, 'pendente');
        }
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