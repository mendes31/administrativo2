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
 * Controller responsável pela edição de AIPD (Avaliação de Impacto à Proteção de Dados).
 *
 * @package App\adms\Controllers\lgpd
 */
class LgpdAipdEdit
{
    private array|string|null $data = null;

    public function index(string|int $id = null): void
    {
        if (empty($id)) {
            $_SESSION['error'] = "AIPD não encontrada!";
            header("Location: " . $_ENV['URL_ADM'] . "lgpd-aipd");
            exit;
        }

        $repo = new LgpdAipdRepository();
        $aipd = $repo->getAipdById($id);

        if (!$aipd) {
            $_SESSION['error'] = "AIPD não encontrada!";
            header("Location: " . $_ENV['URL_ADM'] . "lgpd-aipd");
            exit;
        }

        $this->data['aipd'] = $aipd;
        $this->data['form'] = $aipd;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->data['form'] = $_POST;
            
            $result = $repo->update($id, $this->data['form']);
            
            if ($result) {
                $_SESSION['success'] = "AIPD editada com sucesso!";
                header("Location: " . $_ENV['URL_ADM'] . "lgpd-aipd");
                exit;
            } else {
                $this->data['errors'][] = "AIPD não editada!";
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
            'title_head' => 'Editar AIPD',
            'menu' => 'EditLgpdAipd',
            'buttonPermission' => ['EditLgpdAipd'],
        ];
        $pageLayoutService = new PageLayoutService();
        $this->data = array_merge($this->data, $pageLayoutService->configurePageElements($pageElements));

        $loadView = new LoadViewService("adms/Views/lgpd/aipd/edit", $this->data);
        $loadView->loadView();
    }
}
