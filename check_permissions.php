<?php
require 'vendor/autoload.php';

// Carregar variáveis de ambiente
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
    $pdo = new PDO("mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']}", $_ENV['DB_USER'], $_ENV['DB_PASS']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query('SELECT p.name, p.controller, alp.permission, alp.adms_access_level_id 
                         FROM adms_pages p 
                         LEFT JOIN adms_access_levels_pages alp ON p.id = alp.adms_page_id 
                         WHERE p.directory = "logs" 
                         ORDER BY p.name');
    
    echo "Páginas de Logs e suas permissões:\n";
    echo "===================================\n";
    
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['name'] . ' (' . $row['controller'] . ') - Permissão: ' . 
             ($row['permission'] ? $row['permission'] : 'NENHUMA') . 
             ' - Nível: ' . ($row['adms_access_level_id'] ? $row['adms_access_level_id'] : 'NENHUM') . "\n";
    }
    
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
?> 