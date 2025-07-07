<?php
session_start();
echo "<h1>Teste de Sessão</h1>";
echo "<pre>";
echo "session_id(): " . session_id() . "\n";
echo "Cookie PHPSESSID: " . ($_COOKIE['PHPSESSID'] ?? 'NÃO ENCONTRADO') . "\n";
print_r($_SESSION);
echo "</pre>";
?> 