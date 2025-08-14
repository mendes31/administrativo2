<?php
// Script para debug das permissões
echo "<h2>Debug das Permissões</h2>";

// Verificar se há dados POST
if ($_POST) {
    echo "<h3>Dados POST recebidos:</h3>";
    echo "<pre>" . print_r($_POST, true) . "</pre>";
    
    if (isset($_POST['permissions'])) {
        echo "<h3>Análise do campo 'permissions':</h3>";
        echo "<ul>";
        echo "<li>Tipo: " . gettype($_POST['permissions']) . "</li>";
        echo "<li>É array? " . (is_array($_POST['permissions']) ? 'Sim' : 'Não') . "</li>";
        
        if (is_array($_POST['permissions'])) {
            echo "<li>Total de permissões: " . count($_POST['permissions']) . "</li>";
            echo "<li>Chaves: " . implode(', ', array_keys($_POST['permissions'])) . "</li>";
            echo "<li>Valores: " . implode(', ', array_values($_POST['permissions'])) . "</li>";
            
            echo "<h4>Detalhamento:</h4>";
            foreach ($_POST['permissions'] as $pageId => $value) {
                $status = $value == '1' ? '✅ Autorizada' : '❌ Revogada';
                echo "<p>Página {$pageId}: {$status} (valor: '{$value}')</p>";
            }
        }
        echo "</ul>";
    } else {
        echo "<p style='color: red;'>❌ Campo 'permissions' NÃO encontrado nos dados POST</p>";
    }
    
    if (isset($_POST['csrf_token'])) {
        echo "<p>✅ CSRF Token: " . $_POST['csrf_token'] . "</p>";
    }
    
    if (isset($_POST['adms_access_level_id'])) {
        echo "<p>✅ Access Level ID: " . $_POST['adms_access_level_id'] . "</p>";
    }
    
} else {
    echo "<h3>Formulário de Teste</h3>";
    echo "<form method='POST'>";
    echo "<input type='hidden' name='csrf_token' value='test_token'>";
    echo "<input type='hidden' name='adms_access_level_id' value='3'>";
    echo "<p>Simular permissões:</p>";
    echo "<label><input type='checkbox' name='permissions[1]' value='1'> Página 1 (Autorizada)</label><br>";
    echo "<label><input type='checkbox' name='permissions[2]' value='1'> Página 2 (Revogada - não marcado)</label><br>";
    echo "<label><input type='checkbox' name='permissions[3]' value='1' checked> Página 3 (Autorizada)</label><br>";
    echo "<label><input type='checkbox' name='permissions[4]' value='1'> Página 4 (Revogada - não marcado)</label><br>";
    echo "<br><button type='submit'>Enviar Teste</button>";
    echo "</form>";
    
    echo "<p><strong>Nota:</strong> Checkboxes desmarcados NÃO enviam dados. O JavaScript deve coletar TODOS os checkboxes e enviar seus valores.</p>";
}
?>
