<?php

namespace App\adms\Controllers\login;

use App\adms\Models\Repository\LogsRepository;
use App\adms\Models\Repository\LogAcessosRepository;

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
        // Registrar log de acesso (logout)
        if (isset($_SESSION['user_id'])) {
            $logAcessosRepo = new \App\adms\Models\Repository\LogAcessosRepository();
            $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
            $logAcessosRepo->registrarAcesso($_SESSION['user_id'], 'LOGOUT', $ip, $userAgent);
        }

        // Eliminar os valores da sessão
        unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_email'], $_SESSION['user_username']);

        // Criar a mensagem de sucesso
        $_SESSION['success'] = "Usuário deslogado com suscesso!";

        // Redirecionar o usuário para a pagina listar
        header("Location: {$_ENV['URL_ADM']}login");
    }
}
