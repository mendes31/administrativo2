<?php

namespace App\adms\Controllers\lgpd;

use App\adms\Controllers\Services\PageLayoutService;
use App\adms\Models\Repository\LgpdAipdRepository;
use App\adms\Views\Services\LoadViewService;

/**
 * Controller responsável pela visualização de AIPD (Avaliação de Impacto à Proteção de Dados).
 *
 * @package App\adms\Controllers\lgpd
 */
class LgpdAipdView
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
        $this->data['data_groups'] = $repo->getDataGroupsByAipdId($id);

        $pageElements = [
            'title_head' => 'Visualizar AIPD',
            'menu' => 'ViewLgpdAipd',
            'buttonPermission' => ['ViewLgpdAipd'],
        ];
        $pageLayoutService = new PageLayoutService();
        $this->data = array_merge($this->data, $pageLayoutService->configurePageElements($pageElements));

        $loadView = new LoadViewService("adms/Views/lgpd/aipd/view", $this->data);
        $loadView->loadView();
    }
}
