<?php
session_start();
if($_SERVER['REQUEST_METHOD']!=='POST'){
    header('Location: carrinho.php');
    exit;
}
$config = require __DIR__ . '/../config/database.php';
$dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
$pdo = new PDO($dsn, $config['user'], $config['pass']);
$cep = preg_replace('/[^0-9]/','',$_POST['cep']);
$nome = $_POST['nome'];
$resp = @file_get_contents("https://viacep.com.br/ws/{$cep}/json/");
$dadosCep = $resp ? json_decode($resp,true) : null;
$endereco = $dadosCep && empty($dadosCep['erro']) ? $dadosCep['logradouro'].', '.$dadosCep['bairro'].' - '.$dadosCep['localidade'] : 'CEP inválido';

$sub=0;foreach($_SESSION['cart'] as $c){$sub+=$c['produto']['preco']*$c['qtd'];}
$frete=($sub>200)?0:(($sub>=52&&$sub<=166.59)?15:20);
$total=$sub+$frete;

$pdo->beginTransaction();
$stmt = $pdo->prepare('INSERT INTO pedidos (subtotal,frete,total,nome_cliente,cep,endereco) VALUES (?,?,?,?,?,?)');
$stmt->execute([$sub,$frete,$total,$nome,$cep,$endereco]);
$pedidoId=$pdo->lastInsertId();
$stmtItem=$pdo->prepare('INSERT INTO pedido_itens (pedido_id,produto_id,variacao,quantidade,preco_unitario) VALUES (?,?,?,?,?)');
$updateEstoque=$pdo->prepare('UPDATE estoque SET quantidade=quantidade-? WHERE produto_id=?');
foreach($_SESSION['cart'] as $c){
    $stmtItem->execute([$pedidoId,$c['produto']['id'],$c['produto']['variacao'],$c['qtd'],$c['produto']['preco']]);
    $updateEstoque->execute([$c['qtd'],$c['produto']['id']]);
}
$pdo->commit();
$_SESSION['cart']=[];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Pedido Finalizado</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
</head>
<body class="container py-4">
<h1>Pedido Finalizado</h1>
<p>Endereço: <?= htmlspecialchars($endereco) ?></p>
<p>Total pago: R$ <?= number_format($total,2,',','.') ?></p>
<a href="produtos.php" class="btn btn-primary">Voltar aos Produtos</a>
</body>
</html>
