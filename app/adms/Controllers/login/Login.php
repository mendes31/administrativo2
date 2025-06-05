<?php

namespace App\adms\Controllers\login;

use App\adms\Controllers\Services\Validation\ValidationLoginService;
use App\adms\Controllers\Services\ValidationUserLogin;
use App\adms\Helpers\CSRFHelper;
use App\adms\Models\Repository\LogsRepository;
use App\adms\Views\Services\LoadViewService;

/**
 * Controller login
 * 
 * @author Rafael Mendes <raffaell_mendez@hotmail.com>
 */
class Login
{
    /** @var array|string|null $data Recebe os dados que devem ser enviados para a VIEW */
    private array|string|null $data = null;
    /**
     * Pagian login
     * 
     * @return void
     */
    public function index(): void
    {
        // Receber os dados do formulário
        $this->data['form'] = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        // Verificar se o token CSRF é valido
        if (isset($this->data['form']['csrf_token']) and CSRFHelper::validateCSRFToken('form_login', $this->data['form']['csrf_token'])) {

            // Chamar o método Login
             $this->login();

        } else {
            // Chamar método para carregar a viewLogin
            $this->viewLogin();
        }
        
    }

    /**
     * Carregar a visualização de login.
     * 
     * Este método configura os dados necessários e carrega a view para login.
     * 
     * @return void
     */
    private function viewLogin(): void
    {
        // Criar o título da página
        $this->data['title_head'] =  "Login";

        // Carregar a VIEW
        $loadView = new LoadViewService("adms/Views/login/login", $this->data);
        $loadView->loadViewLogin();
    }

    private function login(): void
    {
        // Instanciar a classe validar os dados do fromulário
        $validationLogin = new ValidationLoginService();
        $this->data['errors'] = $validationLogin->validate($this->data['form']);

        // Acessa o IF quando existir campo com dados incorretos
        if (!empty($this->data['errors'])) {
            // Chamar método carregar a view
            $this->viewLogin();
            return;
        }

        // Instanciar a classe validar  o usuário
        $validationUserLogin = new ValidationUserLogin();
        $result = $validationUserLogin->validationUserLogin($this->data['form']);

        if($result){

            if($_ENV['APP_LOGS'] == 'Sim'){
            $dataLogs = [
                'table_name' => 'adms_users',
                'action' => 'login',
                'record_id' => 0,
                'description' => 'login',

            ];
            // Instanciar a classe validar  o usuário
            $insertLogs = new LogsRepository();
            $insertLogs->insertLogs($dataLogs);
            }

            // Redirecionar o usuário para página listar
            header("Location: {$_ENV['URL_ADM']}dashboard");

        } else {
            // Chamar método para carregar a viewLogin
            $this->viewLogin();

            return;
        }
    }

}
