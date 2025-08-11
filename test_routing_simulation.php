<?php
require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->load();

echo "Simulando sistema de roteamento...\n\n";

// Simular a URL que está sendo acessada
$url = 'lgpd-tia-export-pdf';
$urlParameter = '1';

echo "URL: $url\n";
echo "Parâmetro: $urlParameter\n\n";

// Simular o processo de conversão de slug para PascalCase
function slugController($slug) {
    return str_replace('-', '', ucwords($slug, '-'));
}

$urlController = slugController($url);
echo "Controller convertido: $urlController\n\n";

// Verificar se a classe existe
if (class_exists("\\App\\adms\\Controllers\\lgpd\\$urlController")) {
    echo "✓ Classe encontrada: \\App\\adms\\Controllers\\lgpd\\$urlController\n";
    
    $controllerClass = "\\App\\adms\\Controllers\\lgpd\\$urlController";
    $controller = new $controllerClass();
    
    // Verificar se o método index existe
    if (method_exists($controller, 'index')) {
        echo "✓ Método index encontrado\n";
        
        // Verificar assinatura do método
        $reflection = new ReflectionMethod($controller, 'index');
        $params = $reflection->getParameters();
        echo "✓ Parâmetros: " . count($params) . "\n";
        
        foreach ($params as $param) {
            $type = $param->getType() ? $param->getType()->getName() : 'mixed';
            echo "  - $type \$" . $param->getName() . "\n";
        }
        
        // Verificar se o parâmetro é compatível
        if (count($params) > 0) {
            $firstParam = $params[0];
            $paramType = $firstParam->getType();
            
            if ($paramType && $paramType->getName() === 'int') {
                echo "✓ Parâmetro é int, compatível com ID\n";
                
                // Tentar chamar o método
                try {
                    echo "Tentando chamar index($urlParameter)...\n";
                    $controller->index((int)$urlParameter);
                } catch (Exception $e) {
                    echo "✗ Erro ao executar: " . $e->getMessage() . "\n";
                }
            } else {
                echo "✗ Parâmetro não é int: " . $paramType->getName() . "\n";
            }
        } else {
            echo "✓ Método index não requer parâmetros\n";
        }
        
    } else {
        echo "✗ Método index não encontrado\n";
    }
    
} else {
    echo "✗ Classe NÃO encontrada: \\App\\adms\\Controllers\\lgpd\\$urlController\n";
    
    // Listar classes disponíveis no namespace
    echo "\nClasses disponíveis no namespace lgpd:\n";
    $classes = get_declared_classes();
    foreach ($classes as $class) {
        if (strpos($class, 'App\\adms\\Controllers\\lgpd\\') === 0) {
            echo "  - " . str_replace('App\\adms\\Controllers\\lgpd\\', '', $class) . "\n";
        }
    }
}
