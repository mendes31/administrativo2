<?php

namespace App\adms\Controllers\trainings;

use App\adms\Controllers\Services\PageLayoutService;
use App\adms\Controllers\Services\PaginationService;
use App\adms\Models\Repository\TrainingsRepository;
use App\adms\Views\Services\LoadViewService;

class ListTrainings
{
    private array|string|null $data = null;
    private int $limitResult = 10;

    public function index(string|int $page = 1)
    {
        // Capturar o parâmetro page da URL, se existir
        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            $page = (int)$_GET['page'];
        }
        // Tratar per_page
        if (isset($_GET['per_page']) && in_array((int)$_GET['per_page'], [10, 20, 50, 100])) {
            $this->limitResult = (int)$_GET['per_page'];
        }
        $filters = [
            'nome' => $_GET['nome'] ?? '',
            'codigo' => $_GET['codigo'] ?? '',
            'ativo' => $_GET['ativo'] ?? '',
            'instrutor' => $_GET['instrutor'] ?? '',
            'tipo' => $_GET['tipo'] ?? '',
            'reciclagem' => $_GET['reciclagem'] ?? '',
        ];
        $repo = new TrainingsRepository();
        $this->data['trainings'] = $repo->getAllTrainings((int)$page, (int)$this->limitResult, $filters);
        // Adicionar total de colaboradores vinculados em cada treinamento
        foreach ($this->data['trainings'] as &$training) {
            $training['colaboradores_vinculados'] = $repo->getTotalColaboradoresVinculados($training['id']);
            // Log temporário para depuração
            error_log('Treinamento ID ' . $training['id'] . ' - Colaboradores vinculados: ' . $training['colaboradores_vinculados']);
            $training['cargos_vinculados'] = $repo->getLinkedPositionsCount($training['id']);
        }
        unset($training);
        $totalTrainings = $repo->getTotalTrainings($filters);
        $pagination = PaginationService::generatePagination(
            (int) $totalTrainings,
            (int) $this->limitResult,
            (int) $page,
            'list-trainings',
            array_merge($filters, ['per_page' => $this->limitResult])
        );
        $this->data['pagination'] = $pagination;
        $this->data['per_page'] = $this->limitResult;
        $pageElements = [
            'title_head' => 'Listar Treinamentos',
            'menu' => 'list-trainings',
            'buttonPermission' => ['CreateTraining', 'ViewTraining', 'UpdateTraining', 'DeleteTraining'],
        ];
        $pageLayoutService = new PageLayoutService();
        $this->data = array_merge($this->data, $pageLayoutService->configurePageElements($pageElements));
        $loadView = new LoadViewService('adms/Views/trainings/list', $this->data);
        $loadView->loadView();
    }
} 