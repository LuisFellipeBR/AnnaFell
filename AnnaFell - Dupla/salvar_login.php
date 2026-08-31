<?php
if(!isset($_SESSION)) {
    session_start();
}

include "../app/cons.php";        
require_once "../app/DLL.php";

extract($_POST);
$cpf = $_SESSION['cpf_usuario'] ?? '';

if (empty($cpf)) {
    header("Location: cadastro1.php");
    exit();
}

$sql_verifica = "SELECT login FROM logins WHERE login = '$login'";                                        //mudei
$resultado = banco($server, $user, $password, $db, $sql_verifica);                                       //mudei

if ($resultado->num_rows > 0) {
    header("Location: cadastro2.php?erro=login_existente");
    exit();
}

$senha_criptografada = md5($senha);

$sql = "INSERT INTO logins (login, senha, cpf) VALUES ('$login', '$senha_criptografada', '$cpf')";      //mudei
banco($server, $user, $password, $db, $sql);                                                            //mudei

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