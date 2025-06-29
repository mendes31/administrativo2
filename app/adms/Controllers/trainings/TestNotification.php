<?php

namespace App\adms\Controllers\trainings;

use App\adms\Controllers\Services\NotificacaoTreinamentosService;
use App\adms\Controllers\Services\PageLayoutService;
use App\adms\Views\Services\LoadViewService;

class TestNotification
{
    public function index(): void
    {
        $data = [
            'title_head' => 'Teste de Notificações de Treinamentos',
            'menu' => 'gestao_treinamentos',
            'buttonPermission' => ['TestNotification'],
        ];

        // Adiciona dados de layout e permissões
        $pageLayout = new PageLayoutService();
        $data = $pageLayout->configurePageElements($data);

        // Carregar a view
        $loadView = new LoadViewService('adms/Views/trainings/testNotification', $data);
        $loadView->loadView();
    }

    public function sendNotification(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $_ENV['URL_ADM'] . 'test-notification');
            exit;
        }

        try {
            $notificacaoService = new NotificacaoTreinamentosService();
            $notificacaoService->notificarTreinamentosPendentes();
            
            $_SESSION['msg'] = "Notificações enviadas com sucesso!";
            $_SESSION['msg_type'] = "success";
        } catch (\Exception $e) {
            $_SESSION['msg'] = "Erro ao enviar notificações: " . $e->getMessage();
            $_SESSION['msg_type'] = "danger";
        }

        header('Location: ' . $_ENV['URL_ADM'] . 'test-notification');
        exit;
    }
} 