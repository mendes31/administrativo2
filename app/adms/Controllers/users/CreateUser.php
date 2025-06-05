<?php

namespace App\adms\Controllers\users;

use App\adms\Controllers\Services\PageLayoutService;
use App\adms\Controllers\Services\Validation\ValidationUserRakitService;
use App\adms\Helpers\CSRFHelper;
use App\adms\Models\Repository\ButtonPermissionUserRepository;
use App\adms\Models\Repository\DepartmentsRepository;
use App\adms\Models\Repository\PositionsRepository;
use App\adms\Models\Repository\UsersRepository;
use App\adms\Views\Services\LoadViewService;

/**
 * Controller para criação de usuário
 *
 * Esta classe é responsável pelo processo de criação de novos usuários. Ela lida com a recepção dos dados do
 * formulário, validação dos mesmos, e criação do usuário no sistema. Além disso, é responsável por carregar
 * a visualização apropriada com mensagens de sucesso ou erro.
 * 
 * @package App\adms\Controllers\users
 * @author Rafael Mendes <raffaell_mendez@hotmail.com>
 */
class CreateUser
{
    /** @var array|string|null $data Recebe os dados que devem ser enviados para a VIEW */
    private array|string|null $data = null;

    /**
     * Método principal que gerencia a criação do usuário.
     *
     * Este método é chamado para processar a criação de um novo usuário. Ele verifica a validade do token CSRF,
     * valida os dados do formulário e, se tudo estiver correto, cria o usuário. Caso contrário, carrega a
     * visualização de criação de usuário com mensagens de erro.
     * 
     * @return void
     */
    public function index(): void
    {
        // Receber os dados do formulário
        $this->data['form'] = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        // Verificar se o token CSRF é valido
        if (isset($this->data['form']['csrf_token']) and CSRFHelper::validateCSRFToken('form_create_user', $this->data['form']['csrf_token'])) {

            // Chamar o método para cadastrar o usuário 
            $this->addUser();
        } else {
            // Chamar método carregar a view de criação de usuário
            $this->viewUser();
        }
    }

    /**
     * Carregar a visualização de criação de usuário.
     * 
     * Este método configura os dados necessários e carrega a view para a criação de um novo usuário.
     * 
     * @return void
     */
    private function viewUser(): void
    {
        // Instanciar o repositório para recuperar os departamentos
        $listDepartments = new DepartmentsRepository();
        $this->data['listDepartments'] = $listDepartments->getAllDepartmentsSelect();

        // Instanciar o repositório para recuperar os cargos
        $listPositions = new PositionsRepository();
        $this->data['listPositions'] = $listPositions->getAllPositionsSelect();
 
        // Definir o título da página
        // Ativar o item de menu
        // Apresentar ou ocultar botão 
        $pageElements = [
            'title_head' => 'Cadastrar Usuários',
            'menu' => 'list-users',
            'buttonPermission' => ['ListUsers'],
        ];
        
        $pageLayoutService = new PageLayoutService();
        $pageLayoutService->configurePageElements($pageElements);
        $this->data = array_merge($this->data, $pageLayoutService->configurePageElements($pageElements));

        // Carregar a VIEW
        $loadView = new LoadViewService("adms/Views/users/create", $this->data);
        $loadView->loadView();
    }

    /**
     * Adicionar um novo usuário ao sistema.
     * 
     * Este método valida os dados do formulário usando a classe de validação `ValidationUserRakitService` e,
     * se não houver erros, cria o usuário no banco de dados usando o `UsersRepository`. Caso contrário, ele
     * recarrega a visualização de criação com mensagens de erro.
     * 
     * @return void
     */
    private function addUser(): void
    {
        // Instanciar a classe validar os dados do fromuláriose com Rakit
        $validationUser = new ValidationUserRakitService();
        $this->data['errors'] = $validationUser->validate($this->data['form']);

        // Acessa o IF quando existir campo com dados incorretos
        if (!empty($this->data['errors'])) {
            // Chamar método carregar a view
            $this->viewUser();
            return;
        }

        // Instanciar o Repository para Criar o usuário
        $userCreate = new UsersRepository();
        $result = $userCreate->createUser($this->data['form']);

        // Acessa o IF se o repository retornou TRUE
        if ($result) {

            // Criar a mensagem de sucesso
            $_SESSION['success'] = "Usuário cadastrado com suscesso!";

            // Redirecionar o usuário para a pagina listar
            header("Location: {$_ENV['URL_ADM']}view-user/$result");
            return;
        } else {
            // Criar a mensagem de erro
            // $_SESSION['error'] = "Usuário não cadastrado!";
            $this->data['errors'][] = "Usuário não cadastrado!";

            // Chamar método carregar a view
            $this->viewUser();
        }
    }
}
