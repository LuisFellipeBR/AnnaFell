<?php
session_start();
include "cons.php";
require_once "DLL.php";
require_once "enviar_email.php"; 

$erro = "";

if(isset($_POST['enviar'])){
    $cpf = preg_replace('/[^0-9]/', '', $_POST['cpf']); // Limpa o CPF

    $consulta = "SELECT email FROM usuarios WHERE cpf = '$cpf'";
    $resultado = banco($server, $user, $password, $db, $consulta);

    if($linha = $resultado->fetch_assoc()){
        $email = $linha['email'];

        if(!empty($email)){
            // Gera token de 6 dígitos
            $token = rand(100000, 999999);
            $expira = date('Y-m-d H:i:s', strtotime('+1 hour')); // Expira em 1 hora

            // Salva o token no banco
            $update = "UPDATE usuarios SET token_verificacao = '$token', token_expira = '$expira' WHERE cpf = '$cpf'";
            banco($server, $user, $password, $db, $update);

            // Envia o e-mail
            if(enviarEmailRecuperacao($email, $token)){
                $_SESSION['recuperar_cpf'] = $cpf;
                header("Location: trocar_senha.php");
                exit;
            } else {
                $erro = "Erro ao enviar o e-mail. Tente novamente.";
            }
        } else {
            $erro = "Este usuário não possui um e-mail cadastrado.";
        }
    } else {
        $erro = "CPF não encontrado no sistema.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Senha - AnnaFell</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
<?php include "header.php"; ?>
<div class="af-container">
    <div class="af-form-box">
        <h2>Recuperar Senha</h2>
        <?php if(!empty($erro)): ?>
            <div class="af-flash af-flash-erro"><?php echo $erro; ?></div>
        <?php endif; ?>
        <form method="POST" action="recuperar.php">
            <div class="af-campo">
                <label>Digite seu CPF:</label>
                <input type="text" name="cpf" required placeholder="Apenas 11 números" maxlength="14" pattern="[0-9]{11}">
            </div>
            <button type="submit" name="enviar" class="af-btn">Enviar Código</button>
        </form>
        <div class="af-botoes-laterais">
            <a class="af-btn-login" href="login.php">← Voltar ao Login</a>
        </div>
    </div>
</div>
<div id="af-rodape">Desenvolvido por Anna Engelhardt e Luís Brito © 2026</div>
</body>
</html>