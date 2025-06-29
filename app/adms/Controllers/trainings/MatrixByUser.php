<?php

namespace App\adms\Controllers\trainings;

use App\adms\Models\Repository\TrainingUsersRepository;
use App\adms\Models\Repository\UsersRepository;
use App\adms\Models\Repository\DepartmentsRepository;
use App\adms\Models\Repository\PositionsRepository;
use App\adms\Models\Repository\TrainingsRepository;

class MatrixByUser
{
    private TrainingUsersRepository $trainingUsersRepo;
    private UsersRepository $usersRepo;
    private DepartmentsRepository $departmentsRepo;
    private PositionsRepository $positionsRepo;
    private TrainingsRepository $trainingsRepo;

    public function __construct()
    {
        $this->trainingUsersRepo = new TrainingUsersRepository();
        $this->usersRepo = new UsersRepository();
        $this->departmentsRepo = new DepartmentsRepository();
        $this->positionsRepo = new PositionsRepository();
        $this->trainingsRepo = new TrainingsRepository();
    }

    public function index(): void
    {
        // Filtros da URL
        $filters = [
            'colaborador' => $_GET['colaborador'] ?? null,
            'departamento' => $_GET['departamento'] ?? null,
            'cargo' => $_GET['cargo'] ?? null,
            'treinamento' => $_GET['treinamento'] ?? null,
        ];

        // Busca todos os vínculos obrigatórios, já ordenados por colaborador, aplicando filtros
        $matrixByUser = $this->trainingUsersRepo->getMandatoryMatrixByUser($filters);

        $data = [
            'title_head' => 'Matriz de Treinamentos por Colaborador',
            'matrixByUser' => $matrixByUser,
            'menu' => 'gestao_treinamentos',
            'buttonPermission' => [],
            'filters' => $filters,
            'listUsers' => $this->usersRepo->getAllUsersSelect(),
            'listDepartments' => $this->departmentsRepo->getAllDepartmentsSelect(),
            'listPositions' => $this->positionsRepo->getAllPositionsSelect(),
            'listTrainings' => $this->trainingsRepo->getAllTrainingsSelect(),
        ];

        $pageLayout = new \App\adms\Controllers\Services\PageLayoutService();
        $data = $pageLayout->configurePageElements($data);

        $loadView = new \App\adms\Views\Services\LoadViewService('adms/Views/trainings/matrixByUser', $data);
        $loadView->loadView();
    }
} 