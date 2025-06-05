<?php

namespace App\adms\Controllers\accessLevels;

use App\adms\Controllers\Services\PageLayoutService;
use App\adms\Controllers\Services\PaginationService;
use App\adms\Models\Repository\AccessLevelsRepository;
use App\adms\Models\Repository\ButtonPermissionUserRepository;
use App\adms\Models\Repository\MenuPermissionUserRepository;
use App\adms\Views\Services\LoadViewService;

/**
 * Controller para listar níveis de acesso
 *
 * Esta classe é responsável por recuperar e exibir uma lista de níveis de acesso no sistema. Utiliza um repositório
 * para obter dados dos níveis de acesso e um serviço de paginação para gerenciar a navegação entre páginas de resultados.
 * Em seguida, carrega a visualização correspondente com os dados recuperados.
 * 
 * @package App\adms\Controllers\acessLevels
 * @author Rafael Mendes
 */
class ListAccessLevels
{
    /** @var array|string|null $data Dados que devem ser enviados para a VIEW */
    private array|string|null $data = null;

    /** @var int $limitResult Limite de registros por página */
    private int $limitResult = 1000; // Ajuste conforme necessário

    /**
     * Recuperar e listar níveis de acesso com paginação.
     * 
     * Este método recupera os níveis de acesso a partir do repositório de níveis de acesso com base na página atual e no limite
     * de registros por página. Gera os dados de paginação e carrega a visualização para exibir a lista de níveis de acesso.
     * 
     * @param string|int $page Página atual para a exibição dos resultados. O padrão é 1.
     * 
     * @return void
     */
    public function index(string|int $page = 1): void
    {
        // Instanciar o Repository para recuperar os registros do banco de dados
        $listAccessLevels = new AccessLevelsRepository();

        // Recuperar os níveis de acesso para a página atual
        $this->data['accessLevels'] = $listAccessLevels->getAllAccessLevels((int) $page, (int) $this->limitResult);

        // Gerar dados de paginação
        $this->data['pagination'] = PaginationService::generatePagination(
            (int) $listAccessLevels->getAmountAccessLevels(), 
            (int) $this->limitResult, 
            (int) $page, 
            'list-access-levels'
        );

        // var_dump($this->data);

        // Definir o título da página
        // Ativar o item de menu
        // Apresentar ou ocultar botão 
        $pageElements = [
            'title_head' => 'Listar Níveis de Acesso',
            'menu' => 'list-access-levels',
            'buttonPermission' => ['CreateAccessLevel', 'ViewAccessLevel', 'UpdateAccessLevel', 'DeleteAccessLevel', 'AccessLevelPageSync', 'ListAccessLevelsPermissions'],
        ];

        $pageLayoutService = new PageLayoutService();
        // var_dump($pageLayoutService->configurePageElements($pageElements));
        
        // $pageLayoutService->configurePageElements($pageElements);
        $this->data = array_merge($this->data, $pageLayoutService->configurePageElements($pageElements));

        // Apresentar ou ocultar item de menu
        // $menu = ['Dashboard', 'ListUsers', 'ListDepartments', 'ListPositions', 'ListAccessLevels', 'ListPackages', 'ListGroupsPages', 'ListPages'];
        // $menuPermission = new MenuPermissionUserRepository();
        // $this->data['menuPermission'] = $menuPermission->menuPermission($menu);

        // var_dump($this->data);

        // Carregar a VIEW com os dados
        $loadView = new LoadViewService("adms/Views/accessLevels/list", $this->data);
        $loadView->loadView();
    }
}
