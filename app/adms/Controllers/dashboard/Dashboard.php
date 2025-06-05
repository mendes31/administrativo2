<?php

namespace App\adms\Controllers\dashboard;

use App\adms\Controllers\Services\PageLayoutService;
use App\adms\Models\Repository\MenuPermissionUserRepository;
use App\adms\Views\Services\LoadViewService;

class Dashboard
{
    /** @var array $data Recebe os dados que devem ser enviados para a VIEW */
    private array $data = [];

    public function index()
    {

        // Definir o título da página
        // Ativar o item de menu
        // Apresentar ou ocultar botão 
        $pageElements = [
            'title_head' => 'Dashboard',
            'menu' => 'dashboard',
            'buttonPermission' => [],
        ];
        
        $pageLayoutService = new PageLayoutService();
        $pageLayoutService->configurePageElements($pageElements);
        $this->data = array_merge($this->data, $pageLayoutService->configurePageElements($pageElements));

        // Apresentar ou ocultar item de menu
        // $menu = ['Dashboard', 'ListUsers', 'ListDepartments', 'ListPositions', 'ListAccessLevels', 'ListPackages', 'ListGroupsPages', 'ListPages'];
        // $menuPermission = new MenuPermissionUserRepository();
        // $this->data['menuPermission'] = $menuPermission->menuPermission($menu);

        // Carregar a VIEW
        $loadView = new LoadViewService("adms/Views/dashboard/dashboard", $this->data);
        $loadView->loadView();
    }
      
}