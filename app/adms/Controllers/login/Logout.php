<?php

namespace App\adms\Controllers\login;

use App\adms\Models\Repository\LogsRepository;

class Logout
{

    public function index(): void
    {
        if ($_ENV['APP_LOGS'] == 'Sim') {
            $dataLogs = [
                'table_name' => 'adms_users',
                'action' => 'logout',
                'record_id' => $_SESSION['user_id'] ?? 0,
                'description' => 'logout',
            ];
            // Instanciar a classe validar  o usuário
            $insertLogs = new LogsRepository();
            $insertLogs->insertLogs($dataLogs);
        }

        // Eliminar os valores da sessão
        unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_email'], $_SESSION['user_username']);

        // Criar a mensagem de sucesso
        $_SESSION['success'] = "Usuário deslogado com suscesso!";

        // Redirecionar o usuário para a pagina listar
        header("Location: {$_ENV['URL_ADM']}login");
    }
}
