<?php

namespace App\adms\Controllers\customer;

use App\adms\Controllers\Services\PageLayoutService;
use App\adms\Controllers\Services\PaginationService;
use App\adms\Models\Repository\CustomerRepository;
use App\adms\Models\Repository\FrequencyRepository;
use App\adms\Views\Services\LoadViewService;

/**
 * Controller para listar Clientes
 *
 * Esta classe é responsável por recuperar e exibir uma lista de Clientes no sistema. Utiliza um repositório
 * para obter dados dos Clientes e um serviço de paginação para gerenciar a navegação entre páginas de resultados.
 * Em seguida, carrega a visualização correspondente com os dados recuperados.
 * 
 * @package App\adms\Controllers\customer
 * @author Rafael Mendes
 */
class ListCustomers
{
    /** @var array|string|null $data Dados que devem ser enviados para a VIEW */
    private array|string|null $data = null;

    /** @var int $limitResult Limite de registros por página */
    private int $limitResult = 10000; // Ajuste conforme necessário

    /**
     * Recuperar e listar Clientes com paginação.
     * 
     * Este método recupera os Clientes a partir do repositório de Clientes com base na página atual e no limite
     * de registros por página. Gera os dados de paginação e carrega a visualização para exibir a lista de Clientes.
     * 
     * @param string|int $page Página atual para a exibição dos resultados. O padrão é 1.
     * 
     * @return void
     */
    public function index(string|int $page = 1): void
    {
        // Instanciar o Repository para recuperar os registros do banco de dados
        $listCustomers = new CustomerRepository();

        // Recuperar os Clientes para a página atual
        $this->data['customers'] = $listCustomers->getAllCustomers((int) $page, (int) $this->limitResult);

        // Gerar dados de paginação
        $this->data['pagination'] = PaginationService::generatePagination(
            (int) $listCustomers->getAmountCustomers(), 
            (int) $this->limitResult, 
            (int) $page, 
            'list-customers'
        );

        // Definir o título da página
        // Ativar o item de menu
        // Apresentar ou ocultar botão 
        $pageElements = [
            'title_head' => 'Listar Clientes',
            'menu' => 'list-customers',
            'buttonPermission' => ['CreateCustomer', 'ViewCustomer', 'UpdateCustomer', 'DeleteCustomer'],
        ];
        $pageLayoutService = new PageLayoutService();
        $pageLayoutService->configurePageElements($pageElements);
        $this->data = array_merge($this->data, $pageLayoutService->configurePageElements($pageElements));

        // Carregar a VIEW com os dados
        $loadView = new LoadViewService("adms/Views/customer/list", $this->data);
        $loadView->loadView();
    }
}
