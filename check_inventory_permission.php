<?php
// Script para verificar especificamente a permissão ListLgpdInventory
require_once 'app/adms/Models/Services/DbConnection.php';

try {
    // Usar a classe concreta correta
    $pdo = new PDO("mysql:host=localhost;dbname=administrativo2", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Verificação da Permissão ListLgpdInventory</h2>";
    
    // 1. Verificar se a página existe na tabela adms_pages
    echo "<h3>1. Verificação na tabela adms_pages:</h3>";
    $sql = "SELECT id, controller, name, directory, page_status FROM adms_pages WHERE controller = 'ListLgpdInventory'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $page = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($page) {
        echo "<p style='color: green;'>✅ Página encontrada:</p>";
        echo "<ul>";
        echo "<li><strong>ID:</strong> {$page['id']}</li>";
        echo "<li><strong>Controller:</strong> {$page['controller']}</li>";
        echo "<li><strong>Nome:</strong> {$page['name']}</li>";
        echo "<li><strong>Diretório:</strong> {$page['directory']}</li>";
        echo "<li><strong>Status:</strong> " . ($page['page_status'] ? 'Ativo' : 'Inativo') . "</li>";
        echo "</ul>";
        
        $pageId = $page['id'];
    } else {
        echo "<p style='color: red;'>❌ Página NÃO encontrada na tabela adms_pages!</p>";
        exit;
    }
    
    // 2. Verificar se a permissão está configurada para algum nível de acesso
    echo "<h3>2. Verificação na tabela adms_access_levels_pages:</h3>";
    $sql = "SELECT 
                alp.adms_access_level_id,
                alp.permission,
                al.name as access_level_name
            FROM adms_access_levels_pages alp
            LEFT JOIN adms_access_levels al ON al.id = alp.adms_access_level_id
            WHERE alp.adms_page_id = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$pageId]);
    $accessLevels = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($accessLevels)) {
        echo "<p style='color: red;'>❌ Nenhum nível de acesso configurado para esta página!</p>";
    } else {
        echo "<p style='color: green;'>✅ Níveis de acesso configurados:</p>";
        echo "<ul>";
        foreach ($accessLevels as $level) {
            $status = $level['permission'] ? '✅ LIBERADO' : '❌ BLOQUEADO';
            $color = $level['permission'] ? 'green' : 'red';
            echo "<li style='color: {$color};'>Nível {$level['adms_access_level_id']} ({$level['access_level_name']}) - {$status}</li>";
        }
        echo "</ul>";
    }
    
    // 3. Verificar especificamente o usuário teste1
    echo "<h3>3. Verificação específica do usuário teste1:</h3>";
    
    // Primeiro, vamos encontrar o usuário teste1
    $sql = "SELECT id, name, email FROM adms_users WHERE email = 'teste1@teste.com' OR name LIKE '%teste1%'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "<p><strong>Usuário encontrado:</strong> {$user['name']} (ID: {$user['id']})</p>";
        
        // Verificar os níveis de acesso do usuário
        $sql = "SELECT 
                    aual.adms_access_level_id,
                    al.name as access_level_name
                FROM adms_users_access_levels aual
                LEFT JOIN adms_access_levels al ON al.id = aual.adms_access_level_id
                WHERE aual.adms_user_id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user['id']]);
        $userAccessLevels = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<p><strong>Níveis de acesso do usuário:</strong></p>";
        echo "<ul>";
        foreach ($userAccessLevels as $level) {
            echo "<li>Nível {$level['adms_access_level_id']} ({$level['access_level_name']})</li>";
        }
        echo "</ul>";
        
        // Verificar se o usuário tem acesso à página ListLgpdInventory
        $sql = "SELECT 
                    alp.permission
                FROM adms_users_access_levels aual
                LEFT JOIN adms_access_levels_pages alp ON alp.adms_access_level_id = aual.adms_access_level_id
                WHERE aual.adms_user_id = ? AND alp.adms_page_id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user['id'], $pageId]);
        $userPermission = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($userPermission) {
            $status = $userPermission['permission'] ? '✅ LIBERADO' : '❌ BLOQUEADO';
            $color = $userPermission['permission'] ? 'green' : 'red';
            echo "<p style='color: {$color};'><strong>Permissão do usuário para ListLgpdInventory:</strong> {$status}</p>";
        } else {
            echo "<p style='color: red;'><strong>❌ Usuário NÃO tem acesso configurado para ListLgpdInventory!</strong></p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Usuário teste1 não encontrado!</p>";
    }
    
    // 4. Verificar se há outros usuários com essa permissão
    echo "<h3>4. Verificar outros usuários com essa permissão:</h3>";
    $sql = "SELECT 
                u.name as user_name,
                u.email,
                aual.adms_access_level_id,
                alp.permission
            FROM adms_users u
            LEFT JOIN adms_users_access_levels aual ON aual.adms_user_id = u.id
            LEFT JOIN adms_access_levels_pages alp ON alp.adms_access_level_id = aual.adms_access_level_id
            WHERE alp.adms_page_id = ? AND alp.permission = 1
            ORDER BY u.name";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$pageId]);
    $usersWithPermission = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($usersWithPermission)) {
        echo "<p style='color: red;'>❌ Nenhum usuário tem acesso a esta página!</p>";
    } else {
        echo "<p style='color: green;'>✅ Usuários com acesso:</p>";
        echo "<ul>";
        foreach ($usersWithPermission as $user) {
            echo "<li>{$user['user_name']} ({$user['email']}) - Nível {$user['adms_access_level_id']}</li>";
        }
        echo "</ul>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erro ao conectar com o banco de dados: " . $e->getMessage() . "</p>";
}
?>
