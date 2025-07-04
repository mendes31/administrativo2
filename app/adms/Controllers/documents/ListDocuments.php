<?php

namespace App\adms\Controllers\documents;

use App\adms\Controllers\Services\PageLayoutService;
use App\adms\Controllers\Services\PaginationService;
use App\adms\Models\Repository\DocumentsRepository;
use App\adms\Views\Services\LoadViewService;

/**
 * Controller para listar documentos
 *
 * Esta classe é responsável por recuperar e exibir uma lista de documentos no sistema. Utiliza um repositório
 * para obter dados dos documentos e um serviço de paginação para gerenciar a navegação entre documentos de resultados.
 * Em seguida, carrega a visualização correspondente com os dados recuperados.
 * 
 * @package App\adms\Controllers\documents
 * @author Rafael Mendes
 */
class ListDocuments
{
    /** @var array|string|null $data Dados que devem ser enviados para a VIEW */
    private array|string|null $data = null;

    /** @var int $limitResult Limite de registros por página */
    private int $limitResult = 1000; // Ajuste conforme necessário

    /**
     * Recuperar e listar documentos com paginação.
     * 
     * Este método recupera as documentos a partir do repositório de documentos com base na página atual e no limite
     * de registros por página. Gera os dados de paginação e carrega a visualização para exibir a lista de documentos.
     * 
     * @param string|int $page Página atual para a exibição dos resultados. O padrão é 1.
     * 
     * @return void
     */
    public function index(string|int $page = 1): void
    {
        // Instanciar o Repository para recuperar os registros do banco de dados
        $listDocuments = new DocumentsRepository();

        // Recuperar os documentos para a página atual
        $this->data['documents'] = $listDocuments->getAllDocuments((int) $page, (int) $this->limitResult);

        // Gerar dados de paginação
        $this->data['pagination'] = PaginationService::generatePagination(
            (int) $listDocuments->getAmountDocuments(), 
            (int) $this->limitResult, 
            (int) $page, 
            'list-documents'
        );

        // Definir o título da página
        // Ativar o item de menu
        // Apresentar ou ocultar botão 
        $pageElements = [
            'title_head' => 'Listar Documentos',
            'menu' => 'list-documents',
            'buttonPermission' => ['CreateDocument', 'ViewDocument', 'UpdateDocument', 'DeleteDocument', 'ListDocumentPositions'],
        ];
        $pageLayoutService = new PageLayoutService();
        $pageLayoutService->configurePageElements($pageElements);
        $this->data = array_merge($this->data, $pageLayoutService->configurePageElements($pageElements));

        // Apresentar ou ocultar item de menu
        // $menu = ['Dashboard', 'ListUsers', 'ListDepartments', 'ListPositions', 'ListAccessLevels', 'ListPackages', 'ListGroupsPages', 'ListPages'];
        // $menuPermission = new MenuPermissionUserRepository();
        // $this->data['menuPermission'] = $menuPermission->menuPermission($menu);

        // Carregar a VIEW com os dados
        $loadView = new LoadViewService("adms/Views/documents/list", $this->data);
        $loadView->loadView();
    }
}
