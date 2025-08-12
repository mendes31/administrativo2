<?php
require __DIR__ . '/../vendor/autoload.php';

$pdo = new PDO('mysql:host=localhost;dbname=administrativo2', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$map = [
    'lgpd-tia-templates'      => 'LgpdTiaTemplates',
    'lgpd-tia-export-pdf'     => 'LgpdTiaExportPdf',
    'lgpd-tia-export-pdf-view'=> 'LgpdTiaExportPdfView',
    'lgpd-tia-export-pdf-list'=> 'LgpdTiaExportPdfList',
];

foreach ($map as $url => $controller) {
    $stmt = $pdo->prepare('UPDATE adms_pages SET controller = :controller WHERE controller_url = :url');
    $stmt->execute([':controller' => $controller, ':url' => $url]);
    echo "Atualizado: $url -> $controller\n";
}

echo "Concluído.\n";
