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
        $trainingUsersRepo = new TrainingUsersRepository();
        $usersRepo = new UsersRepository();
        $departmentsRepo = new DepartmentsRepository();
        $positionsRepo = new PositionsRepository();
        $trainingsRepo = new TrainingsRepository();

        // Filtros da URL
        $filters = [
            'colaborador' => $_GET['colaborador'] ?? null,
            'departamento' => $_GET['departamento'] ?? null,
            'cargo' => $_GET['cargo'] ?? null,
            'treinamento' => $_GET['treinamento'] ?? null,
        ];

        // Dados para a view
        $this->data = [
            'filters' => $filters,
            'matrix' => $trainingUsersRepo->getTrainingStatusByUser($filters),
            'summary' => $trainingUsersRepo->getSummary(),
            'expiring' => $trainingUsersRepo->getExpiringTrainings(30),
            'listDepartments' => $departmentsRepo->getAllDepartmentsSelect(),
            'listPositions' => $positionsRepo->getAllPositionsSelect(),
            'listTrainings' => $trainingsRepo->getAllTrainingsSelect(),
            'listUsers' => $usersRepo->getAllUsersSelect(),
        ];

        // Elementos de página
        $pageElements = [
            'title_head' => 'Matriz de Treinamentos',
            'menu' => 'list-training-status',
            'buttonPermission' => ['ListTrainingStatus'],
        ];
        $pageLayoutService = new PageLayoutService();
        $this->data = array_merge($this->data, $pageLayoutService->configurePageElements($pageElements));

        $loadView = new LoadViewService('adms/Views/trainings/listTrainingStatus', $this->data);
        $loadView->loadView();
    }
} 