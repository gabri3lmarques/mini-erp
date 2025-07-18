<?php
$host = 'db';
$db   = 'erp';
$user = 'erpuser';
$pass = 'senha123';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass);
    echo "✅ Conexão com MySQL bem-sucedida!";
} catch (PDOException $e) {
    echo "❌ Erro ao conectar: " . $e->getMessage();
}
