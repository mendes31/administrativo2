<?php

namespace App\adms\Controllers\settings;

use App\adms\Views\Services\LoadViewService;
use App\adms\Controllers\Services\PageLayoutService;

class EmailConfig
{
    public function index(): void
    {
        $data = [
            'title_head' => 'Configuração de E-mail',
            'menu' => 'email-config',
            'buttonPermission' => ['EmailConfig'],
            'email_config' => $this->getCurrentConfig(),
        ];
        $pageLayout = new PageLayoutService();
        $data = array_merge($data, $pageLayout->configurePageElements($data));
        $loadView = new LoadViewService('adms/Views/settings/emailConfig', $data);
        $loadView->loadView();
    }

    public function save(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $_ENV['URL_ADM'] . 'email-config');
            exit;
        }
        $config = [
            'MAIL_HOST' => $_POST['MAIL_HOST'] ?? '',
            'MAIL_USERNAME' => $_POST['MAIL_USERNAME'] ?? '',
            'MAIL_PASSWORD' => $_POST['MAIL_PASSWORD'] ?? '',
            'MAIL_PORT' => $_POST['MAIL_PORT'] ?? '',
            'MAIL_ENCRYPTI' => $_POST['MAIL_ENCRYPTI'] ?? '',
            'EMAIL_TI' => $_POST['EMAIL_TI'] ?? '',
            'NAME_EMAIL_TI' => $_POST['NAME_EMAIL_TI'] ?? '',
        ];
        $ok = $this->saveConfigToEnv($config);
        if ($ok) {
            $_SESSION['msg'] = 'Configurações de e-mail salvas com sucesso!';
            $_SESSION['msg_type'] = 'success';
        } else {
            $_SESSION['msg'] = 'Erro ao salvar configurações.';
            $_SESSION['msg_type'] = 'danger';
        }
        header('Location: ' . $_ENV['URL_ADM'] . 'email-config');
        exit;
    }

    private function getCurrentConfig(): array
    {
        return [
            'MAIL_HOST' => $_ENV['MAIL_HOST'] ?? '',
            'MAIL_USERNAME' => $_ENV['MAIL_USERNAME'] ?? '',
            'MAIL_PASSWORD' => $_ENV['MAIL_PASSWORD'] ?? '',
            'MAIL_PORT' => $_ENV['MAIL_PORT'] ?? '',
            'MAIL_ENCRYPTI' => $_ENV['MAIL_ENCRYPTI'] ?? '',
            'EMAIL_TI' => $_ENV['EMAIL_TI'] ?? '',
            'NAME_EMAIL_TI' => $_ENV['NAME_EMAIL_TI'] ?? '',
        ];
    }

    private function saveConfigToEnv(array $config): bool
    {
        $envPath = __DIR__ . '/../../../.env';
        if (!file_exists($envPath)) return false;
        $env = file_get_contents($envPath);
        foreach ($config as $key => $value) {
            $env = preg_replace('/^' . preg_quote($key, '/') . '=.*/m', $key . '=' . $value, $env);
        }
        return file_put_contents($envPath, $env) !== false;
    }
} 