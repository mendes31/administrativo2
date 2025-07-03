<?php

namespace App\adms\Controllers\settings;

use App\adms\Views\Services\LoadViewService;
use App\adms\Controllers\Services\PageLayoutService;
use App\adms\Models\Repository\AdmsPasswordPolicyRepository;

class PasswordPolicy
{
    public function index(): void
    {
        $repo = new AdmsPasswordPolicyRepository();
        $data = [
            'title_head' => 'Administração de Senhas',
            'menu' => 'password-policy',
            'buttonPermission' => ['PasswordPolicy'],
            'form' => $repo->getPolicy(),
        ];
        $pageLayout = new PageLayoutService();
        $data = array_merge($data, $pageLayout->configurePageElements($data));
        $loadView = new LoadViewService('adms/Views/settings/passwordPolicy', $data);
        $loadView->loadView();
    }
} 