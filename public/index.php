<?php
require __DIR__ . '/../config/database.php';
$config = require __DIR__ . '/../config/database.php';
$dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
try {
    new PDO($dsn, $config['user'], $config['pass']);
    $msg = '✅ Conexão com MySQL bem-sucedida!';
} catch (PDOException $e) {
    $msg = '❌ Erro ao conectar: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Mini ERP</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
</head>
<body class="container py-4">
<h1>Mini ERP</h1>
<p><?= $msg ?></p>
<a href="produtos.php" class="btn btn-primary">Gerenciar Produtos</a>
<a href="carrinho.php" class="btn btn-secondary">Ver Carrinho</a>
</body>
</html>
