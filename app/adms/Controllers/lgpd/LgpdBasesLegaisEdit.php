<?php

namespace App\adms\Controllers\lgpd;

use App\adms\Controllers\Services\PageLayoutService;
use App\adms\Models\Repository\LgpdBasesLegaisRepository;
use App\adms\Views\Services\LoadViewService;

class LgpdBasesLegaisEdit
{
    private array|string|null $data = null;

    public function index(): void
    {
        $id = $_GET['id'] ?? null;
        
        if (!$id || !is_numeric($id)) {
            $_SESSION['msg'] = "Erro: ID inválido!";
            $_SESSION['msg_type'] = "danger";
            header("Location: " . $_ENV['URL_ADM'] . "lgpd-bases-legais");
            exit;
        }

        $this->data = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->update($id);
        } else {
            $this->showForm($id);
        }
    }

    private function update(int $id): void
    {
        $data = [
            'base_legal' => $_POST['base_legal'] ?? '',
            'descricao' => $_POST['descricao'] ?? '',
            'exemplo' => $_POST['exemplo'] ?? '',
            'status' => $_POST['status'] ?? 'Ativo'
        ];

        // Validação básica
        if (empty($data['base_legal'])) {
            $_SESSION['msg'] = "Erro: Base Legal é obrigatória!";
            $_SESSION['msg_type'] = "danger";
            $this->data['formData'] = $data;
            $this->showForm($id);
            return;
        }

        $repository = new LgpdBasesLegaisRepository();
        $result = $repository->update($this->data['form']);

        if ($result) {
            $_SESSION['msg'] = "Base Legal editada com sucesso!";
            $_SESSION['msg_type'] = "success";
            header("Location: " . $_ENV['URL_ADM'] . "lgpd-bases-legais");
            exit;
        } else {
            $_SESSION['msg'] = "Erro: Base Legal não foi editada!";
            $_SESSION['msg_type'] = "danger";
            $this->data['formData'] = $data;
            $this->showForm($id);
        }
    }

    private function showForm(int $id): void
    {
        $repository = new LgpdBasesLegaisRepository();
        $registro = $repository->getById($id);

        if (!$registro) {
            $_SESSION['msg'] = "Erro: Base Legal não encontrada!";
            $_SESSION['msg_type'] = "danger";
            header("Location: " . $_ENV['URL_ADM'] . "lgpd-bases-legais");
            exit;
        }

        $this->data['registro'] = $registro;

        $pageElements = [
            'title_head' => 'Editar Base Legal LGPD',
            'menu' => 'EditLgpdBasesLegais',
            'buttonPermission' => ['EditLgpdBasesLegais'],
        ];
        $pageLayoutService = new PageLayoutService();
        $this->data = array_merge($this->data, $pageLayoutService->configurePageElements($pageElements));

        $loadView = new LoadViewService("adms/Views/lgpd/bases-legais/edit", $this->data);
        $loadView->loadView();
    }
} 