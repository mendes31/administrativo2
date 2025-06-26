<?php

namespace App\adms\Controllers\trainings;

use App\adms\Models\Repository\UsersRepository;
use App\adms\Models\Repository\DepartmentsRepository;
use App\adms\Models\Repository\PositionsRepository;
use App\adms\Models\Repository\TrainingsRepository;
use App\adms\Models\Repository\TrainingUsersRepository;
use App\adms\Views\Services\LoadViewService;
use App\adms\Controllers\Services\PageLayoutService;

class ListTrainingStatus
{
    private array $data = [];

    public function index(): void
    {
        $filters = $_GET;
        $usersRepo = new UsersRepository();
        $departmentsRepo = new DepartmentsRepository();
        $positionsRepo = new PositionsRepository();
        $trainingsRepo = new TrainingsRepository();
        $trainingUsersRepo = new TrainingUsersRepository();

        // Filtros
        $this->data['filters'] = [
            'colaborador' => $filters['colaborador'] ?? '',
            'departamento' => $filters['departamento'] ?? '',
            'cargo' => $filters['cargo'] ?? '',
            'status' => $filters['status'] ?? '',
            'treinamento' => $filters['treinamento'] ?? '',
        ];

        // Listas para selects
        $this->data['listDepartments'] = $departmentsRepo->getAllDepartmentsSelect();
        $this->data['listPositions'] = $positionsRepo->getAllPositionsSelect();
        $this->data['listTrainings'] = $trainingsRepo->getAllTrainingsSelect();
        $this->data['listUsers'] = $usersRepo->getAllUsersSelect();

        // Buscar status dos treinamentos obrigatórios por colaborador
        $this->data['trainingStatus'] = $trainingUsersRepo->getTrainingStatusByUser($this->data['filters']);

        // Ordenar por nome do colaborador
        usort($this->data['trainingStatus'], fn($a, $b) => strcmp($a['user_name'], $b['user_name']));

        // Elementos de página
        $pageElements = [
            'title_head' => 'Status de Treinamentos por Colaborador',
            'menu' => 'list-training-status',
            'buttonPermission' => ['ListTrainingStatus'],
        ];
        $pageLayoutService = new PageLayoutService();
        $this->data = array_merge($this->data, $pageLayoutService->configurePageElements($pageElements));

        $loadView = new LoadViewService('adms/Views/trainings/listTrainingStatus', $this->data);
        $loadView->loadView();
    }
} 