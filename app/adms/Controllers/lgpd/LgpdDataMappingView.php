<?php

namespace App\adms\Controllers\lgpd;

use App\adms\Controllers\Services\PageLayoutService;
use App\adms\Models\Repository\LgpdDataMappingRepository;
use App\adms\Views\Services\LoadViewService;

class LgpdDataMappingView
{
    private array|string|null $data = null;

    public function index($id): void
    {
        $repo = new LgpdDataMappingRepository();
        $this->data['data_mapping'] = $repo->getById($id);
        
        if (!$this->data['data_mapping']) {
            $_SESSION['error'] = "Data Mapping não encontrado!";
            header("Location: {$_ENV['URL_ADM']}lgpd-data-mapping");
            return;
        }

        $pageElements = [
            'title_head' => 'Visualizar Data Mapping',
            'menu' => 'ListLgpdDataMapping',
            'buttonPermission' => ['ListLgpdDataMapping', 'EditLgpdDataMapping', 'DeleteLgpdDataMapping'],
        ];
        $pageLayoutService = new PageLayoutService();
        $this->data = array_merge($this->data, $pageLayoutService->configurePageElements($pageElements));

        $loadView = new LoadViewService("adms/Views/lgpd/data-mapping/view", $this->data);
        $loadView->loadView();
    }
}