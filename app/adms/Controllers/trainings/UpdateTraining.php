<?php

namespace App\adms\Controllers\trainings;

use App\adms\Models\Repository\TrainingsRepository;
use App\adms\Controllers\Services\PageLayoutService;
use App\adms\Views\Services\LoadViewService;
use App\adms\Helpers\CSRFHelper;

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
        $repo = new TrainingsRepository();
        $result = $repo->updateTraining($this->id, $this->data['form']);
        if ($result) {
            $_SESSION['success'] = 'Treinamento atualizado com sucesso!';
            header('Location: ' . $_ENV['URL_ADM'] . 'list-trainings');
            exit;
        } else {
            $this->data['errors'][] = 'Erro ao atualizar treinamento!';
            $this->viewUpdateTraining();
        }
    }
} 