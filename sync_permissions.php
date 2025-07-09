<?php
require 'vendor/autoload.php';

// Carregar variáveis de ambiente
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Iniciar sessão para compatibilidade com o sistema
session_start();

try {
    // Instanciar o serviço de sincronização
    $accessLevelPage = new App\adms\Controllers\Services\AccessLevelPageSyncService();
    $result = $accessLevelPage->accessLevelPageSync();

    if ($result) {
        echo "✅ Sincronização entre níveis de acesso e páginas realizada com sucesso!\n";
        echo "📋 As páginas de Filiais foram adicionadas a todos os níveis de acesso (sem permissão).\n";
        echo "🔓 Agora você pode liberar as permissões manualmente na interface administrativa.\n";
    } else {
        echo "❌ Erro na sincronização entre níveis de acesso e páginas!\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
?> 