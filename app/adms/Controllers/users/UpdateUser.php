<?php

namespace App\adms\Controllers\users;

use App\adms\Controllers\Services\PageLayoutService;
use App\adms\Controllers\Services\Validation\ValidationUserRakitService;
use App\adms\Helpers\CSRFHelper;
use App\adms\Helpers\GenerateLog;
use App\adms\Models\Repository\ButtonPermissionUserRepository;
use App\adms\Models\Repository\DepartmentsRepository;
use App\adms\Models\Repository\PositionsRepository;
use App\adms\Models\Repository\UsersRepository;
use App\adms\Views\Services\LoadViewService;

/**
 * Controller para editar usuário
 *
 * Esta classe é responsável por gerenciar a edição de informações de um usuário existente. Inclui a validação dos dados
 * do formulário, a atualização das informações do usuário no repositório e a renderização da visualização apropriada.
 * Caso haja algum problema, como um usuário não encontrado ou dados inválidos, mensagens de erro são exibidas e registradas.
 *
 * @package App\adms\Controllers\users
 * @author Rafael Mendes <raffaell_mendez@hotmail.com>
 */
class UpdateUser
{
    // /** @var array|null $dataForm Recebe os dados do FORMULARIO */
    // private array|null $dataForm;

    /** @var array|string|null $data Recebe os dados que devem ser enviados para a VIEW */
    private array|string|null $data = null;

    /**
     * Editar o usuário.
     *
     * Este método gerencia o processo de edição de um usuário. Recebe os dados do formulário, valida o CSRF token e
     * a existência do usuário, e chama o método adequado para editar o usuário ou carregar a visualização de edição.
     *
     * @param int|string $id ID do usuário a ser editado.
     * 
     * @return void
     */
    public function index(int|string $id): void
    {

        // Receber os dados do formulário
        $this->data['form'] = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        // Acessar o IF se existir o CSRF e for valido o CSRF
        if (isset($this->data['form']['csrf_token']) and CSRFHelper::validateCSRFToken('form_update_user', $this->data['form']['csrf_token'])) {

            // Chamar o método editar
            $this->editUser();
           

        } else {
            
            // Instanciar o Repository para recuperar o registro do banco de dados
            $viewUser = new UsersRepository();
            $this->data['form'] = $viewUser->getUser((int) $id);

            // Verificar se existe o registro no banco de dados
            if (!$this->data['form']) {

                // Chamar o método para salvar o log
                GenerateLog::generateLog("error", "Usuário não encontrado.", ['id' => (int) $id]);

                // Criar a mensagem de erro 
                $_SESSION['error'] = "Usuário não encontrado.";

                // Redirecionar o usuário para página listar
                header("Location: {$_ENV['URL_ADM']}list-users");
                return;
            }

            // Chamar método carregar a view
            $this->viewUser();
        }
    }

    /**
     * Carregar a visualização para edição do usuário.
     *
     * Este método define o título da página e carrega a visualização de edição do usuário com os dados necessários.
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
            'title_head' => 'Editar Usuário',
            'menu' => 'list-users',
            'buttonPermission' => ['ListUsers', 'ViewUser'],
        ];
        
        $pageLayoutService = new PageLayoutService();
        $pageLayoutService->configurePageElements($pageElements);
        $this->data = array_merge($this->data, $pageLayoutService->configurePageElements($pageElements));

        // Carregar a VIEW
        $loadView = new LoadViewService("adms/Views/users/update", $this->data);
        $loadView->loadView();
    }

   /**
     * Editar o usuário.
     *
     * Este método valida os dados do formulário, atualiza as informações do usuário no repositório e lida com o resultado
     * da operação. Se a atualização for bem-sucedida, o usuário é redirecionado para a página de visualização do usuário.
     * Caso contrário, uma mensagem de erro é exibida e a visualização de edição é recarregada.
     * 
     * @return void
     */
    private function editUser(): void 
    {
        // Instanciar a classe validar os dados do formulario
        $validationUser = new ValidationUserRakitService();
        $this->data['errors'] = $validationUser->validate($this->data['form']);

        // Acessa o IF quando existir campo com dados incorretos
        if (!empty($this->data['errors'])) {
            // Chamar método carregar a view
            $this->viewUser();
            return;
        }

        // Instanciar Repository para editar o usuário
        $userUpdate = new UsersRepository();
        $result = $userUpdate->updateUser($this->data['form']);

        // Acessa o IF se o repository retornou TRUE
        if($result){
            // Criar a mensagem de sucesso
            $_SESSION['success'] = "Usuário editado com suscesso!";

            // Redirecionar o usuário para a pagina view - visualizar usuario
            header("Location: {$_ENV['URL_ADM']}view-user/{$this->data['form']['id']}");
            return;
        }else {
            // Criar a mensagem de erro
            $this->data['errors'][] = "Usuário não editado!";

            // Chamar método carregar a view
            $this->viewUser();
        }

    }
}
