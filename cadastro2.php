<?php
if(!isset($_SESSION)) {
    session_start();
}

if(!isset($_SESSION['cpf_usuario'])) {
    header("Location: cadastro1.php");
    exit();
}

include "cons.php";
require_once "DLL.php";
$cpf_logado   = $_SESSION['cpf_usuario'] ?? '';
$qtd_carrinho = contarCarrinho($server, $user, $password, $db, $cpf_logado);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro - Etapa 2 - AnnaFell</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<?php include "header.php"; ?>

<div class="af-container">
    <div class="af-form-box">
        <h2>Cadastro de Login - Etapa 2</h2>

        <form method="POST" action="salvar_login.php">
            <div class="af-campo">
                <label>Login:</label>
                <input type="text" name="login" required>
            </div>
            <div class="af-campo">
                <label>Senha:</label>
                <input type="password" name="senha" required>
            </div>
            <button type="submit" class="af-btn">Finalizar Cadastro</button>
        </form>
    </div>
</div>

<div id="af-rodape">Desenvolvido por Anna Engelhardt e Luís Brito © 2026</div>

</body>
</html>