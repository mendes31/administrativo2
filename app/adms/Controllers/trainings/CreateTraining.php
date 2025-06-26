<?php

namespace App\adms\Controllers\trainings;

use App\adms\Models\Repository\TrainingsRepository;
use App\adms\Controllers\Services\PageLayoutService;
use App\adms\Views\Services\LoadViewService;
use App\adms\Helpers\CSRFHelper;

class CreateTraining
{
    private array|string|null $data = null;

    public function index(): void
    {
        $this->data['form'] = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if (
            isset($this->data['form']['csrf_token']) &&
            CSRFHelper::validateCSRFToken('form_create_training', $this->data['form']['csrf_token'])
        ) {
            $this->addTraining();
        } else {
            $this->viewCreateTraining();
        }
    }

    private function viewCreateTraining(): void
    {
        $pageElements = [
            'title_head' => 'Cadastrar Treinamento',
            'menu' => 'list-trainings',
            'buttonPermission' => ['ListTrainings'],
        ];
        $pageLayoutService = new PageLayoutService();
        $this->data = array_merge($this->data, $pageLayoutService->configurePageElements($pageElements));
        $loadView = new LoadViewService('adms/Views/trainings/create', $this->data);
        $loadView->loadView();
    }

    private function addTraining(): void
    {
        $repo = new TrainingsRepository();
        $result = $repo->createTraining($this->data['form']);
        if ($result) {
            $_SESSION['success'] = 'Treinamento cadastrado com sucesso!';
            header('Location: ' . $_ENV['URL_ADM'] . 'list-trainings');
            exit;
        } else {
            $this->data['errors'][] = 'Erro ao cadastrar treinamento!';
            $this->viewCreateTraining();
        }
    }
} 