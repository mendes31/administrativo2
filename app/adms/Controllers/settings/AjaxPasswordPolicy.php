<?php

namespace App\adms\Controllers\settings;

use App\adms\Controllers\Services\ValidationUserLogin;
use App\adms\Helpers\GenerateLog;
use App\adms\Models\Repository\AdmsPasswordPolicyRepository;

class AjaxPasswordPolicy
{
    public function validatePassword(): void
    {
        // Recebe a senha enviada via AJAX
        $input = json_decode(file_get_contents('php://input'), true);
        $senha = $input['senha'] ?? '';

        // Pega o usuário logado da sessão
        $username = $_SESSION['user_username'] ?? null;
        if (!$username) {
            GenerateLog::generateLog('debug', 'Validação de senha na modal - usuário não autenticado', [
                'session' => $_SESSION,
                'input' => $input,
                'username' => $username
            ]);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário não autenticado!']);
            exit;
        }

        // Usa o serviço de validação
        $validador = new ValidationUserLogin();
        $ok = $validador->validationUserLogin([
            'username' => $username,
            'password' => $senha
        ]);
        GenerateLog::generateLog('debug', 'Validação de senha na modal', [
            'session' => $_SESSION,
            'input' => $input,
            'username' => $username,
            'resultado' => $ok ? 'Senha OK' : 'Senha incorreta'
        ]);

        if ($ok) {
            echo json_encode(['sucesso' => true]);
        } else {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Senha incorreta!']);
        }
        exit;
    }

    public function index(): void
    {
        header('Content-Type: application/json');
        $form = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if (!$form) {
            echo json_encode(['success' => false, 'message' => 'Dados não recebidos.']);
            return;
        }
        $repo = new AdmsPasswordPolicyRepository();
        // Se não veio id, buscar o id do registro existente
        if (empty($form['id'])) {
            $policy = $repo->getPolicy();
            if ($policy && isset($policy->id)) {
                $form['id'] = $policy->id;
            } else {
                echo json_encode(['success' => false, 'message' => 'Registro de política de senha não encontrado.']);
                return;
            }
        }
        $nivel = $form['nivel_seguranca'] ?? 'Baixo';
        $exemplos = [
            'Baixo' => 'abcd',
            'Médio' => 'Abcde1',
            'Elevado' => 'Abcde12@',
            'Customizado' => $form['exemplo_senha'] ?? ''
        ];
        $data = [
            'id' => $form['id'],
            'nivel_seguranca' => $nivel,
            'vencimento_dias' => $form['vencimento_dias'] ?? 90,
            'comprimento_minimo' => $form['comprimento_minimo'] ?? 8,
            'min_maiusculas' => $form['min_maiusculas'] ?? 0,
            'min_minusculas' => $form['min_minusculas'] ?? 0,
            'min_digitos' => $form['min_digitos'] ?? 0,
            'min_nao_alfanumericos' => $form['min_nao_alfanumericos'] ?? 0,
            'historico_senhas' => $form['historico_senhas'] ?? 5,
            'tentativas_bloqueio' => $form['tentativas_bloqueio'] ?? 5,
            'exemplo_senha' => $nivel !== 'Customizado' ? $exemplos[$nivel] : $exemplos['Customizado'],
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $result = $repo->update($data);
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Política de senha atualizada com sucesso!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao atualizar política de senha.']);
        }
    }
} 