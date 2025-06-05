<?php

namespace App\adms\Controllers\users;

use App\adms\Controllers\Services\PageLayoutService;
use App\adms\Controllers\Services\PaginationService;
use App\adms\Models\Repository\UsersRepository;
use App\adms\Views\Services\LoadViewService;

/**
 * Controller para listar usuários
 *
 * Esta classe é responsável por recuperar e exibir uma lista de usuários no sistema. Utiliza um repositório
 * para obter dados dos usuários e um serviço de paginação para gerenciar a navegação entre páginas de resultados.
 * Em seguida, carrega a visualização correspondente com os dados recuperados.
 * 
 * @package App\adms\Controllers\users
 * @author Rfael Mendes <raffaell_mendez@hotmail.com>
 */
class ListUsers
{
    /** @var array|string|null $data Recebe os dados que devem ser enviados para a VIEW */
    private array|string|null $data = null;

    /** @var int $limitResult Recebe a quantidade de registros que deve retornar do banco de dados */
    private int $limitResult = 10000; // Ajuste se necessário a quantidade de registro por pagina

    /**
     * Recuperar e listar usuários com paginação.
     * 
     * Este método recupera os usuários a partir do repositório de usuários com base na página atual e no limite
     * de registros por página. Gera os dados de paginação e carrega a visualização para exibir a lista de usuários.
     * 
     * @param string|int $page Página atual para a exibição dos resultados. O padrão é 1.
     * 
     * @return void
     */
    public function index(string|int $page = 1)
    {
        // Instanciar o Repository para recuperar os registros do banco de dados
        $listUsers = new UsersRepository();
        $this->data['users'] = $listUsers->getAllUsers((int) $page, (int) $this->limitResult);
        $this->data['pagination'] = PaginationService::generatePagination((int) $listUsers->getAmountUsers(), (int) $this->limitResult, (int) $page, 'list-users');
    
        // Definir o título da página
        // Ativar o item de menu
        // Apresentar ou ocultar botão 
        $pageElements = [
            'title_head' => 'Listar Usuários',
            'menu' => 'list-users',
            'buttonPermission' => ['CreateUser', 'ViewUser', 'UpdateUser', 'DeleteUser'],
        ];
        
        $pageLayoutService = new PageLayoutService();
        $pageLayoutService->configurePageElements($pageElements);
        $this->data = array_merge($this->data, $pageLayoutService->configurePageElements($pageElements));

        // Carregar a VIEW
        $loadView = new LoadViewService("adms/Views/users/list", $this->data);
        $loadView->loadView();
    }
}
