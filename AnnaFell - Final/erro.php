<?php
if(!isset($_SESSION)) {
    session_start();
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
    <title>Erro no Login - AnnaFell</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<?php include "header.php"; ?>

<div class="af-container">
    <div class="af-form-box af-form-box-centro">
        <h2>Erro no Login</h2>
        <p class="af-erro">❌ Usuário ou senha incorretos!</p>
        <p class="af-texto-centro">Por favor, tente novamente.</p>
        <a href="login.php" class="af-btn">Voltar para o Login</a>
    </div>
</div>

<div id="af-rodape">Desenvolvido por Anna Engelhardt e Luís Brito © 2026</div>

</body>
</html>