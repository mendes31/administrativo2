<?php

namespace App\adms\Controllers\trainings;

use App\adms\Models\Repository\TrainingsRepository;
use App\adms\Controllers\Services\PageLayoutService;
use App\adms\Views\Services\LoadViewService;
use App\adms\Helpers\CSRFHelper;
use App\adms\Models\Repository\UsersRepository;

class UpdateTraining
{
    private array|string|null $data = null;
    private int|string $id;

    public function index(int|string $id): void
    {
        $this->id = $id;
        $this->data['form'] = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if (
            isset($this->data['form']['csrf_token']) &&
            CSRFHelper::validateCSRFToken('form_update_training', $this->data['form']['csrf_token'])
        ) {
            $this->editTraining();
        } else {
            $this->viewUpdateTraining();
        }
    }

    private function viewUpdateTraining(): void
    {
        $repo = new TrainingsRepository();
        $this->data['training'] = $repo->getTraining($this->id);
        $usersRepo = new UsersRepository();
        $this->data['listUsers'] = $usersRepo->getAllUsersSelect();
        $pageElements = [
            'title_head' => 'Editar Treinamento',
            'menu' => 'list-trainings',
            'buttonPermission' => ['ListTrainings'],
        ];
        $pageLayoutService = new PageLayoutService();
        $this->data = array_merge($this->data, $pageLayoutService->configurePageElements($pageElements));
        $loadView = new LoadViewService('adms/Views/trainings/update', $this->data);
        $loadView->loadView();
    }

    private function editTraining(): void
    {
        // Determinar tipo de instrutor e ajustar campos
        if (!empty($this->data['form']['instructor_user_id'])) {
            // Instrutor interno - buscar e-mail do usuário
            $usersRepo = new UsersRepository();
            $user = $usersRepo->getUser((int)$this->data['form']['instructor_user_id']);
            if ($user) {
                $this->data['form']['instructor_email'] = $user['email'];
            }
            // Limpar campo de instrutor externo se existir
            $this->data['form']['instrutor'] = '';
        } elseif (!empty($this->data['form']['instructor_email'])) {
            // Instrutor externo - limpar campo de usuário interno
            $this->data['form']['instructor_user_id'] = null;
            $this->data['form']['instrutor'] = 'Instrutor Externo';
        } else {
            // Nenhum instrutor definido - limpar campos
            $this->data['form']['instructor_user_id'] = null;
            $this->data['form']['instructor_email'] = null;
            $this->data['form']['instrutor'] = '';
        }
        
        $repo = new TrainingsRepository();
        $result = $repo->updateTraining($this->id, $this->data['form']);
        if ($result) {
            $matrixService = new \App\adms\Controllers\trainings\TrainingMatrixService();
            $matrixService->updateMatrixForAllUsers();
            $_SESSION['success'] = 'Treinamento atualizado com sucesso!';
            header('Location: ' . $_ENV['URL_ADM'] . 'list-trainings');
            exit;
        } else {
            $this->data['errors'][] = 'Erro ao atualizar treinamento!';
            $this->viewUpdateTraining();
        }
    }
} 