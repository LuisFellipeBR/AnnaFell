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

extract($_POST); // $pagamento

$cpf = $_SESSION['cpf_usuario'];

// ---------- Verifica se ainda há itens ----------
$consulta = "SELECT COUNT(*) AS total FROM carrinho WHERE cpf_usuario = '$cpf' AND status = 'carrinho'";
$res = banco($server, $user, $password, $db, $consulta);
$row = $res->fetch_assoc();
if ($row['total'] == 0) {
    header("Location: carrinho.php");
    exit();
}

// ---------- Confere estoque de TODOS os itens ----------
$consulta = "SELECT c.id_produto, c.quantidade, p.estoque, p.nome
             FROM carrinho c
             INNER JOIN produtos p ON p.id = c.id_produto
             WHERE c.cpf_usuario = '$cpf' AND c.status = 'carrinho'";
$res = banco($server, $user, $password, $db, $consulta);

$faltando = [];
while ($linha = $res->fetch_assoc()) {
    if ($linha['quantidade'] > $linha['estoque']) {
        $faltando[] = $linha['nome'];
    }
}

if (!empty($faltando)) {
    $_SESSION['flash'] = ['tipo' => 'erro', 'texto' => '❌ Estoque insuficiente para: ' . implode(', ', $faltando)];
    header("Location: carrinho.php");
    exit();
}

// ---------- Nome completo ----------
$consulta = "SELECT nome FROM usuarios WHERE cpf = '$cpf'";
$res = banco($server, $user, $password, $db, $consulta);
$nome_completo = "";
if ($linha = $res->fetch_assoc()) {
    $nome_completo = $linha['nome'];
}

// ---------- Gera venda ----------
$numero_venda = date("YmdHis") . rand(100, 999);
$data_hora    = date("Y-m-d H:i:s");

$update = "UPDATE carrinho
           SET status = 'finalizado',
               numero_venda = '$numero_venda',
               forma_pagamento = '$pagamento',
               data_finalizacao = '$data_hora'
           WHERE cpf_usuario = '$cpf' AND status = 'carrinho'";
banco($server, $user, $password, $db, $update);

// ---------- Desconta estoque de cada produto ----------
$consulta = "SELECT id_produto, quantidade FROM carrinho
             WHERE cpf_usuario = '$cpf' AND numero_venda = '$numero_venda'";
$res = banco($server, $user, $password, $db, $consulta);

while ($linha = $res->fetch_assoc()) {
    $id_prod = (int)$linha['id_produto'];
    $qtd     = (int)$linha['quantidade'];
    banco($server, $user, $password, $db,
          "UPDATE produtos SET estoque = estoque - $qtd WHERE id = $id_prod");
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Compra Finalizada - AnnaFell</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<?php include "header.php"; ?>

<div class="af-container">
    <div class="af-form-box af-mensagem-sucesso">
        <h2>🎉 Compra Realizada com Sucesso!</h2>
        <div class="af-agradecimento">
            <p>Olá, <strong><?php echo $nome_completo; ?></strong>!</p>
            <p>Muito obrigado pela sua compra na <strong>AnnaFell</strong>.</p>
            <p>Seu pedido foi registrado com o número:
               <strong class="af-numero-venda"><?php echo $numero_venda; ?></strong></p>
            <p>Em breve você receberá um e-mail com os detalhes da entrega.</p>
            <p>🌟 Aproveite seus novos produtos e volte sempre!</p>
        </div>
        <a href="index.php" class="af-btn">Continuar Comprando</a>
    </div>
</div>

<div id="af-rodape">Desenvolvido por Anna Engelhardt e Luís Brito © 2026</div>

</body>
</html>