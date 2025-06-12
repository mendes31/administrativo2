<?php

namespace App\adms\Controllers\banks;

use App\adms\Controllers\Services\PageLayoutService;
use App\adms\Controllers\Services\PaginationService;
use App\adms\Models\Repository\MovBetweenAccountsRepository;
use App\adms\Views\Services\LoadViewService;

/**
 * Controller responsável por listar as transferências entre contas.
 *
 * @package App\adms\Controllers
 * @author Rafael Mendes
 */
class ListMovBetweenAccounts
{
    /** @var array|string|null $data Dados que devem ser enviados para a VIEW */
    private array|string|null $data = null;

    /** @var int $limitResult Limite de registros por página */
    private int $limitResult = 10; // Ajuste conforme necessário

    /**
     * Recuperar e listar Bancos com paginação.
     * 
     * Este método recupera os Bancos a partir do repositório de Bancos com base na página atual e no limite
     * de registros por página. Gera os dados de paginação e carrega a visualização para exibir a lista de Bancos.
     * 
     * @param string|int $page Página atual para a exibição dos resultados. O padrão é 1.
     * 
     * @return void
     */
    public function index(string|int $page = 1): void
    {
        // Repositório para buscar transferências
        $listMovBetweenAccounts = new MovBetweenAccountsRepository();

        // Recuperar os Bancos para a página atual
        $this->data['movBetweenAccounts'] = $listMovBetweenAccounts->getAllMovBetweenAccounts((int) $page, (int) $this->limitResult);
        
                // Gerar dados de paginação
                $this->data['pagination'] = PaginationService::generatePagination(
                    (int) $listMovBetweenAccounts->getAmountMovBetweenAccounts(), 
                    (int) $this->limitResult, 
                    (int) $page, 
                    'list-mov-between-accounts'
                );
        // Definir o título da página
        // Ativar o item de menu
        // Apresentar ou ocultar botão 
        $pageElements = [
            'title_head' => 'Listar Transferências entre Contas',
            'menu' => 'list-mov-between-accounts',
            'buttonPermission' => ['MovBetweenAccounts', 'ViewMovBetweenAccounts','ViewTransfer', 'UpdateMovBetweenAccounts', 'DeleteMovBetweenAccounts'],
        ];
        $pageLayoutService = new PageLayoutService();
        $pageLayoutService->configurePageElements($pageElements);
        $this->data = array_merge($this->data, $pageLayoutService->configurePageElements($pageElements));

        // Carregar a VIEW com os dados
        $loadView = new LoadViewService("adms/Views/banks/listMovBetweenAccounts", $this->data);
        $loadView->loadView();
    }
}
