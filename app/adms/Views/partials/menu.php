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
                [
                'label' => 'Configurações',
                'icon' => 'fa-solid fa-sliders',
                'submenu' => [
                    [
                        'label' => 'Configuração de E-mail',
                        'url' => $_ENV['URL_ADM'] . 'email-config',
                        'permission' => 'EmailConfig'
                    ],
                ]
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
        'id' => 'garantia',
        'icon' => 'fa-solid fa-coins',
        'label' => 'Garantia da Qualidade',
        'submenu' => [
            [
                'label' => 'Documentos',
                'url' => $_ENV['URL_ADM'] . 'list-documents',
                'permission' => 'ListDocuments'
            ],
            [
                'label' => 'Treinamentos Obrigatórios',
                'url' => $_ENV['URL_ADM'] . 'list-mandatory-trainings',
                'permission' => 'ListMandatoryTrainings'
            ],
            // [
            //     'label' => 'Treinamentos',
            //     'url' => $_ENV['URL_ADM'] . 'list-trainings',
            //     'permission' => 'ListTrainings'
            // ],
            ]
    ],
    [
        'id' => 'gestao_treinamentos',
        'icon' => 'fa-solid fa-chalkboard-teacher',
        'label' => 'Gestão de Treinamentos',
        'submenu' => [
            [
                'label' => 'Cadastrar Treinamentos',
                'url' => $_ENV['URL_ADM'] . 'list-trainings',
                'permission' => 'ListTrainings'
            ],
            [
                'label' => 'Dashboard de KPIs ',
                'url' => $_ENV['URL_ADM'] . 'training-kpi-dashboard',
                'permission' => 'TrainingKPIDashboard'
            ],
            [
                'label' => 'Matriz por Colaborador',
                'url' => $_ENV['URL_ADM'] . 'matrix-by-user',
                'permission' => 'MatrixByUser'
            ],
            [
                'label' => 'Matriz de Treinamentos Realizados',
                'url' => $_ENV['URL_ADM'] . 'completed-trainings-matrix',
                'permission' => 'CompletedTrainingsMatrix'
            ],
            [
                'label' => 'Dashboard de Treinamentos',
                'url' => $_ENV['URL_ADM'] . 'training-dashboard',
                'permission' => 'TrainingDashboard'
            ],
            [
                'label' => 'Histórico de Reciclagem',
                'url' => $_ENV['URL_ADM'] . 'training-history',
                'permission' => 'TrainingHistory'
            ],
            
            // [
            //     'label' => 'Atualizar Matriz de Treinamentos',
            //     'url' => $_ENV['URL_ADM'] . 'update-training-matrix',
            //     'permission' => 'UpdateTrainingMatrix'
            // ],
            [
                'label' => 'Status de Treinamentos',
                'url' => $_ENV['URL_ADM'] . 'list-training-status',
                'permission' => 'ListTrainingStatus'
            ],
            // Atalhos para testes
            [
                'label' => 'Testar Notificações',
                'url' => $_ENV['URL_ADM'] . 'test-notification',
                'permission' => 'TestNotification'
            ],
            [
                'label' => 'Criar Dados de Teste',
                'url' => $_ENV['URL_ADM'] . 'create-test-data',
                'permission' => 'CreateTestData'
            ],
            [
                'label' => 'Avaliações',
                'url' => '#',
                'permission' => 'ListEvaluationModels',
                'submenu' => [
                    [
                        'label' => 'Modelos de Avaliação',
                        'url' => $_ENV['URL_ADM'] . 'list-evaluation-models',
                        'permission' => 'ListEvaluationModels'
                    ],
                    [
                        'label' => 'Perguntas de Avaliação',
                        'url' => $_ENV['URL_ADM'] . 'list-evaluation-questions',
                        'permission' => 'ListEvaluationQuestions'
                    ],
                    [
                        'label' => 'Respostas de Avaliação',
                        'url' => $_ENV['URL_ADM'] . 'list-evaluation-answers',
                        'permission' => 'ListEvaluationAnswers'
                    ],
                    [
                        'label' => 'Minhas Avaliações',
                        'url' => $_ENV['URL_ADM'] . 'my-evaluations',
                        'permission' => 'MyEvaluations'
                    ],
                    [
                        'label' => 'Histórico de Avaliações',
                        'url' => $_ENV['URL_ADM'] . 'historico-avaliacoes',
                        'permission' => 'HistoricoAvaliacoes'
                    ],
                    [
                        'label' => 'Notificações',
                        'url' => $_ENV['URL_ADM'] . 'notificacoes',
                        'permission' => 'Notificacoes'
                    ]
                ]
            ]
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
                // Função recursiva para renderizar submenus aninhados
                if (!function_exists('renderMenu')) {
                    function renderMenu($menus, $menuPermission, $menuAtivo = null, $nivel = 0, $parentId = 'sidenavAccordion') {
                        foreach ($menus as $index => $menu) {
                            $hasSubmenu = !empty($menu['submenu']);
                            $hasPermitted = isset($menu['permission']) ? in_array($menu['permission'], $menuPermission) : false;
                            if ($hasSubmenu) {
                                $permittedSubmenus = array_filter($menu['submenu'], function($submenu) use ($menuPermission) {
                                    return isset($submenu['permission']) && in_array($submenu['permission'], $menuPermission);
                                });
                                if (count($permittedSubmenus) > 0) {
                                    // Gera um id único para cada submenu
                                    $submenuId = 'collapse' . md5(($menu['label'] ?? 'submenu') . $nivel . $index);
                                    $submenuActive = false;
                                    foreach ($menu['submenu'] as $submenu) {
                                        if (isset($submenu['permission']) && in_array($submenu['permission'], $menuPermission)) {
                                            if ((isset($menu['id']) && $menuAtivo == $menu['id']) || (isset($submenu['url']) && $menuAtivo == basename($submenu['url']))) {
                                                $submenuActive = true;
                                                break;
                                            }
                                        }
                                    }
                                    $isOpen = $submenuActive ? 'show' : '';
                                    echo '<a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#' . $submenuId . '" aria-expanded="' . ($isOpen ? 'true' : 'false') . '" aria-controls="' . $submenuId . '">';
                                    if ($nivel == 0 && isset($menu['icon'])) {
                                        echo '<div class="sb-nav-link-icon"><i class="' . $menu['icon'] . '"></i></div> ';
                                    }
                                    echo $menu['label'];
                                    echo '<div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>';
                                    echo '</a>';
                                    echo '<div class="collapse' . ($isOpen ? ' show' : '') . '" id="' . $submenuId . '" data-bs-parent="#' . $parentId . '">';
                                    echo '<nav class="sb-sidenav-menu-nested nav">';
                                    renderMenu($menu['submenu'], $menuPermission, $menuAtivo, $nivel + 1, $submenuId);
                                    echo '</nav></div>';
                                }
                            } else {
                                if ($hasPermitted) {
                                    $active = ($menuAtivo == basename($menu['url'])) ? 'active' : '';
                                    echo '<a href="' . $menu['url'] . '" class="nav-link ' . $active . '">' . ($nivel == 0 && isset($menu['icon']) ? '<div class="sb-nav-link-icon"><i class="' . $menu['icon'] . '"></i></div> ' : '') . $menu['label'] . '</a>';
                                }
                            }
                        }
                    }
                }
                // Renderização dinâmica dos menus
                $menuAtivo = $this->data['menu'] ?? false;
                renderMenu($menus, $this->data['menuPermission'], $menuAtivo);
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