<?php
// Script para debugar as permissões do menu
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    echo "Usuário não está logado!";
    exit;
}

echo "<h2>Debug das Permissões do Menu</h2>";
echo "<p><strong>Usuário:</strong> " . ($_SESSION['user_name'] ?? 'N/A') . "</p>";
echo "<p><strong>ID do Usuário:</strong> " . $_SESSION['user_id'] . "</p>";
echo "<p><strong>Nível de Acesso:</strong> " . ($_SESSION['user_access_level_id'] ?? 'N/A') . "</p>";

// Incluir o arquivo de conexão
require_once 'app/adms/Models/Services/DbConnection.php';

try {
    $db = new \App\adms\Models\Services\DbConnection();
    $pdo = $db->getConnection();
    
    // Verificar todas as permissões LGPD disponíveis
    $sql = "SELECT controller, name, directory FROM adms_pages WHERE directory = 'lgpd' AND page_status = 1 ORDER BY name";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $lgpdPages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Páginas LGPD Disponíveis no Sistema:</h3>";
    echo "<ul>";
    foreach ($lgpdPages as $page) {
        echo "<li><strong>{$page['controller']}</strong> - {$page['name']} ({$page['directory']})</li>";
    }
    echo "</ul>";
    
    // Verificar permissões do usuário atual
    $sql = "SELECT 
                ap.controller,
                ap.name,
                alp.permission,
                aual.adms_access_level_id
            FROM 
                adms_users_access_levels AS aual
            LEFT JOIN
                adms_access_levels_pages AS alp ON alp.adms_access_level_id = aual.adms_access_level_id
            LEFT JOIN 
                adms_pages AS ap ON ap.id = alp.adms_page_id
            WHERE 
                aual.adms_user_id = ?
                AND ap.directory = 'lgpd'
                AND ap.page_status = 1
            ORDER BY ap.name";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['user_id']]);
    $userPermissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Permissões do Usuário Atual:</h3>";
    if (empty($userPermissions)) {
        echo "<p style='color: red;'>Nenhuma permissão LGPD encontrada para este usuário!</p>";
    } else {
        echo "<ul>";
        foreach ($userPermissions as $permission) {
            $status = $permission['permission'] ? '✅ LIBERADO' : '❌ BLOQUEADO';
            $color = $permission['permission'] ? 'green' : 'red';
            echo "<li style='color: {$color};'><strong>{$permission['controller']}</strong> - {$permission['name']} - {$status} (Nível: {$permission['adms_access_level_id']})</li>";
        }
        echo "</ul>";
    }
    
    // Verificar especificamente as permissões do inventário
    echo "<h3>Verificação Específica - Inventário LGPD:</h3>";
    $sql = "SELECT 
                ap.controller,
                ap.name,
                alp.permission,
                aual.adms_access_level_id
            FROM 
                adms_users_access_levels AS aual
            LEFT JOIN
                adms_access_levels_pages AS alp ON alp.adms_access_level_id = aual.adms_access_level_id
            LEFT JOIN 
                adms_pages AS ap ON ap.id = alp.adms_page_id
            WHERE 
                aual.adms_user_id = ?
                AND ap.controller IN ('ListLgpdInventory', 'LgpdInventoryCreate', 'LgpdInventoryEdit', 'LgpdInventoryView', 'LgpdInventoryDelete')
                AND ap.page_status = 1";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['user_id']]);
    $inventoryPermissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($inventoryPermissions)) {
        echo "<p style='color: red;'>Nenhuma permissão de inventário encontrada para este usuário!</p>";
    } else {
        echo "<ul>";
        foreach ($inventoryPermissions as $permission) {
            $status = $permission['permission'] ? '✅ LIBERADO' : '❌ BLOQUEADO';
            $color = $permission['permission'] ? 'green' : 'red';
            echo "<li style='color: {$color};'><strong>{$permission['controller']}</strong> - {$permission['name']} - {$status} (Nível: {$permission['adms_access_level_id']})</li>";
        }
        echo "</ul>";
    }
    
    // Verificar se o usuário é super admin
    $sql = "SELECT adms_access_level_id FROM adms_users_access_levels WHERE adms_user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['user_id']]);
    $accessLevels = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h3>Níveis de Acesso do Usuário:</h3>";
    echo "<ul>";
    foreach ($accessLevels as $level) {
        $isSuperAdmin = $level == 1 ? ' (SUPER ADMIN)' : '';
        echo "<li>Nível {$level}{$isSuperAdmin}</li>";
    }
    echo "</ul>";
    
    if (in_array(1, $accessLevels)) {
        echo "<p style='color: green;'><strong>Este usuário é Super Administrador e deve ter acesso a todas as funcionalidades!</strong></p>";
    }
    
    // Verificar se as permissões estão sendo carregadas corretamente pelo PageLayoutService
    echo "<h3>Teste do PageLayoutService:</h3>";
    
    // Simular o que o PageLayoutService faz
    $menu = [
        'ListLgpdInventory',
        'LgpdInventoryCreate', 
        'LgpdInventoryEdit',
        'LgpdInventoryView',
        'LgpdInventoryDelete'
    ];
    
    // Verificar se essas permissões estão disponíveis para o usuário
    $placeholders = implode(', ', array_fill(0, count($menu), '?'));
    $sql = "SELECT
                ap.controller
            FROM 
                adms_users_access_levels AS aual
            LEFT JOIN
                adms_access_levels_pages AS alp ON alp.adms_access_level_id = aual.adms_access_level_id
            LEFT JOIN 
                adms_pages AS ap ON ap.id = alp.adms_page_id
            WHERE 
                aual.adms_user_id = ?
                AND ap.controller IN ($placeholders)
                AND alp.permission = 1";
    
    $stmt = $pdo->prepare($sql);
    $params = array_merge([$_SESSION['user_id']], $menu);
    $stmt->execute($params);
    $availablePermissions = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<p><strong>Permissões disponíveis para o usuário:</strong></p>";
    echo "<ul>";
    foreach ($menu as $permission) {
        $hasPermission = in_array($permission, $availablePermissions);
        $status = $hasPermission ? '✅ DISPONÍVEL' : '❌ NÃO DISPONÍVEL';
        $color = $hasPermission ? 'green' : 'red';
        echo "<li style='color: {$color};'>{$permission} - {$status}</li>";
    }
    echo "</ul>";
    
    // Verificar se há pelo menos uma permissão disponível
    $hasAnyPermission = count($availablePermissions) > 0;
    echo "<p><strong>Menu deve ser exibido:</strong> " . ($hasAnyPermission ? '✅ SIM' : '❌ NÃO') . "</p>";
    
    if (!$hasAnyPermission) {
        echo "<p style='color: red;'><strong>PROBLEMA IDENTIFICADO: O usuário não tem nenhuma permissão de inventário!</strong></p>";
        echo "<p>Isso explica por que o menu não está sendo exibido.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erro ao conectar com o banco de dados: " . $e->getMessage() . "</p>";
}
?>
