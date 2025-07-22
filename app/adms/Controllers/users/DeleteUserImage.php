<?php

namespace App\adms\Controllers\users;

use App\adms\Helpers\CSRFHelper;
use App\adms\Helpers\GenerateLog;
use App\adms\Models\Repository\UsersRepository;

class DeleteUserImage
{
    private array|string|null $data = null;

    public function index(int|string $id): void
    {
        // Receber os dados do formulário
        $this->data['form'] = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        // Validar CSRF e ID
        if (!isset($this->data['form']['csrf_token']) || !CSRFHelper::validateCSRFToken('form_delete_user_image', $this->data['form']['csrf_token']) || !$id) {
            GenerateLog::generateLog("error", "Requisição inválida para deleção de imagem de usuário.", ['id' => $id]);
            $_SESSION['error'] = "Requisição inválida.";
            header("Location: {$_ENV['URL_ADM']}view-user/$id");
            return;
        }

        $userRepo = new UsersRepository();
        $user = $userRepo->getUser((int)$id);
        if (!$user) {
            GenerateLog::generateLog("error", "Usuário não encontrado ao tentar deletar imagem.", ['id' => $id]);
            $_SESSION['error'] = "Usuário não encontrado.";
            header("Location: {$_ENV['URL_ADM']}list-users");
            return;
        }

        // Remover arquivo físico se existir
        if (!empty($user['image'])) {
            $imgPath = 'public/adms/uploads/' . $user['image'];
            if (file_exists($imgPath)) {
                unlink($imgPath);
            }
        }

        // Limpar campo image no banco
        $userRepo->updateUser([
            'id' => $id,
            'image' => null,
            'name' => $user['name'],
            'email' => $user['email'],
            'username' => $user['username'],
            'user_department_id' => $user['user_department_id'],
            'user_position_id' => $user['user_position_id'],
        ]);

        GenerateLog::generateLog("info", "Imagem de usuário deletada com sucesso.", ['id' => $id]);
        $_SESSION['success'] = "Imagem removida com sucesso.";
        header("Location: {$_ENV['URL_ADM']}view-user/$id");
        return;
    }
} 