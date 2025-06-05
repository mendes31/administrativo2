<?php

namespace App\adms\Controllers\accountsPlan;

use App\adms\Controllers\Services\PageLayoutService;
use App\adms\Controllers\Services\PaginationService;
use App\adms\Models\Repository\AccountPlanRepository;
use App\adms\Views\Services\LoadViewService;

/**
 * Controller para listar Planos de Conta
 *
 * Esta classe é responsável por recuperar e exibir uma lista de Planos de Conta no sistema. Utiliza um repositório
 * para obter dados dos Planos de Conta e um serviço de paginação para gerenciar a navegação entre páginas de resultados.
 * Em seguida, carrega a visualização correspondente com os dados recuperados.
 * 
 * @package App\adms\Controllers\accountsPlan
 * @author Rafael Mendes
 */
class ListAccountsPlan
{
    /** @var array|string|null $data Dados que devem ser enviados para a VIEW */
    private array|string|null $data = null;

    /** @var int $limitResult Limite de registros por página */
    private int $limitResult = 1000; // Ajuste conforme necessário

    /**
     * Recuperar e listar Planos de Conta com paginação.
     * 
     * Este método recupera os Planos de Conta a partir do repositório de Planos de Conta com base na página atual e no limite
     * de registros por página. Gera os dados de paginação e carrega a visualização para exibir a lista de Planos de Conta.
     * 
     * @param string|int $page Página atual para a exibição dos resultados. O padrão é 1.
     * 
     * @return void
     */
    public function index(string|int $page = 1): void
    {
        // Instanciar o Repository para recuperar os registros do banco de dados
        $listAccountPlan = new AccountPlanRepository();

        // Recuperar os Planos de Conta para a página atual
        $this->data['accountsPlan'] = $listAccountPlan->getAllAccountsPlan((int) $page, (int) $this->limitResult);

        // Gerar dados de paginação
        $this->data['pagination'] = PaginationService::generatePagination(
            (int) $listAccountPlan->getAmountAccountsPlan(), 
            (int) $this->limitResult, 
            (int) $page, 
            'list-accounts-plan'
        );

        // Definir o título da página
        // Ativar o item de menu
        // Apresentar ou ocultar botão 
        $pageElements = [
            'title_head' => 'Listar Planos de Conta',
            'menu' => 'list-accounts-plan',
            'buttonPermission' => ['CreateAccountPlan', 'ViewAccountPlan', 'UpdateAccountPlan', 'DeleteAccountPlan'],
        ];
        $pageLayoutService = new PageLayoutService();
        $pageLayoutService->configurePageElements($pageElements);
        $this->data = array_merge($this->data, $pageLayoutService->configurePageElements($pageElements));

        // Carregar a VIEW com os dados
        $loadView = new LoadViewService("adms/Views/accountsPlan/list", $this->data);
        $loadView->loadView();
    }
}
