# Mini ERP

Este projeto demonstra um pequeno ERP escrito em PHP puro utilizando MySQL como banco de dados. Ele permite cadastrar produtos com estoque, gerenciar um carrinho de compras e finalizar pedidos com cálculo de frete.

## Executando com Docker

```bash
docker-compose up -d
```

A aplicação ficará disponível em `http://localhost:8080`.

Copie o arquivo `config/database.php.example` para `config/database.php` e ajuste caso seja necessário.

## Estrutura das tabelas

Os scripts para criação das tabelas encontram-se em `sql/schema.sql`.

## Páginas principais

- `index.php` – Tela inicial.
- `produtos.php` – Cadastro e listagem de produtos.
- `carrinho.php` – Visualização do carrinho e finalização do pedido.
