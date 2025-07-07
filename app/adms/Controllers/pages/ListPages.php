<?php

namespace App\adms\Controllers\pages;

use App\adms\Controllers\Services\PageLayoutService;
use App\adms\Controllers\Services\PaginationService;
use App\adms\Models\Repository\PagesRepository;
use App\adms\Views\Services\LoadViewService;

/**
 * Controller para listar páginas
 *
 * Esta classe é responsável por recuperar e exibir uma lista de páginas no sistema. Utiliza um repositório
 * para obter dados das páginas e um serviço de paginação para gerenciar a navegação entre páginas de resultados.
 * Em seguida, carrega a visualização correspondente com os dados recuperados.
 * 
 * @package App\adms\Controllers\pages
 * @author Rafael Mendes
 */
class ListPages
{
    /** @var array|string|null $data Dados que devem ser enviados para a VIEW */
    private array|string|null $data = null;

    /** @var int $limitResult Limite de registros por página */
    private int $limitResult = 1000; // Ajuste conforme necessário

    /**
     * Recuperar e listar páginas com paginação.
     * 
     * Este método recupera as páginas a partir do repositório de páginas com base na página atual e no limite
     * de registros por página. Gera os dados de paginação e carrega a visualização para exibir a lista de páginas.
     * 
     * @param string|int $page Página atual para a exibição dos resultados. O padrão é 1.
     * 
     * @return void
     */
    public function index(string|int $page = 1): void
    {
        // Instanciar o Repository para recuperar os registros do banco de dados
        $listPages = new PagesRepository();

        // Recuperar as páginas para a página atual
        $this->data['pages'] = $listPages->getAllPages((int) $page, (int) $this->limitResult);

        // Gerar dados de paginação
        $this->data['pagination'] = PaginationService::generatePagination(
            (int) $listPages->getAmountPages(), 
            (int) $this->limitResult, 
            (int) $page, 
            'list-pages'
        );

        // Definir o título da página
        // Ativar o item de menu
        // Apresentar ou ocultar botão 
        $pageElements = [
            'title_head' => 'Listar Páginas',
            'menu' => 'list-pages',
            'buttonPermission' => ['CreatePage', 'ViewPage', 'UpdatePage', 'DeletePage'],
        ];
        $pageLayoutService = new PageLayoutService();
        $this->data = array_merge($this->data, $pageLayoutService->configurePageElements($pageElements));

        // Apresentar ou ocultar item de menu
        // $menu = ['Dashboard', 'ListUsers', 'ListDepartments', 'ListPositions', 'ListAccessLevels', 'ListPackages', 'ListGroupsPages', 'ListPages'];
        // $menuPermission = new MenuPermissionUserRepository();
        // $this->data['menuPermission'] = $menuPermission->menuPermission($menu);

        // Carregar a VIEW com os dados
        $loadView = new LoadViewService("adms/Views/pages/list", $this->data);
        $loadView->loadView();
    }
}
