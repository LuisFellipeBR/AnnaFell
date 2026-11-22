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
    <title>Contato - AnnaFell</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<?php include "header.php"; ?>

<div class="af-container">

    <div class="af-banner-destaque">
        <h1>📞 Contato</h1>
        <p>ESTAMOS AQUI PARA AJUDAR VOCÊ</p>
    </div>

    <div class="af-form-box af-form-box-grande">
        <h2>Fale Conosco</h2>

        <div class="af-conteudo-texto">
            <p><strong class="af-destaque-texto">📱 WhatsApp:</strong> (73) 99999-9999</p>
            <p><strong class="af-destaque-texto">📧 E-mail:</strong> contato@AnnaFell.com</p>
            <p><strong class="af-destaque-texto">📷 Instagram:</strong> @AnnaFell</p>
            <p><strong class="af-destaque-texto">🌐 Site:</strong> www.AnnaFell.com.br</p>

            <hr class="af-divisor">

            <p><strong class="af-destaque-texto">🕐 Horário de Atendimento:</strong></p>
            <p>Segunda a Sexta: 9h às 18h</p>
            <p>Sábado: 9h às 13h</p>
            <p>Domingo e feriados: Fechado</p>

            <hr class="af-divisor">

            <p><strong class="af-destaque-texto">📍 Endereço:</strong></p>
            <p>Rua dos Gamers, 123 - Centro</p>
            <p>Vitória da Conquista - BA</p>
        </div>

        <div class="af-botoes-laterais">
            <a href="index.php" class="af-btn-login">← Voltar para a loja</a>
        </div>
    </div>

</div>

<div id="af-rodape">Desenvolvido por Anna Engelhardt e Luís Brito © 2026</div>

</body>
</html>