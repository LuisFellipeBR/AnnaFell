<?php
if(!isset($_SESSION)) {
    session_start();
}

extract($_POST);
$cpf = $_SESSION['cpf_usuario'] ?? '';

if (empty($cpf)) {
    header("Location: cadastro1.php");
    exit();
}

$senha_criptografada = md5($senha);
$arquivo = "login/" . $login . ".dat";
$arq = fopen($arquivo, "w");
fwrite($arq, $login . "\n");
fwrite($arq, $senha_criptografada . "\n");
fwrite($arq, $cpf . "\n");
fclose($arq);

unset($_SESSION['cpf_usuario']);
header("Location: login.php?cadastro=ok");
exit();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Venda Confirmada</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <div class="container">
        <div class="form-box">
            <h2>Compra Realizada com Sucesso!</h2>
            <div class="info-compra">
                <p><strong>Número da Venda:</strong> <?php echo $numero_venda; ?></p>
                <p><strong>Produto:</strong> <?php echo $produto['nome']; ?></p>
                <p><strong>Valor:</strong> R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                <p><strong>Forma de Pagamento:</strong> <?php echo $pagamento; ?></p>
            </div>
            <a href="index.php" class="btn">Voltar para Loja</a>
        </div>
    </div>
</body>
</html>