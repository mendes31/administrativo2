<?php

namespace App\adms\Controllers\settings;

use App\adms\Models\Repository\LoginRepository;

class AjaxSimplePasswordValidate
{
    public function index(): void
    {
        // Log simples para debug
        file_put_contents('logs/teste_ajax.txt', date('Y-m-d H:i:s') . " - Entrou no validate AJAX simples\n", FILE_APPEND);

        $input = json_decode(file_get_contents('php://input'), true);
        $senha = $input['senha'] ?? '';
        $username = $_SESSION['user_username'] ?? null;

        if (!$username) {
            file_put_contents('logs/teste_ajax.txt', date('Y-m-d H:i:s') . " - Usuário não autenticado\n", FILE_APPEND);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário não autenticado!']);
            exit;
        }

        $repo = new LoginRepository();
        $user = $repo->getUser($username);
        if (!$user) {
            file_put_contents('logs/teste_ajax.txt', date('Y-m-d H:i:s') . " - Usuário não encontrado no banco\n", FILE_APPEND);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário não encontrado!']);
            exit;
        }

        if (password_verify($senha, $user['password'])) {
            file_put_contents('logs/teste_ajax.txt', date('Y-m-d H:i:s') . " - Senha OK\n", FILE_APPEND);
            echo json_encode(['sucesso' => true]);
        } else {
            file_put_contents('logs/teste_ajax.txt', date('Y-m-d H:i:s') . " - Senha incorreta\n", FILE_APPEND);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Senha incorreta!']);
        }
        exit;
    }
} 