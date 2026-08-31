<?php
if(!isset($_SESSION)) {
    session_start();
}

if(!isset($_SESSION['usuario_logado']) || empty($_SESSION['carrinho'])) {
    header("Location: index.php");
    exit();
}

extract($_POST);  // cria $pagamento

$carrinho = $_SESSION['carrinho'];
$usuario = $_SESSION['usuario_logado'];
$cpf = $_SESSION['cpf_usuario'];

$arquivo_usuario = "usuarios/" . $cpf . ".dat";
$nome_completo = "";
if(file_exists($arquivo_usuario)) {
    $arq = fopen($arquivo_usuario, "r");
    if ($arq) {
        $nome_completo = trim(fgets($arq));                                         // primeira linha
        fclose($arq);
    }
}

$numero_venda = date("YmdHis") . rand(100, 999);
$data = date("d/m/Y");
$hora = date("H:i:s");

$dados = "Número da Venda: $numero_venda\n";
$dados .= "Nome do Usuário: $nome_completo\n";
$dados .= "Data: $data\n";
$dados .= "Hora: $hora\n";
$dados .= "Forma de Pagamento: $pagamento\n";
$dados .= "Itens:\n";
$total = 0;
foreach($carrinho as $item) {
    $subtotal = $item['preco'] * $item['quantidade'];
    $total += $subtotal;
    $dados .= "- " . $item['nome'] . " | Quantidade: " . $item['quantidade'] . " | Preço unitário: R$ " . number_format($item['preco'], 2, ',', '.') . " | Subtotal: R$ " . number_format($subtotal, 2, ',', '.') . "\n";
}
$dados .= "Total da compra: R$ " . number_format($total, 2, ',', '.') . "\n";

$arquivo = "vendas/venda_" . $numero_venda . ".dat";
$arq = fopen($arquivo, "w");
fwrite($arq, $dados);
fclose($arq);

unset($_SESSION['carrinho']);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Compra Finalizada</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

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
    <div class="form-box mensagem-sucesso">
        <h2>🎉 Compra Realizada com Sucesso!</h2>
        <div class="agradecimento">
            <p>Olá, <strong><?php echo $nome_completo; ?></strong>!</p>
            <p>Muito obrigado pela sua compra na <strong>AnnaFell</strong>.</p>
            <p>Seu pedido foi registrado com o número: <strong class="numero-venda"><?php echo $numero_venda; ?></strong></p>
            <p> Em breve você receberá um e-mail com os detalhes da entrega.</p>
            <p>🌟 Aproveite seus novos produtos e volte sempre!</p>
        </div>
        <a href="index.php" class="btn">Continuar Comprando</a>
    </div>
</div>

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