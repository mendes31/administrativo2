<?php

namespace App\adms\Controllers\receive;

use App\adms\Models\Repository\PaymentsRepository;

class ClearBusyReceive
{
    public function index(int $id): void
    {

        
        if (!(int) $id) {
            $_SESSION['error'] = "ID inválido para limpar bloqueio!";
            header("Location: {$_ENV['URL_ADM']}list-payments");
            return;
        }

        $payRepo = new PaymentsRepository();
        $payRepo->clearBusy((int) $id);

        $_SESSION['success'] = "Bloqueio liberado com sucesso.";
        header("Location: {$_ENV['URL_ADM']}list-payments"); // ou volte pra listagem
    }
}
