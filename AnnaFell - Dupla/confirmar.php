<?php
if(!isset($_SESSION)) {
    session_start();
}

if(!isset($_SESSION['usuario_logado']) || empty($_SESSION['carrinho'])) {
    header("Location: login.php");
    exit();
}

$carrinho = $_SESSION['carrinho'];
$usuario = $_SESSION['usuario_logado'];
$cpf = $_SESSION['cpf_usuario'];

$arquivo_usuario = "usuarios/" . $cpf . ".dat";
$nome_usuario = "";

if(file_exists($arquivo_usuario)) {
    $arq = fopen($arquivo_usuario, "r");
    if ($arq) {
        $nome_usuario = trim(fgets($arq)); // primeira linha = nome completo
        fclose($arq);
    }
}

$total = 0;
foreach($carrinho as $item) {
    $total += $item['preco'] * $item['quantidade'];
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Confirmar Compra</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <div class="nav-container">
        <a href="index.php" class="nav-logo">AnnaFell</a>
        <div class="nav-links">
            <a href="index.php">Produtos</a>
            <a href="carrinho.php">Carrinho</a>
            <a href="logout.php">Sair</a>
        </div>
    </div>
</div>

<div class="container">
    <div class="form-box confirmacao-box">
        <h2>✅ Confirmação da Compra</h2>
        <div class="info-compra">
            <p><strong>Usuário:</strong> <?php echo $nome_usuario; ?></p>
            <p><strong>Login:</strong> <?php echo $usuario; ?></p>
            <p><strong>Itens do pedido:</strong></p>
            <div class="itens-confirmacao">
                <?php foreach($carrinho as $item): ?>
                <div class="item-confirmacao">
                    <img src="<?php echo $item['imagem']; ?>" class="item-img-confirmacao">
                    <div class="item-detalhes">
                        <strong><?php echo $item['nome']; ?></strong><br>
                        Quantidade: <?php echo $item['quantidade']; ?><br>
                        Preço unitário: R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?><br>
                        Subtotal: R$ <?php echo number_format($item['preco'] * $item['quantidade'], 2, ',', '.'); ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <p class="total-final"><strong>Total: R$ <?php echo number_format($total, 2, ',', '.'); ?></strong></p>
        </div>
        <form method="POST" action="salvar_venda.php">
            <div class="campo">
                <label>Forma de Pagamento:</label>
                <select name="pagamento" required>
                    <option value="">Selecione...</option>
                    <option value="Cartão de Crédito">Cartão de Crédito</option>
                    <option value="Cartão de Débito">Cartão de Débito</option>
                    <option value="Boleto Bancário">Boleto Bancário</option>
                    <option value="Pix">Pix</option>
                </select>
            </div>
            <button type="submit" class="btn">Confirmar Compra</button>
            <div class="links-aux">
                <a class="btn-login" href="carrinho.php">← Voltar ao carrinho</a>
                <a class="btn-login" href="index.php">Continuar comprando</a>
            </div>
        </form>
    </div>
</div>

<!-- Contato flutuante -->
<div class="contato-flutuante">
    <div class="contato-conteudo">
        <strong>📞 Fale conosco</strong><br>
        WhatsApp: (73) 99999-9999<br>
        E-mail: contato@AnnaFell.com
    </div>
</div>

<div id="rodape">Desenvolvido por Anna Rebeca e Luís Fellipe © 2026</div>

<script src="js/script.js"></script>
</body>
</html>