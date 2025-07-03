<?php

namespace App\adms\Controllers\Services;

use App\adms\Helpers\GenerateLog;
use App\adms\Models\Repository\LoginRepository;

class ValidationUserLogin
{
    /**
     * Retorna array de dados do usuário autenticado ou false em caso de falha
     * @param array $data
     * @return array|false
     */
    public function validationUserLogin(array $data)
    {
        // Instanciar o Repository para validar o usuário no banco de dados
        $login = new LoginRepository();
        $result = $login->getUser((string) $data['username']);

        // Verificar se existe o registro no banco de dados
        if (!$result) {

            // Chamar o método para salvar o log
            GenerateLog::generateLog("error", "Usuário incorreto.", ['username' => $data['username']]);

            // Criar a mensagem de erro 
            $_SESSION['error'] = "Usuário ou senha incorreta.";

            return false;
        }
        if (password_verify($data['password'], $result['password'])) {

            // Extrair o array para imprimir o elemento do array através do nome
            extract($result);

            // Salvar os dados do usuário na sessão
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_username'] = $username;
            $_SESSION['user_image'] = $image;
            $_SESSION['user_department'] = $dep_name;
            $_SESSION['user_position'] = $pos_name;

            return $result;
        }

        // Chamar o método para salvar o log
        GenerateLog::generateLog("error", "Senha incorreta.", ['username' => $data['password']]);

        // Criar a mensagem de erro 
        $_SESSION['error'] = "Usuário ou senha incorreta.";

        return false;
    }
}
