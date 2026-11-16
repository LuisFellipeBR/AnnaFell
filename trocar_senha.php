<?php
session_start();
include "cons.php";
require_once "DLL.php";

if(!isset($_SESSION['recuperar_cpf'])){
    header("Location: login.php");
    exit;
}

$erro = "";

if(isset($_POST['trocar'])){
    $token = $_POST['token'];
    $nova_senha = md5($_POST['nova_senha']); // Criptografa a nova senha em MD5
    $cpf = $_SESSION['recuperar_cpf'];

    // Verifica se o token bate e se não expirou
    $consulta = "SELECT cpf FROM usuarios WHERE cpf = '$cpf' AND token_verificacao = '$token' AND token_expira > NOW()";
    $resultado = banco($server, $user, $password, $db, $consulta);

    if($linha = $resultado->fetch_assoc()){
        // Atualiza a senha na tabela logins
        $update = "UPDATE logins SET senha = '$nova_senha' WHERE cpf = '$cpf'";
        banco($server, $user, $password, $db, $update);

        // Limpa o token para não ser reutilizado
        $limpar = "UPDATE usuarios SET token_verificacao = NULL, token_expira = NULL WHERE cpf = '$cpf'";
        banco($server, $user, $password, $db, $limpar);

        unset($_SESSION['recuperar_cpf']);
        header("Location: login.php?senha_alterada=ok");
        exit;
    } else {
        $erro = "Código inválido ou expirado! Tente novamente.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Nova Senha - AnnaFell</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
<?php include "header.php"; ?>
<div class="af-container">
    <div class="af-form-box">
        <h2>Nova Senha</h2>
        <?php if(!empty($erro)): ?>
            <div class="af-flash af-flash-erro"><?php echo $erro; ?></div>
        <?php endif; ?>
        <form method="POST" action="trocar_senha.php">
            <div class="af-campo">
                <label>Código de 6 dígitos (enviado por e-mail):</label>
                <input type="text" name="token" required maxlength="6" placeholder="Ex: 123456">
            </div>
            <div class="af-campo">
                <label>Nova Senha:</label>
                <input type="password" name="nova_senha" required placeholder="Digite a nova senha">
            </div>
            <button type="submit" name="trocar" class="af-btn">Alterar Senha</button>
        </form>
    </div>
</div>
<div id="af-rodape">Desenvolvido por Anna Engelhardt e Luís Brito © 2026</div>
</body>
</html>