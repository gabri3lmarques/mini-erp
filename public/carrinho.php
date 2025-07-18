<?php
session_start();
$config = require __DIR__ . '/../config/database.php';
$dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
$pdo = new PDO($dsn, $config['user'], $config['pass']);

function produto($pdo, $id){
    $stmt = $pdo->prepare('SELECT p.*, e.quantidade, e.variacao FROM produtos p JOIN estoque e ON e.produto_id=p.id WHERE p.id=?');
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if(isset($_GET['add'])){
    $prod = produto($pdo, (int)$_GET['add']);
    if($prod && $prod['quantidade']>0){
        if(!isset($_SESSION['cart'][$prod['id']])){
            $_SESSION['cart'][$prod['id']] = ['produto'=>$prod,'qtd'=>1];
        }else{
            $_SESSION['cart'][$prod['id']]['qtd']++;
        }
    }
    header('Location: carrinho.php');
    exit;
}

function subtotal(){
    $s=0;foreach($_SESSION['cart'] as $c){$s+=$c['produto']['preco']*$c['qtd'];}return $s;
}

function frete($subtotal){
    if($subtotal>200) return 0;
    if($subtotal>=52 && $subtotal<=166.59) return 15;
    return 20;
}

$sub = subtotal();
$frete = frete($sub);
$total = $sub + $frete;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Carrinho</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
</head>
<body class="container py-4">
<h1>Carrinho</h1>
<table class="table">
<thead><tr><th>Produto</th><th>Qtd</th><th>Preço</th></tr></thead>
<tbody>
<?php foreach($_SESSION['cart'] as $c): ?>
<tr>
    <td><?= htmlspecialchars($c['produto']['nome']) ?></td>
    <td><?= $c['qtd'] ?></td>
    <td>R$ <?= number_format($c['produto']['preco']*$c['qtd'],2,',','.') ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<p>Subtotal: R$ <?= number_format($sub,2,',','.') ?></p>
<p>Frete: R$ <?= number_format($frete,2,',','.') ?></p>
<p>Total: R$ <?= number_format($total,2,',','.') ?></p>
<form method="post" action="finalizar.php" class="mt-3">
    <div class="mb-2">
        <label>CEP</label>
        <input type="text" name="cep" class="form-control" required>
    </div>
    <div class="mb-2">
        <label>Nome</label>
        <input type="text" name="nome" class="form-control" required>
    </div>
    <button class="btn btn-primary" type="submit">Finalizar Pedido</button>
</form>
</body>
</html>
