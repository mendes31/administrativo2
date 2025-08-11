<?php
require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->load();

try {
    $pdo = new PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASS']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Criar nova página com URL correta
    $stmt = $pdo->prepare('INSERT INTO adms_pages (controller_url, controller, name, type, order_level, obs, adms_sits_pgs_id) VALUES (?, ?, ?, ?, ?, ?, ?)');
    
    $stmt->execute([
        'lgpd-tia-export-pdf-view',  // URL correta
        'LgpdTiaExportPdfView',      // Controller correto
        'Exportar TIA PDF Individual', // Nome descritivo
        'private',                    // Tipo privado
        1,                           // Ordem
        'Exportação individual de TIA para PDF', // Observação
        1                            // Status ativo
    ]);
    
    echo "✓ Nova página criada: lgpd-tia-export-pdf-view -> LgpdTiaExportPdfView\n";
    
    // Atualizar a página existente para apontar para o controller correto
    $stmt = $pdo->prepare('UPDATE adms_pages SET controller = ? WHERE controller_url = ?');
    $stmt->execute(['LgpdTiaExportPdf', 'lgpd-tia-export-pdf']);
    
    echo "✓ Página existente atualizada: lgpd-tia-export-pdf -> LgpdTiaExportPdf\n";
    
    echo "\nConcluído! Agora as URLs funcionam assim:\n";
    echo "- lgpd-tia-export-pdf/1 -> LgpdTiaExportPdfView (TIA individual)\n";
    echo "- lgpd-tia-export-pdf-list -> LgpdTiaExportPdfList (Lista completa)\n";
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
