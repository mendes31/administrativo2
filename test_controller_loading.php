<?php
require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->load();

echo "Testando carregamento de controllers...\n\n";

// Testar se o controller principal existe
if (class_exists('\App\adms\Controllers\lgpd\LgpdTiaExportPdf')) {
    echo "✓ LgpdTiaExportPdf encontrado\n";
    
    $controller = new \App\adms\Controllers\lgpd\LgpdTiaExportPdf();
    if (method_exists($controller, 'exportTia')) {
        echo "✓ Método exportTia encontrado\n";
    } else {
        echo "✗ Método exportTia NÃO encontrado\n";
    }
    
    if (method_exists($controller, 'exportTiaList')) {
        echo "✓ Método exportTiaList encontrado\n";
    } else {
        echo "✗ Método exportTiaList NÃO encontrado\n";
    }
} else {
    echo "✗ LgpdTiaExportPdf NÃO encontrado\n";
}

echo "\n";

// Testar se o controller wrapper individual existe
if (class_exists('\App\adms\Controllers\lgpd\LgpdTiaExportPdfView')) {
    echo "✓ LgpdTiaExportPdfView encontrado\n";
    
    $controller = new \App\adms\Controllers\lgpd\LgpdTiaExportPdfView();
    if (method_exists($controller, 'index')) {
        echo "✓ Método index encontrado\n";
        
        // Verificar assinatura do método
        $reflection = new ReflectionMethod($controller, 'index');
        $params = $reflection->getParameters();
        echo "✓ Parâmetros do método index: " . count($params) . "\n";
        foreach ($params as $param) {
            echo "  - " . $param->getType() . " \$" . $param->getName() . "\n";
        }
    } else {
        echo "✗ Método index NÃO encontrado\n";
    }
} else {
    echo "✗ LgpdTiaExportPdfView NÃO encontrado\n";
}

echo "\n";

// Testar se o controller wrapper da lista existe
if (class_exists('\App\adms\Controllers\lgpd\LgpdTiaExportPdfList')) {
    echo "✓ LgpdTiaExportPdfList encontrado\n";
    
    $controller = new \App\adms\Controllers\lgpd\LgpdTiaExportPdfList();
    if (method_exists($controller, 'index')) {
        echo "✓ Método index encontrado\n";
    } else {
        echo "✗ Método index NÃO encontrado\n";
    }
} else {
    echo "✗ LgpdTiaExportPdfList NÃO encontrado\n";
}

echo "\n";

// Testar se o repositório existe
if (class_exists('\App\adms\Models\Repository\LgpdTiaRepository')) {
    echo "✓ LgpdTiaRepository encontrado\n";
} else {
    echo "✗ LgpdTiaRepository NÃO encontrado\n";
}
