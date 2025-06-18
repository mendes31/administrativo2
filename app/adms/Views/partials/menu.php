<?php
$menus = [
    [
        'id' => 'dashboard',
        'icon' => 'fas fa-tachometer-alt',
        'label' => 'Dashboard',
        'url' => $_ENV['URL_ADM'] . 'dashboard',
        'permission' => 'Dashboard',
        'submenu' => []
    ],
    [
        'id' => 'admininstracao',
        'icon' => 'fa-solid fa-gear',
        'label' => 'Admininstracao',
        'submenu' => [
            [
                'label' => 'Grupos de Páginas',
                'url' => $_ENV['URL_ADM'] . 'list-groups-pages',
                'permission' => 'ListGroupsPages'
            ],
            [
                'label' => 'Pacotes',
                'url' => $_ENV['URL_ADM'] . 'list-packages',
                'permission' => 'ListPackages'
            ],
            [
                'label' => 'Páginas',
                'url' => $_ENV['URL_ADM'] . 'list-pages',
                'permission' => 'ListPages'
            ],
        ]
    ],
    [
        'id' => 'cadastro',
        'icon' => 'fa-solid fa-folder-plus',
        'label' => 'Cadastro',
        'submenu' => [
            [
                'label' => 'Cargos',
                'url' => $_ENV['URL_ADM'] . 'list-positions',
                'permission' => 'ListPositions'
            ],
            [
                'label' => 'Centros de Custo',
                'url' => $_ENV['URL_ADM'] . 'list-cost-centers',
                'permission' => 'ListCostCenters'
            ],
            [
                'label' => 'Departamentos',
                'url' => $_ENV['URL_ADM'] . 'list-departments',
                'permission' => 'ListDepartments'
            ],
            [
                'label' => 'Níveis de Acesso',
                'url' => $_ENV['URL_ADM'] . 'list-access-levels',
                'permission' => 'ListAccessLevels'
            ],
            [
                'label' => 'Usuários',
                'url' => $_ENV['URL_ADM'] . 'list-users',
                'permission' => 'ListUsers'
            ],
        ]
    ],
    [
        'id' => 'financeiro',
        'icon' => 'fa-solid fa-coins',
        'label' => 'Financeiro',
        'submenu' => [
            [
                'label' => 'Bancos',
                'url' => $_ENV['URL_ADM'] . 'list-banks',
                'permission' => 'ListBanks'
            ],
            [
                'label' => 'Bancos - Transferência entre Contas',
                'url' => $_ENV['URL_ADM'] . 'list-mov-between-accounts',
                'permission' => 'ListMovBetweenAccounts'
            ],
            [
                'label' => 'Frequências',
                'url' => $_ENV['URL_ADM'] . 'list-frequencies',
                'permission' => 'ListFrequencies'
            ],
            [
                'label' => 'Formas de Pagamento',
                'url' => $_ENV['URL_ADM'] . 'list-payment-methods',
                'permission' => 'ListPaymentMethods'
            ],
            [
                'label' => 'Pagar',
                'url' => $_ENV['URL_ADM'] . 'list-payments',
                'permission' => 'ListPayments'
            ],
            [
                'label' => 'Plano de Contas',
                'url' => $_ENV['URL_ADM'] . 'list-accounts-plan',
                'permission' => 'ListAccountsPlan'
            ],
            [
                'label' => 'Receber',
                'url' => $_ENV['URL_ADM'] . 'list-receipts',
                'permission' => 'ListReceipts'
            ],
            [
                'label' => 'Rel Centro de Custo',
                'url' => $_ENV['URL_ADM'] . 'cost-center-summary',
                'permission' => 'CostCenterSummary'
            ],
            [
                'label' => 'Rel Extrato Caixa',
                'url' => $_ENV['URL_ADM'] . 'movements',
                'permission' => 'Movements'
            ],
            [
                'label' => 'Rel Fluxo de Caixa Diário',
                'url' => $_ENV['URL_ADM'] . 'cash-flow',
                'permission' => 'CashFlow'
            ],
            [
                'label' => 'Relatório Resumo Fianceiro',
                'url' => $_ENV['URL_ADM'] . 'flow-cash-competence',
                'permission' => 'FlowCashCompetence'
            ],
        ]
    ],
    [
        'id' => 'parceiros',
        'icon' => 'fa-solid fa-handshake-simple',
        'label' => 'Parceiros',
        'submenu' => [
            [
                'label' => 'Clientes',
                'url' => $_ENV['URL_ADM'] . 'list-customers',
                'permission' => 'ListCustomers'
            ],
            [
                'label' => 'Fornecedores',
                'url' => $_ENV['URL_ADM'] . 'list-suppliers',
                'permission' => 'ListSuppliers'
            ],
        ]
    ],
    [
        'id' => 'logout',
        'icon' => 'fa-solid fa-arrow-right-from-bracket',
        'label' => 'Sair',
        'url' => $_ENV['URL_ADM'] . 'logout',
        'submenu' => []
    ],
];

// Depuração: exibir as permissões do menu para o usuário atual
// echo '<pre style="color:#fff;background:#222;z-index:9999;position:relative;">';
// print_r($this->data['menuPermission']);
// echo '</pre>';
// ?>

<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-five" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <?php
                // Função para verificar se há pelo menos um submenu permitido
                if (!function_exists('hasPermittedSubmenu')) {
                    function hasPermittedSubmenu($submenu, $menuPermission) {
                        foreach ($submenu as $item) {
                            if (isset($item['permission']) && in_array($item['permission'], $menuPermission)) {
                                return true;
                            }
                        }
                        return false;
                    }
                }
                // Renderização dinâmica dos menus
                foreach ($menus as $menu) {
                    // Se não houver submenu, verifica permissão direta
                    if (empty($menu['submenu'])) {
                        if (isset($menu['permission']) && in_array($menu['permission'], $this->data['menuPermission'])) {
                            echo '<a href="' . $menu['url'] . '" class="nav-link ' . (($this->data['menu'] ?? false) == $menu['id'] ? 'active' : '') . '">';
                            echo '<div class="sb-nav-link-icon"><i class="' . $menu['icon'] . '"></i></div> ' . $menu['label'];
                            echo '</a>';
                        }
                    } else {
                        // Só exibe o menu se houver pelo menos um submenu permitido
                        if (hasPermittedSubmenu($menu['submenu'], $this->data['menuPermission'])) {
                            $submenuId = 'collapse' . ucfirst($menu['id']);
                            // Verifica se algum submenu está ativo
                            $submenuActive = false;
                            $menuAtivo = $this->data['menu'] ?? false;
                            foreach ($menu['submenu'] as $submenu) {
                                if (isset($submenu['permission']) && in_array($submenu['permission'], $this->data['menuPermission'])) {
                                    // Considera ativo se menu principal ou url do submenu estiver ativo
                                    if ($menuAtivo == $menu['id'] || (isset($submenu['url']) && $menuAtivo == basename($submenu['url']))) {
                                        $submenuActive = true;
                                        break;
                                    }
                                }
                            }
                            $isOpen = $submenuActive ? 'show' : '';
                            echo '<a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#' . $submenuId . '" aria-expanded="' . ($isOpen ? 'true' : 'false') . '" aria-controls="' . $submenuId . '">';
                            echo '<div class="sb-nav-link-icon"><i class="' . $menu['icon'] . '"></i></div> ' . $menu['label'];
                            echo '<div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>';
                            echo '</a>';
                            echo '<div class="collapse' . ($isOpen ? ' show' : '') . '" id="' . $submenuId . '" data-bs-parent="#sidenavAccordion">';
                            echo '<nav class="sb-sidenav-menu-nested nav">';
                            foreach ($menu['submenu'] as $submenu) {
                                if (isset($submenu['permission']) && in_array($submenu['permission'], $this->data['menuPermission'])) {
                                    $active = ($menuAtivo == basename($submenu['url'])) ? 'active' : '';
                                    echo '<a href="' . $submenu['url'] . '" class="nav-link ' . $active . '">' . $submenu['label'] . '</a>';
                                }
                            }
                            echo '</nav></div>';
                        }
                    }
                }
                ?>
            </div>
        </div>
        <!-- Rodapé com Informações do Usuário -->
        <div class="sb-sidenav-footer">
            <div class="small">Logado como:</div>
            <?= $_SESSION['user_name'] ?? '' ?><br>
            <?= $_SESSION['user_department'] ?? '' ?><br>
            <?= $_SESSION['user_position'] ?? '' ?>
        </div>
    </nav>
</div>