<?php

namespace App\adms\Controllers\frequency;

use App\adms\Controllers\Services\PageLayoutService;
use App\adms\Controllers\Services\PaginationService;
use App\adms\Models\Repository\FrequencyRepository;
use App\adms\Views\Services\LoadViewService;

/**
 * Controller para listar Frequências
 *
 * Esta classe é responsável por recuperar e exibir uma lista de Frequências no sistema. Utiliza um repositório
 * para obter dados dos Frequências e um serviço de paginação para gerenciar a navegação entre páginas de resultados.
 * Em seguida, carrega a visualização correspondente com os dados recuperados.
 * 
 * @package App\adms\Controllers\frequency
 * @author Rafael Mendes
 */
class ListFrequencies
{
    /** @var array|string|null $data Dados que devem ser enviados para a VIEW */
    private array|string|null $data = null;

    /** @var int $limitResult Limite de registros por página */
    private int $limitResult = 1000; // Ajuste conforme necessário

    /**
     * Recuperar e listar Frequências com paginação.
     * 
     * Este método recupera os Frequências a partir do repositório de Frequências com base na página atual e no limite
     * de registros por página. Gera os dados de paginação e carrega a visualização para exibir a lista de Frequências.
     * 
     * @param string|int $page Página atual para a exibição dos resultados. O padrão é 1.
     * 
     * @return void
     */
    public function index(string|int $page = 1): void
    {
        // Instanciar o Repository para recuperar os registros do banco de dados
        $listFrequencies = new FrequencyRepository();

        // Recuperar os Frequências para a página atual
        $this->data['frequencies'] = $listFrequencies->getAllFrequencies((int) $page, (int) $this->limitResult);

        // Gerar dados de paginação
        $this->data['pagination'] = PaginationService::generatePagination(
            (int) $listFrequencies->getAmountFrequencies(), 
            (int) $this->limitResult, 
            (int) $page, 
            'list-frequencies'
        );

        // Definir o título da página
        // Ativar o item de menu
        // Apresentar ou ocultar botão 
        $pageElements = [
            'title_head' => 'Listar Frequências',
            'menu' => 'list-frequencies',
            'buttonPermission' => ['CreateFrequency', 'ViewFrequency', 'UpdateFrequency', 'DeleteFrequency'],
        ];
        $pageLayoutService = new PageLayoutService();
        $this->data = array_merge($this->data, $pageLayoutService->configurePageElements($pageElements));

        // Carregar a VIEW com os dados
        $loadView = new LoadViewService("adms/Views/frequency/list", $this->data);
        $loadView->loadView();
    }
}
