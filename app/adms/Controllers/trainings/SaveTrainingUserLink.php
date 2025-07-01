<?php
namespace App\adms\Controllers\trainings;

use App\adms\Models\Repository\TrainingUsersRepository;

class SaveTrainingUserLink
{
    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $_ENV['URL_ADM'] . 'list-trainings');
            exit;
        }
        $trainingId = isset($_POST['training_id']) ? (int)$_POST['training_id'] : null;
        $userIds = $_POST['user_ids'] ?? [];
        if ($trainingId && !empty($userIds)) {
            $repo = new TrainingUsersRepository();
            $repo->vincularUsuariosTreinamento($trainingId, $userIds);
            $_SESSION['msg'] = '<div class="alert alert-success">Colaboradores vinculados com sucesso!</div>';
            header('Location: ' . $_ENV['URL_ADM'] . 'list-trainings');
            exit;
        } else {
            $_SESSION['msg'] = '<div class="alert alert-danger">Selecione pelo menos um colaborador.</div>';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
    }
} 