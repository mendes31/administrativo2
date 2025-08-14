<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "Usuário não está logado!";
    exit;
}

echo "<h2>Teste da Lógica de Verificação de Permissões do Menu</h2>";
echo "<p><strong>Usuário:</strong> " . ($_SESSION['user_name'] ?? 'N/A') . "</p>";

require_once 'app/adms/Models/Services/DbConnection.php';

try {
    $db = new \App\adms\Models\Services\DbConnection();
    $pdo = $db->getConnection();
    
    // Simular as permissões do usuário
    $menu = [
        'ListLgpdInventory',
        'LgpdInventoryCreate', 
        'LgpdInventoryEdit',
        'LgpdInventoryView',
        'LgpdInventoryDelete'
    ];
    
    // Verificar permissões disponíveis
    $placeholders = implode(', ', array_fill(0, count($menu), '?'));
    $sql = "SELECT ap.controller FROM adms_users_access_levels AS aual
            LEFT JOIN adms_access_levels_pages AS alp ON alp.adms_access_level_id = aual.adms_access_level_id
            LEFT JOIN adms_pages AS ap ON ap.id = alp.adms_page_id
            WHERE aual.adms_user_id = ? AND ap.controller IN ($placeholders) AND alp.permission = 1";
    
    $stmt = $pdo->prepare($sql);
    $params = array_merge([$_SESSION['user_id']], $menu);
    $stmt->execute($params);
    $availablePermissions = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h3>Permissões Disponíveis:</h3>";
    foreach ($menu as $permission) {
        $hasPermission = in_array($permission, $availablePermissions);
        $status = $hasPermission ? '✅ DISPONÍVEL' : '❌ NÃO DISPONÍVEL';
        $color = $hasPermission ? 'green' : 'red';
        echo "<p style='color: {$color};'>{$permission} - {$status}</p>";
    }
    
    // Testar lógica do menu
    $hasAnyPermission = count($availablePermissions) > 0;
    echo "<p><strong>Menu deve ser exibido:</strong> " . ($hasAnyPermission ? '✅ SIM' : '❌ NÃO') . "</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erro: " . $e->getMessage() . "</p>";
}
?>
