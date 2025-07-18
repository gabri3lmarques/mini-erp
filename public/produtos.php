<?php
session_start();
$config = require __DIR__ . '/../config/database.php';
$dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
$pdo = new PDO($dsn, $config['user'], $config['pass']);

function listarProdutos($pdo) {
    return $pdo->query('SELECT * FROM produtos')->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $preco = $_POST['preco'];
    $variacao = $_POST['variacao'];
    $quantidade = (int)$_POST['quantidade'];
    if (isset($_POST['id']) && $_POST['id']) {
        $id = (int)$_POST['id'];
        $stmt = $pdo->prepare('UPDATE produtos SET nome=?, preco=? WHERE id=?');
        $stmt->execute([$nome, $preco, $id]);
        $stmt = $pdo->prepare('UPDATE estoque SET variacao=?, quantidade=? WHERE produto_id=?');
        $stmt->execute([$variacao, $quantidade, $id]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO produtos (nome, preco) VALUES (?, ?)');
        $stmt->execute([$nome, $preco]);
        $produtoId = $pdo->lastInsertId();
        $stmt = $pdo->prepare('INSERT INTO estoque (produto_id, variacao, quantidade) VALUES (?, ?, ?)');
        $stmt->execute([$produtoId, $variacao, $quantidade]);
    }
    header('Location: produtos.php');
    exit;
}

$produtos = listarProdutos($pdo);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Produtos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
</head>
<body class="container py-4">
    <h1>Cadastro de Produtos</h1>
    <form method="post" class="mb-4">
        <input type="hidden" name="id" id="prod-id">
        <div class="mb-2">
            <label>Nome</label>
            <input class="form-control" type="text" name="nome" id="prod-nome" required>
        </div>
        <div class="mb-2">
            <label>Preço</label>
            <input class="form-control" type="number" step="0.01" name="preco" id="prod-preco" required>
        </div>
        <div class="mb-2">
            <label>Variação</label>
            <input class="form-control" type="text" name="variacao" id="prod-variacao">
        </div>
        <div class="mb-2">
            <label>Quantidade em Estoque</label>
            <input class="form-control" type="number" name="quantidade" id="prod-quantidade" required>
        </div>
        <button class="btn btn-primary" type="submit">Salvar</button>
    </form>
    <h2>Produtos cadastrados</h2>
    <table class="table">
        <thead><tr><th>ID</th><th>Nome</th><th>Preço</th><th>Ações</th></tr></thead>
        <tbody>
            <?php foreach ($produtos as $p): ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td><?= htmlspecialchars($p['nome']) ?></td>
                <td>R$ <?= number_format($p['preco'], 2, ',', '.') ?></td>
                <td>
                    <button class="btn btn-sm btn-secondary" onclick="editar(<?= $p['id'] ?>, '<?= htmlspecialchars($p['nome'], ENT_QUOTES) ?>', <?= $p['preco'] ?>)">Editar</button>
                    <a class="btn btn-sm btn-success" href="carrinho.php?add=<?= $p['id'] ?>">Comprar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<script>
function editar(id,nome,preco){
    document.getElementById('prod-id').value=id;
    document.getElementById('prod-nome').value=nome;
    document.getElementById('prod-preco').value=preco;
}
</script>
</body>
</html>
