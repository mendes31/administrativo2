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
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = isset($_GET['per_page']) && in_array((int)$_GET['per_page'], [10, 20, 50, 100]) ? (int)$_GET['per_page'] : 10;
        $offset = ($page - 1) * $perPage;
        $filters = [
            'colaborador' => $_GET['colaborador'] ?? null,
            'departamento' => $_GET['departamento'] ?? null,
            'cargo' => $_GET['cargo'] ?? null,
            'treinamento' => $_GET['treinamento'] ?? null,
        ];
        
        $matrixByUser = [];
        $total = 0;
        if (!empty($filters['treinamento'])) {
            // Se filtrou por um treinamento específico, traz todos os vinculados (cargo e individual)
            $matrixByUser = $this->trainingUsersRepo->getAllVinculadosPorTreinamento($filters['treinamento']);
            $total = count($matrixByUser);
            // Paginação manual
            $matrixByUser = array_slice($matrixByUser, $offset, $perPage);
        } else {
            // Comportamento padrão (apenas obrigatórios por cargo)
            $matrixByUser = $this->trainingUsersRepo->getMandatoryMatrixByUser($filters, $perPage, $offset);
            if (!is_array($matrixByUser)) {
                $matrixByUser = [];
            }
            $total = count($this->trainingUsersRepo->getMandatoryMatrixByUser($filters, 1000000, 0));
        }
        $pagination = \App\adms\Controllers\Services\PaginationService::generatePagination(
            $total,
            $perPage,
            $page,
            'matrix-by-user',
            array_merge($filters, ['per_page' => $perPage])
        );
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
            'pagination' => $pagination,
            'per_page' => $perPage,
        ];
        
        $pageLayout = new \App\adms\Controllers\Services\PageLayoutService();
        $data = $pageLayout->configurePageElements($data);
        $loadView = new \App\adms\Views\Services\LoadViewService('adms/Views/trainings/matrixByUser', $data);
        $loadView->loadView();
    }
} 