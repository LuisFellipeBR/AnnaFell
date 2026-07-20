<?php
if(!isset($_SESSION)) {
    session_start();
}
require_once 'produtos.php';
$produtos = getProdutos();

if (!isset($_SESSION['carrinho'])) 
    $_SESSION['carrinho'] = [];

if (isset($_GET['adicionar'])) {
    $id = (int)$_GET['adicionar'];
    if (isset($produtos[$id])) {
        if (isset($_SESSION['carrinho'][$id])) {
            $_SESSION['carrinho'][$id]['quantidade']++;
        } else {
            $_SESSION['carrinho'][$id] = [
                'nome' => $produtos[$id]['nome'],
                'preco' => $produtos[$id]['preco'],
                'imagem' => $produtos[$id]['imagem'],
                'quantidade' => 1
            ];
        }
    }
    header("Location: carrinho.php");
    exit();
}

if (isset($_POST['remover'])) {
    unset($_SESSION['carrinho'][(int)$_POST['remover']]);
    header("Location: carrinho.php");
    exit();
}
if (isset($_POST['atualizar'])) {
    foreach ($_POST['quantidade'] as $id => $qtd) {
        $id = (int)$id;
        $qtd = (int)$qtd;
        if ($qtd <= 0) unset($_SESSION['carrinho'][$id]);
        else $_SESSION['carrinho'][$id]['quantidade'] = $qtd;
    }
    header("Location: carrinho.php");
    exit();
}
if (isset($_POST['finalizar']) && !empty($_SESSION['carrinho'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meu Carrinho</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

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

<div id="modalContato" class="modal">
    <div class="modal-conteudo">
        <span class="fechar-modal">&times;</span>
        <h3>📞 Entre em contato</h3>
        <p>WhatsApp: (73) 99999-9999</p>
        <p>E-mail: contato@AnnaFell.com</p>
        <p>Instagram: @AnnaFell</p>
        <p>Horário de atendimento: Seg a Sex, 9h às 18h</p>
        <button class="btn fechar-modal-btn">Fechar</button>
    </div>
</div>

<div class="container">
    <h1>Meu Carrinho</h1>
    <?php if (empty($_SESSION['carrinho'])): ?>
        <div class="carrinho-vazio">
            <p>Seu carrinho está vazio.</p>
            <a href="index.php" class="btn">Continuar Comprando</a>
        </div>
    <?php else: ?>
        <form method="POST">
            <div class="carrinho-tabela">
                <div class="carrinho-linha cabecalho">
                    <div class="celula-produto">Produto</div>
                    <div class="celula-preco">Preço</div>
                    <div class="celula-quantidade">Quantidade</div>
                    <div class="celula-subtotal">Subtotal</div>
                    <div class="celula-acao"></div>
                </div>
                <?php 
                $total = 0;
                foreach ($_SESSION['carrinho'] as $id => $item):
                    $subtotal = $item['preco'] * $item['quantidade'];
                    $total += $subtotal;
                ?>
                <div class="carrinho-linha">
                    <div class="celula-produto">
                        <img src="<?php echo $item['imagem']; ?>" class="carrinho-img">
                        <?php echo $item['nome']; ?>
                    </div>
                    <div class="celula-preco">R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></div>
                    <div class="celula-quantidade">
                        <input type="number" name="quantidade[<?php echo $id; ?>]" value="<?php echo $item['quantidade']; ?>" min="1" class="input-quantidade">
                    </div>
                    <div class="celula-subtotal">R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></div>
                    <div class="celula-acao">
                        <button type="submit" name="remover" value="<?php echo $id; ?>" class="btn-remover">Remover</button>
                    </div>
                </div>
                <?php endforeach; ?>
                <div class="carrinho-total">
                    <div>Total:</div>
                    <div class="total-valor">R$ <?php echo number_format($total, 2, ',', '.'); ?></div>
                </div>
            </div>
            <div class="carrinho-acoes">
                <button type="submit" name="atualizar" class="btn">Atualizar Quantidades</button>
                <button type="submit" name="finalizar" class="btn-finalizar">Finalizar Compra</button>
                <a href="index.php" class="btn-voltar">Continuar Comprando</a>
            </div>
        </form>
    <?php endif; ?>
</div>

<div id="rodape">Desenvolvido por Anna Rebeca e Luís Fellipe © 2026</div>

<script src="js/script.js"></script>
</body>
</html>