<?php

namespace App\adms\Controllers\lgpd;

use App\adms\Controllers\Services\PageLayoutService;
use App\adms\Models\Repository\LgpdAipdRepository;
use App\adms\Models\Repository\LgpdRopaRepository;
use App\adms\Models\Repository\LgpdDataGroupsRepository;
use App\adms\Models\Repository\DepartmentsRepository;
use App\adms\Models\Repository\UsersRepository;
use App\adms\Views\Services\LoadViewService;

/**
 * Controller responsável pela criação de AIPD (Avaliação de Impacto à Proteção de Dados).
 *
 * @package App\adms\Controllers\lgpd
 */
class LgpdAipdCreate
{
    protected array|string|null $data = null;

    public function index(): void
    {
        $this->data['form'] = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->data['form'] = $_POST;
            
            $repo = new LgpdAipdRepository();
            $result = $repo->create($this->data['form']);
            
            if ($result) {
                $_SESSION['success'] = "AIPD cadastrada com sucesso!";
                header("Location: " . $_ENV['URL_ADM'] . "lgpd-aipd");
                exit;
            } else {
                $this->data['errors'][] = "AIPD não cadastrada!";
            }
        }

        // Carregar dados para os selects
        $departmentsRepo = new DepartmentsRepository();
        $ropaRepo = new LgpdRopaRepository();
        $dataGroupsRepo = new LgpdDataGroupsRepository();
        $usersRepo = new UsersRepository();
        
        $this->data['departamentos'] = $departmentsRepo->getAllDepartmentsSelect();
        $this->data['ropas'] = $ropaRepo->getAllRopaForSelect();
        $this->data['data_groups'] = $dataGroupsRepo->getAllDataGroupsForSelect();
        $this->data['usuarios'] = $usersRepo->getAllUsersForSelect();

        $pageElements = [
            'title_head' => 'Cadastrar AIPD',
            'menu' => 'CreateLgpdAipd',
            'buttonPermission' => ['CreateLgpdAipd'],
        ];
        $pageLayoutService = new PageLayoutService();
        $this->data = array_merge($this->data, $pageLayoutService->configurePageElements($pageElements));

        $loadView = new LoadViewService("adms/Views/lgpd/aipd/create", $this->data);
        $loadView->loadView();
    }
}
