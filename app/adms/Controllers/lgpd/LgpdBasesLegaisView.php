<?php

namespace App\adms\Controllers\lgpd;

use App\adms\Controllers\Services\PageLayoutService;
use App\adms\Models\Repository\LgpdBasesLegaisRepository;
use App\adms\Views\Services\LoadViewService;

class LgpdBasesLegaisView
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
            'title_head' => 'Visualizar Base Legal LGPD',
            'menu' => 'ViewLgpdBasesLegais',
            'buttonPermission' => ['ViewLgpdBasesLegais'],
        ];
        $pageLayoutService = new PageLayoutService();
        $this->data = array_merge($this->data, $pageLayoutService->configurePageElements($pageElements));

        $loadView = new LoadViewService("adms/Views/lgpd/bases-legais/view", $this->data);
        $loadView->loadView();
    }
} 