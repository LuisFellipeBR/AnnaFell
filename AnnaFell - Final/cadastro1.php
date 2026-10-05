<?php
if(!isset($_SESSION)) {
    session_start();
}
include "cons.php";
require_once "DLL.php";

$cpf_logado   = $_SESSION['cpf_usuario'] ?? '';
$qtd_carrinho = contarCarrinho($server, $user, $password, $db, $cpf_logado);

// Flash message
$flash_msg  = '';
$flash_tipo = '';
if (isset($_SESSION['flash_cadastro'])) {
    $flash_msg  = $_SESSION['flash_cadastro']['texto'];
    $flash_tipo = $_SESSION['flash_cadastro']['tipo'];
    unset($_SESSION['flash_cadastro']);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro - Etapa 1 - AnnaFell</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<?php include "header.php"; ?>

<div class="af-container">
    <div class="af-form-box">
        <h2>Cadastro - Etapa 1</h2>

        <?php if (!empty($flash_msg)): ?>
            <div class="af-flash af-flash-<?php echo $flash_tipo; ?>" id="afFlash">
                <?php echo $flash_msg; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="salvar_usuario.php">
            <div class="af-campo">
                <label>Nome Completo:</label>
                <input type="text" name="nome" required maxlength="100">
            </div>
            <div class="af-campo">
                <label>CPF:</label>
                <input type="text" name="cpf" required placeholder="Apenas 11 números" 
                       maxlength="14" pattern="[0-9]{11}" 
                       title="Digite apenas os 11 números do CPF">
            </div>
            <div class="af-campo">
                <label>Endereço:</label>
                <input type="text" name="endereco" required maxlength="100">
            </div>
            <div class="af-campo">
                <label>Bairro:</label>
                <input type="text" name="bairro" required maxlength="50">
            </div>
            <div class="af-campo">
                <label>Cidade:</label>
                <input type="text" name="cidade" required maxlength="50">
            </div>
            <div class="af-campo">
                <label>Estado:</label>
                <input type="text" name="estado" required maxlength="2" 
                       placeholder="UF" pattern="[A-Za-z]{2}"
                       title="Digite a sigla do estado (ex: BA)">
            </div>
            <div class="af-campo">
                <label>CEP:</label>
                <input type="text" name="cep" required placeholder="Apenas 8 números"
                       maxlength="10" pattern="[0-9]{8}"
                       title="Digite apenas os 8 números do CEP">
            </div>
            <button type="submit" class="af-btn">Próxima Etapa</button>
        </form>
    </div>
</div>

<div id="af-rodape">Desenvolvido por Anna Engelhardt e Luís Brito © 2026</div>

<script>
var flash = document.getElementById('afFlash');
if (flash) {
    setTimeout(function() {
        flash.style.transition = 'opacity 0.5s';
        flash.style.opacity = '0';
        setTimeout(function() { flash.style.display = 'none'; }, 500);
    }, 5000);
}
</script>

</body>
</html>