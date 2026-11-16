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
    <title>Não logado - AnnaFell</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<?php include "header.php"; ?>

<div class="af-container">
    <div class="af-form-box af-form-box-centro">
        <h2>🔒 Nenhum usuário logado</h2>
        <p class="af-texto-centro">Esta área exige autenticação.</p>
        <p class="af-texto-centro">Faça login ou cadastre-se para continuar.</p>

        <div class="af-acoes-nao-logado">
            <a href="login.php" class="af-btn">Fazer Login</a>
            <a href="cadastro1.php" class="af-btn-secundario">Cadastrar-se</a>
            <a href="index.php" class="af-btn-terciario">← Voltar para a loja</a>
        </div>
    </div>
</div>

<div id="af-rodape">Desenvolvido por Anna Engelhardt e Luís Brito © 2026</div>

</body>
</html>