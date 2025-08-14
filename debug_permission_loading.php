<?php
// Script para debugar o carregamento das permissões
require_once 'app/adms/Models/Services/DbConnection.php';

try {
    $pdo = new PDO("mysql:host=localhost;dbname=administrativo2", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Debug do Carregamento de Permissões</h2>";
    
    // 1. Verificar se a permissão ListLgpdInventory está no PageLayoutService
    echo "<h3>1. Verificação no PageLayoutService:</h3>";
    
    // Ler o arquivo PageLayoutService para ver se ListLgpdInventory está incluído
    $pageLayoutContent = file_get_contents('app/adms/Controllers/Services/PageLayoutService.php');
    if (strpos($pageLayoutContent, 'ListLgpdInventory') !== false) {
        echo "<p style='color: green;'>✅ ListLgpdInventory está incluído no PageLayoutService</p>";
    } else {
        echo "<p style='color: red;'>❌ ListLgpdInventory NÃO está incluído no PageLayoutService</p>";
    }
    
    // 2. Verificar se a permissão está sendo carregada pelo MenuPermissionUserRepository
    echo "<h3>2. Teste do MenuPermissionUserRepository:</h3>";
    
    // Simular o que o MenuPermissionUserRepository faz
    $menu = ['ListLgpdInventory'];
    
    // Verificar se a permissão está disponível para o nível de acesso 3
    $sql = "SELECT
                ap.controller,
                ap.page_status,
                alp.permission
            FROM 
                adms_pages AS ap
            LEFT JOIN
                adms_access_levels_pages AS alp ON alp.adms_page_id = ap.id
            WHERE 
                ap.controller = 'ListLgpdInventory'
                AND alp.adms_access_level_id = 3";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $permission = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($permission) {
        echo "<p style='color: green;'>✅ Permissão encontrada para nível 3:</p>";
        echo "<ul>";
        echo "<li><strong>Controller:</strong> {$permission['controller']}</li>";
        echo "<li><strong>Status da Página:</strong> " . ($permission['page_status'] ? 'Ativo' : 'Inativo') . "</li>";
        echo "<li><strong>Permissão:</strong> " . ($permission['permission'] ? '✅ LIBERADO' : '❌ BLOQUEADO') . "</li>";
        echo "</ul>";
    } else {
        echo "<p style='color: red;'>❌ Permissão NÃO encontrada para nível 3!</p>";
    }
    
    // 3. Verificar se há algum problema na consulta do MenuPermissionUserRepository
    echo "<h3>3. Simulação da consulta do MenuPermissionUserRepository:</h3>";
    
    // Consulta exata que o MenuPermissionUserRepository executa
    $sql = "SELECT
                ap.controller
            FROM 
                adms_users_access_levels AS aual
            LEFT JOIN
                adms_access_levels_pages AS alp ON alp.adms_access_level_id = aual.adms_access_level_id
            LEFT JOIN 
                adms_pages AS ap ON ap.id = alp.adms_page_id
            WHERE 
                aual.adms_user_id = (SELECT id FROM adms_users WHERE name LIKE '%teste1%' LIMIT 1)
                AND ap.controller IN ('ListLgpdInventory')
                AND alp.permission = 1";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($result)) {
        echo "<p style='color: red;'>❌ MenuPermissionUserRepository NÃO retornou ListLgpdInventory!</p>";
        
        // Vamos debugar passo a passo
        echo "<h4>Debug passo a passo:</h4>";
        
        // Passo 1: Verificar se o usuário existe
        $sql = "SELECT id, name FROM adms_users WHERE name LIKE '%teste1%'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo "<p>✅ Usuário encontrado: {$user['name']} (ID: {$user['id']})</p>";
            
            // Passo 2: Verificar níveis de acesso do usuário
            $sql = "SELECT adms_access_level_id FROM adms_users_access_levels WHERE adms_user_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$user['id']]);
            $accessLevels = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            echo "<p>Níveis de acesso: " . implode(', ', $accessLevels) . "</p>";
            
            // Passo 3: Verificar se a página existe
            $sql = "SELECT id, controller, page_status FROM adms_pages WHERE controller = 'ListLgpdInventory'";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $page = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($page) {
                echo "<p>✅ Página encontrada: ID {$page['id']}, Status: " . ($page['page_status'] ? 'Ativo' : 'Inativo') . "</p>";
                
                // Passo 4: Verificar permissões para cada nível de acesso do usuário
                foreach ($accessLevels as $levelId) {
                    $sql = "SELECT permission FROM adms_access_levels_pages WHERE adms_access_level_id = ? AND adms_page_id = ?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$levelId, $page['id']]);
                    $permission = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($permission) {
                        $status = $permission['permission'] ? '✅ LIBERADO' : '❌ BLOQUEADO';
                        echo "<p>Nível {$levelId}: {$status}</p>";
                    } else {
                        echo "<p>Nível {$levelId}: ❌ NÃO CONFIGURADO</p>";
                    }
                }
            } else {
                echo "<p>❌ Página não encontrada!</p>";
            }
        } else {
            echo "<p>❌ Usuário não encontrado!</p>";
        }
        
    } else {
        echo "<p style='color: green;'>✅ MenuPermissionUserRepository retornou ListLgpdInventory!</p>";
        echo "<p>Resultado: " . implode(', ', array_column($result, 'controller')) . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erro: " . $e->getMessage() . "</p>";
}
?>
