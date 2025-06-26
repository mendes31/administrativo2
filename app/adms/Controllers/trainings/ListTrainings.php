<?php

namespace App\adms\Controllers\trainings;

use App\adms\Models\Repository\TrainingsRepository;
use App\adms\Controllers\Services\PageLayoutService;
use App\adms\Views\Services\LoadViewService;

class ListTrainings
{
    private array|string|null $data = null;
    private int $limitResult = 20;

    public function index(string|int $page = 1): void
    {
        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            $page = (int)$_GET['page'];
        }
        $page = (int)($page ?: 1);

        $repo = new TrainingsRepository();
        $trainings = $repo->getAllTrainings($page, $this->limitResult);
        foreach ($trainings as &$training) {
            $training['cargos_vinculados'] = $repo->getLinkedPositionsCount($training['id']);
        }
        $this->data['trainings'] = $trainings;
        $this->data['pagination'] = []; // Adicione paginação se necessário

        $pageElements = [
            'title_head' => 'Listar Treinamentos',
            'menu' => 'list-trainings',
            'buttonPermission' => ['CreateTraining', 'UpdateTraining', 'DeleteTraining', 'ListTrainings'],
        ];
        $pageLayoutService = new PageLayoutService();
        $this->data = array_merge($this->data, $pageLayoutService->configurePageElements($pageElements));

        $loadView = new LoadViewService('adms/Views/trainings/list', $this->data);
        $loadView->loadView();
    }
} 