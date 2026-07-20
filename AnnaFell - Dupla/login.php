<?php
if(!isset($_SESSION)) {
    session_start();
}

// Mensagem de sucesso após cadastro
$mensagem = '';
if (isset($_GET['cadastro']) && $_GET['cadastro'] == 'ok') {
    $mensagem = '<p style="color: green; text-align: center;">Cadastro realizado com sucesso! Faça login.</p>';
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <div class="container">
        <div class="form-box">
            <h2>Login</h2>
            <?php echo $mensagem; ?>
            <form method="POST" action="processa_login.php">
                <div class="campo">
                    <label>Login:</label>
                    <input type="text" name="login" required>
                </div>
                <div class="campo">
                    <label>Senha:</label>
                    <input type="password" name="senha" required>
                </div>
                <button type="submit" class="btn">Entrar</button> <!-- Vai para processa login -->
            </form>
            <p class="link-cadastro">
                <br>
                <a class="btn-login" href="cadastro1.php">Cadastrar novo usuário</a> <br> <br> 
                <a class="btn-login" href="index.php">Voltar à loja</a>
            </p>
        </div>
    </div>
</body>
</html>