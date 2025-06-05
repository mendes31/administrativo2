<?php

namespace App\adms\Controllers\Services;

use App\adms\Models\Repository\ButtonPermissionUserRepository;
use App\adms\Models\Repository\MenuPermissionUserRepository;
use App\adms\Models\Repository\UsersAccessLevelsRepository;

class PageLayoutService
{
    public function configurePageElements(array $data): array
    {
        // Array com os itens de menu
        $menu = [
            'Dashboard',
            'ListUsers',
            'ListDepartments',
            'ListPositions',
            'ListAccessLevels',
            'ListPackages',
            'ListGroupsPages',
            'ListPages',
            'ListBanks',
            'ListPayments',
            'ListReceive',
            'ListBalance',
            'FinancialReport',
            'ListCostCenters',
            "ListAccountsPlan",
            "ListCustomers",
            "ListSuppliers",
            "ListFrequencies",
            "ListPaymentMethods",
            "Movements",
            "CashFlow",
            "ExportPdfCashFlow",
            // Adicione aqui outras permissões de páginas/submenus que possam existir no futuro
        ];

        // Verificar se o usuário tem o nível de acesso Super Administrador.
        // Nível de acesso Super Administrador tem acesso a todos os botões, não precisa validar as permissões no banco de dados
        $usersAccessLevels = new UsersAccessLevelsRepository();
        if (in_array(1, $usersAccessLevels->getUserAccessLevelArray($_SESSION['user_id']))) {
            return array_merge($data, ['menuPermission' => $menu]);
        }

        // Definir o título da página
        $pageElements['title_head'] = $data['title_head'] ?? '';

        // Ativar o item de menu
        $pageElements['menu'] = $data['menu'] ?? '';

        // Apresentar ou ocultar botão
        $buttonPermission = new ButtonPermissionUserRepository();
        $pageElements['buttonPermission'] = $buttonPermission->buttonPermission($data['buttonPermission'] ?? []);

        // Apresentar ou ocultar item de menu
        $menuPermission = new MenuPermissionUserRepository();
        $pageElements['menuPermission'] = $menuPermission->menuPermission($menu);

        return $pageElements;
    }
}
