<?php
require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->load();

try {
    $pdo = new PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASS']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query('SELECT controller_url, controller, name FROM adms_pages WHERE controller_url LIKE "%tia%" ORDER BY controller_url');
    $pages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Todas as páginas relacionadas a TIA:\n";
    echo str_repeat("-", 80) . "\n";
    foreach($pages as $page) {
        echo sprintf("%-30s | %-25s | %s\n", 
            $page['controller_url'], 
            $page['controller'], 
            $page['name']
        );
    }
    
    echo "\n";
    echo "Testando conversão de slugs:\n";
    echo str_repeat("-", 50) . "\n";
    
    $testUrls = [
        'lgpd-tia-export-pdf',
        'lgpd-tia-export-pdf-list',
        'lgpd-tia-export-pdf-view'
    ];
    
    foreach($testUrls as $url) {
        $converted = str_replace("-", "", ucwords($url, "-"));
        echo sprintf("%-25s -> %s\n", $url, $converted);
    }
    
} catch(Exception $e) {
    echo 'Erro: ' . $e->getMessage() . "\n";
}
