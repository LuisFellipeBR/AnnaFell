<?php
if(!isset($_SESSION)) {
    session_start();
} 
require_once 'produtos.php';
$produtos = getProdutos();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Loja Virtual - Produtos</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <div class="nav-container">
        <a href="index.php" class="nav-logo">AnnaFell</a>
        <div class="nav-links">
            <a href="index.php">Produtos</a>
            <a href="carrinho.php">Carrinho <?php if(!empty($_SESSION['carrinho'])) echo '('.array_sum(array_column($_SESSION['carrinho'], 'quantidade')).')'; ?></a>
            <a href="login.php">Login</a>
            <a id="abrirContato">Contato</a>
        </div>
    </div>
</div>

<!-- Modal de contato -->
<div id="modalContato" class="modal">
    <div class="modal-conteudo">
        <span class="fechar-modal">&times;</span>
        <h3>Entre em contato</h3>
        <p>WhatsApp: (73) 99999-9999</p>
        <p>E-mail: contato@AnnaFell.com</p>
        <p>Instagram: @AnnaFell</p>
        <p>Horário de atendimento: Seg a Sex, 9h às 18h</p>
        <button class="btn fechar-modal-btn">Fechar</button>
    </div>
</div>

<div class="container">
    <h1>Nossos Produtos</h1>
    <div class="produtos">
        <?php foreach ($produtos as $id => $produto): ?>
            <div class="produto">
                <img src="<?php echo $produto['imagem']; ?>" class="produto-img">
                <h3><?php echo $produto['nome']; ?></h3>
                <p><?php echo $produto['descricao']; ?></p>
                <p class="preco">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                <a href="carrinho.php?adicionar=<?php echo $id; ?>" class="btn-comprar">Comprar</a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div id="rodape">Desenvolvido por Anna Rebeca e Luís Fellipe © 2026</div>

<script src="js/script.js"></script>
</body>
</html>