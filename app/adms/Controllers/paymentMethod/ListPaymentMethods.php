<?php

namespace App\adms\Controllers\paymentMethod;

use App\adms\Controllers\Services\PageLayoutService;
use App\adms\Controllers\Services\PaginationService;
use App\adms\Models\Repository\PaymentMethodsRepository;
use App\adms\Models\Repository\PositionsRepository;
use App\adms\Views\Services\LoadViewService;

/**
 * Controller para listar Formas de Pagamento
 *
 * Esta classe é responsável por recuperar e exibir uma lista de Formas de pagamento no sistema. Utiliza um repositório
 * para obter dados dos Formas de pagamento e um serviço de paginação para gerenciar a navegação entre páginas de resultados.
 * Em seguida, carrega a visualização correspondente com os dados recuperados.
 * 
 * @package App\adms\Controllers\paymentMethod
 * @author Rafael Mendes
 */
class ListPaymentMethods
{
    /** @var array|string|null $data Dados que devem ser enviados para a VIEW */
    private array|string|null $data = null;

    /** @var int $limitResult Limite de registros por página */
    private int $limitResult = 1000; // Ajuste conforme necessário

    /**
     * Recuperar e listar Formas de pagamento com paginação.
     * 
     * Este método recupera os Formas de pagamento a partir do repositório de Formas de pagamento com base na página atual e no limite
     * de registros por página. Gera os dados de paginação e carrega a visualização para exibir a lista de Formas de pagamento.
     * 
     * @param string|int $page Página atual para a exibição dos resultados. O padrão é 1.
     * 
     * @return void
     */
    public function index(string|int $page = 1): void
    {
        // Instanciar o Repository para recuperar os registros do banco de dados
        $listPaymentMethods = new PaymentMethodsRepository();

        // Recuperar os Formas de pagamento para a página atual
        $this->data['paymentMethods'] = $listPaymentMethods->getAllPaymentMethods((int) $page, (int) $this->limitResult);

        // Gerar dados de paginação
        $this->data['pagination'] = PaginationService::generatePagination(
            (int) $listPaymentMethods->getAmountPaymentMethods(), 
            (int) $this->limitResult, 
            (int) $page, 
            'list-payment-methods'
        );

        // Definir o título da página
        // Ativar o item de menu
        // Apresentar ou ocultar botão 
        $pageElements = [
            'title_head' => 'Listar Formas de pagamento',
            'menu' => 'list-payment-methods',
            'buttonPermission' => ['CreatePaymentMethod', 'ViewPaymentMethod', 'UpdatePaymentMethod', 'DeletePaymentMethod'],
        ];
        $pageLayoutService = new PageLayoutService();
        $pageLayoutService->configurePageElements($pageElements);
        $this->data = array_merge($this->data, $pageLayoutService->configurePageElements($pageElements));

        // Carregar a VIEW com os dados
        $loadView = new LoadViewService("adms/Views/paymentMethod/list", $this->data);
        $loadView->loadView();
    }
}
