<?php
if(!isset($_SESSION)) {
    session_start();
}
include "cons.php";
require_once "DLL.php";

if (!isset($_SESSION['usuario_logado']) || empty($_SESSION['carrinho'])) {
    header("Location: index.php");
    exit();
}

extract($_POST); // $pagamento

$carrinho = $_SESSION['carrinho'];
$usuario = $_SESSION['usuario_logado'];
$cpf = $_SESSION['cpf_usuario'];

// Buscar nome do cliente (usando a função banco normal)
$consulta_nome = "SELECT nome FROM usuarios WHERE cpf = '$cpf'";
$resultado_nome = banco($server, $user, $password, $db, $consulta_nome);
$nome_completo = "";
if ($linha = $resultado_nome->fetch_assoc()) {
    $nome_completo = $linha['nome'];
}

$numero_venda = date("YmdHis") . rand(100, 999);
$data = date("Y-m-d");
$hora = date("H:i:s");
$total = 0;
foreach ($carrinho as $item) {
    $total += $item['preco'] * $item['quantidade'];
}

// 1. Inserir cabeçalho da venda com keep_open = true
$sql_venda = "INSERT INTO vendas (numero, cpf_cliente, data, hora, pagamento, total) 
              VALUES ('$numero_venda', '$cpf', '$data', '$hora', '$pagamento', '$total')";
$res_venda = banco($server, $user, $password, $db, $sql_venda, true); // mantém conexão aberta

// 2. Obter o ID da venda usando a conexão que ficou aberta
$id_venda = $res_venda->conn->insert_id;

// 3. Inserir os itens (usando a mesma conexão, ou podemos usar banco() normalmente,
//    mas para evitar abrir outra conexão, vamos usar a que já está aberta)
foreach ($carrinho as $item) {
    $subtotal = $item['preco'] * $item['quantidade'];
    $sql_item = "INSERT INTO vendas_itens (id_venda, produto, quantidade, preco_unitario, subtotal) 
                 VALUES ($id_venda, '{$item['nome']}', {$item['quantidade']}, {$item['preco']}, $subtotal)";
    // Executa com a mesma conexão
    if (!$res_venda->conn->query($sql_item)) {
        echo "Erro ao inserir item: " . $res_venda->conn->error;
        exit();
    }
}

// Fecha a conexão que ficou aberta
$res_venda->conn->close();

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