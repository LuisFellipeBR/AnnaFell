<?php
if(!isset($_SESSION)) {
    session_start();
}
include "cons.php";
require_once "DLL.php";

if (!isset($_SESSION['usuario_logado'])) {
    header("Location: nao_logado.php");
    exit();
}

$usuario = $_SESSION['usuario_logado'];
$cpf     = $_SESSION['cpf_usuario'];

$consulta = "SELECT nome FROM usuarios WHERE cpf = '$cpf'";
$res = banco($server, $user, $password, $db, $consulta);
$nome_usuario = "";
if ($linha = $res->fetch_assoc()) {
    $nome_usuario = $linha['nome'];
}

$itens = [];
$total = 0;
$tem_estoque_insuficiente = false;

$consulta = "SELECT c.id AS id_carrinho, c.id_produto, c.quantidade, c.preco_unitario, c.subtotal,
                    p.nome, p.imagem, p.estoque
             FROM carrinho c
             INNER JOIN produtos p ON p.id = c.id_produto
             WHERE c.cpf_usuario = '$cpf' AND c.status = 'carrinho'
             ORDER BY c.adicionado_em DESC";
$res = banco($server, $user, $password, $db, $consulta);
while ($linha = $res->fetch_assoc()) {
    if ($linha['quantidade'] > $linha['estoque']) {
        $tem_estoque_insuficiente = true;
    }
    $itens[] = $linha;
    $total  += $linha['subtotal'];
}

if (empty($itens)) {
    header("Location: carrinho.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Confirmar Compra - AnnaFell</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<?php include "header.php"; ?>

<div class="af-container">
    <div class="af-form-box af-form-box-grande">
        <h2>✅ Confirmação da Compra</h2>

        <div class="af-info-usuario">
            <p><strong class="af-destaque-texto">Usuário:</strong> <?php echo $nome_usuario; ?></p>
            <p><strong class="af-destaque-texto">Login:</strong> <?php echo $usuario; ?></p>
        </div>

        <p class="af-destaque-texto af-negrito">Itens do pedido:</p>
        <div class="af-itens-confirmacao">
            <?php foreach($itens as $item): ?>
            <div class="af-item-confirmacao">
                <img src="<?php echo $item['imagem']; ?>" class="af-item-img-confirmacao">
                <div>
                    <strong><?php echo $item['nome']; ?></strong><br>
                    Quantidade: <?php echo $item['quantidade']; ?><br>
                    Preço unitário: R$ <?php echo number_format($item['preco_unitario'], 2, ',', '.'); ?><br>
                    Subtotal: R$ <?php echo number_format($item['subtotal'], 2, ',', '.'); ?>

                    <?php if ($item['quantidade'] > $item['estoque']): ?>
                        <p class="af-estoque af-estoque-esgotado af-estoque-bloco">
                            ❌ Estoque insuficiente (disponível: <?php echo $item['estoque']; ?>)
                        </p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <p class="af-total-final"><strong>Total: R$ <?php echo number_format($total, 2, ',', '.'); ?></strong></p>

        <?php if ($tem_estoque_insuficiente): ?>
            <div class="af-flash af-flash-erro">
                ⚠️ Um ou mais produtos não têm estoque suficiente. Volte ao carrinho para ajustar.
            </div>
            <a href="carrinho.php" class="af-btn">← Voltar ao carrinho</a>
        <?php else: ?>
            <form method="POST" action="salvar_venda.php">
                <div class="af-campo">
                    <label>Forma de Pagamento:</label>
                    <select name="pagamento" required>
                        <option value="">Selecione...</option>
                        <option value="Cartão de Crédito">Cartão de Crédito</option>
                        <option value="Cartão de Débito">Cartão de Débito</option>
                        <option value="Boleto Bancário">Boleto Bancário</option>
                        <option value="Pix">Pix</option>
                    </select>
                </div>
                <button type="submit" class="af-btn">Confirmar Compra</button>
            </form>
        <?php endif; ?>

        <div class="af-botoes-laterais">
            <a class="af-btn-login" href="carrinho.php">← Voltar ao carrinho</a>
            <a class="af-btn-login" href="index.php">Continuar comprando</a>
        </div>
    </div>
</div>

<div id="af-rodape">Desenvolvido por Anna Engelhardt e Luís Brito © 2026</div>

</body>
</html>