<?php
if(!isset($_SESSION)) {
    session_start();
}
include "cons.php";
require_once "DLL.php";

$mensagem = '';
if (isset($_GET['cadastro']) && $_GET['cadastro'] == 'ok') {
    $mensagem = '<p class="af-msg-sucesso">✅ Cadastro realizado com sucesso! Faça login.</p>';
} elseif (isset($_SESSION['produto_pendente'])) {
    $mensagem = '<p class="af-msg-aviso">🔒 Faça login para adicionar o produto ao carrinho.</p>';
}

$cpf_logado   = $_SESSION['cpf_usuario'] ?? '';
$qtd_carrinho = contarCarrinho($server, $user, $password, $db, $cpf_logado);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - AnnaFell</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<?php include "header.php"; ?>

<div class="af-container">
    <div class="af-form-box">
        <h2>Login</h2>
        <?php echo $mensagem; ?>
        <form method="POST" action="processa_login.php">
            <div class="af-campo">
                <label>Login:</label>
                <input type="text" name="login" required>
            </div>
            <div class="af-campo">
                <label>Senha:</label>
                <input type="password" name="senha" required>
            </div>
            <button type="submit" class="af-btn">Entrar</button>
        </form>
        <div class="af-botoes-laterais">
            <a class="af-btn-login" href="cadastro1.php">Cadastrar novo usuário</a>
            <a class="af-btn-login" href="index.php">Voltar à loja</a>
        </div>
    </div>
</div>

<div id="af-rodape">Desenvolvido por Anna Engelhardt e Luís Brito © 2026</div>

</body>
</html>