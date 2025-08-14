<?php
// Script para testar as permissões LGPD do usuário logado
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    echo "Usuário não está logado!";
    exit;
}

echo "<h2>Teste de Permissões LGPD</h2>";
echo "<p><strong>Usuário:</strong> " . ($_SESSION['user_name'] ?? 'N/A') . "</p>";
echo "<p><strong>ID do Usuário:</strong> " . $_SESSION['user_id'] . "</p>";
echo "<p><strong>Nível de Acesso:</strong> " . ($_SESSION['user_access_level_id'] ?? 'N/A') . "</p>";

// Incluir o arquivo de conexão
require_once 'app/adms/Models/Services/DbConnection.php';

try {
    $db = new \App\adms\Models\Services\DbConnection();
    $pdo = $db->getConnection();
    
    // Verificar todas as permissões LGPD disponíveis
    $sql = "SELECT controller, name FROM adms_pages WHERE directory = 'lgpd' AND page_status = 1 ORDER BY name";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $lgpdPages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Páginas LGPD Disponíveis no Sistema:</h3>";
    echo "<ul>";
    foreach ($lgpdPages as $page) {
        echo "<li><strong>{$page['controller']}</strong> - {$page['name']}</li>";
    }
    echo "</ul>";
    
    // Verificar permissões do usuário atual
    $sql = "SELECT 
                ap.controller,
                ap.name,
                alp.permission
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
            echo "<li style='color: {$color};'><strong>{$permission['controller']}</strong> - {$permission['name']} - {$status}</li>";
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
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erro ao conectar com o banco de dados: " . $e->getMessage() . "</p>";
}
?>
