<?php
require 'vendor/autoload.php';

echo "=== TESTE DE CARREGAMENTO DOS CONTROLLERS TIA ===\n\n";

// Testar controller Dashboard
echo "1. Testando LgpdTiaDashboard...\n";
try {
    $class = "\\App\\adms\\Controllers\\lgpd\\LgpdTiaDashboard";
    if (class_exists($class)) {
        echo "   ✅ Classe existe\n";
        
        $controller = new $class();
        echo "   ✅ Controller instanciado\n";
        
        if (method_exists($controller, 'index')) {
            echo "   ✅ Método index() existe\n";
        } else {
            echo "   ❌ Método index() NÃO existe\n";
        }
    } else {
        echo "   ❌ Classe NÃO existe\n";
    }
} catch (Exception $e) {
    echo "   ❌ Erro: " . $e->getMessage() . "\n";
}

echo "\n";

// Testar controller Template
echo "2. Testando LgpdTiaTemplate...\n";
try {
    $class = "\\App\\adms\\Controllers\\lgpd\\LgpdTiaTemplate";
    if (class_exists($class)) {
        echo "   ✅ Classe existe\n";
        
        $controller = new $class();
        echo "   ✅ Controller instanciado\n";
        
        if (method_exists($controller, 'index')) {
            echo "   ✅ Método index() existe\n";
        } else {
            echo "   ❌ Método index() NÃO existe\n";
        }
    } else {
        echo "   ❌ Classe NÃO existe\n";
    }
} catch (Exception $e) {
    echo "   ❌ Erro: " . $e->getMessage() . "\n";
}

echo "\n";

// Testar controller Export PDF
echo "3. Testando LgpdTiaExportPdf...\n";
try {
    $class = "\\App\\adms\\Controllers\\lgpd\\LgpdTiaExportPdf";
    if (class_exists($class)) {
        echo "   ✅ Classe existe\n";
        
        $controller = new $class();
        echo "   ✅ Controller instanciado\n";
        
        if (method_exists($controller, 'index')) {
            echo "   ✅ Método index() existe\n";
        } else {
            echo "   ❌ Método index() NÃO existe\n";
        }
        
        if (method_exists($controller, 'exportTia')) {
            echo "   ✅ Método exportTia() existe\n";
        } else {
            echo "   ❌ Método exportTia() NÃO existe\n";
        }
        
        if (method_exists($controller, 'exportTiaList')) {
            echo "   ✅ Método exportTiaList() existe\n";
        } else {
            echo "   ❌ Método exportTiaList() NÃO existe\n";
        }
    } else {
        echo "   ❌ Classe NÃO existe\n";
    }
} catch (Exception $e) {
    echo "   ❌ Erro: " . $e->getMessage() . "\n";
}

echo "\n=== TESTE CONCLUÍDO ===\n";
