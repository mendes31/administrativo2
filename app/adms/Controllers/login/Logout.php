<?php

namespace App\adms\Controllers\login;

use App\adms\Models\Repository\LogsRepository;
use App\adms\Models\Repository\LogAcessosRepository;

class Logout
{

    public function index(): void
    {
        if (isset($_SESSION['user_id']) && isset($_SESSION['session_id'])) {
            $sessionRepo = new \App\adms\Models\Repository\AdmsSessionsRepository();
            $sessionRepo->invalidateSessionByUserIdAndSessionId($_SESSION['user_id'], $_SESSION['session_id']);
        }
        session_unset();
        session_destroy();
        header('Location: ' . $_ENV['URL_ADM'] . 'login');
        exit;
    }
}
