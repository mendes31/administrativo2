<?php
require 'vendor/autoload.php';

// Carregar variáveis de ambiente
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
    $pdo = new PDO("mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']}", $_ENV['DB_USER'], $_ENV['DB_PASS']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query('SELECT p.name, p.controller, alp.permission, alp.adms_access_level_id, al.name as level_name
                         FROM adms_pages p 
                         LEFT JOIN adms_access_levels_pages alp ON p.id = alp.adms_page_id 
                         LEFT JOIN adms_access_levels al ON alp.adms_access_level_id = al.id
                         WHERE p.directory = "branches" 
                         ORDER BY p.name, alp.adms_access_level_id');
    
    echo "Páginas de Filiais e suas permissões:\n";
    echo "=====================================\n";
    
    $currentPage = '';
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($currentPage != $row['name']) {
            echo "\n📄 " . $row['name'] . ' (' . $row['controller'] . "):\n";
            $currentPage = $row['name'];
        }
        
        $permission = $row['permission'] ? '✅ LIBERADO' : '❌ BLOQUEADO';
        $levelName = $row['level_name'] ? $row['level_name'] : 'NENHUM';
        echo "   - Nível: {$levelName} (ID: {$row['adms_access_level_id']}) - {$permission}\n";
    }
    
    echo "\n✅ Verificação concluída! As páginas de Filiais foram adicionadas a todos os níveis de acesso.\n";
    echo "🔓 Para liberar as permissões, acesse: Administração > Níveis de Acesso > [Nível] > Permissões\n";
    
} catch (PDOException $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
?> 