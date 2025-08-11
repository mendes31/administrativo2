<?php
require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->load();

try {
    $pdo = new PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASS']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Verificar estrutura da tabela
    $stmt = $pdo->query('DESCRIBE adms_pages');
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Estrutura da tabela adms_pages:\n";
    echo str_repeat("-", 60) . "\n";
    foreach($columns as $column) {
        echo sprintf("%-20s | %-15s | %-10s | %s\n", 
            $column['Field'], 
            $column['Type'], 
            $column['Null'], 
            $column['Key']
        );
    }
    
    // Verificar uma página existente para ver os campos
    echo "\nExemplo de página existente:\n";
    $stmt = $pdo->query('SELECT * FROM adms_pages WHERE controller_url LIKE "%tia%" LIMIT 1');
    $page = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($page) {
        foreach($page as $key => $value) {
            echo "$key: $value\n";
        }
    }
    
} catch(Exception $e) {
    echo 'Erro: ' . $e->getMessage() . "\n";
}
