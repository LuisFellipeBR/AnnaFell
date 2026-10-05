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
    <title>Sobre Nós - AnnaFell</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<?php include "header.php"; ?>

<div class="af-container">

    <div class="af-banner-destaque">
        <h1>Sobre Nós</h1>
        <p>CONHEÇA A ANNAFELL</p>
    </div>

    <div class="af-form-box af-form-box-extra-grande">
        <h2>🎮 Nossa História</h2>

        <div class="af-conteudo-texto">
            <div class="af-bloco-destaque">
                <p>
                    A <strong class="af-destaque-texto">AnnaFell</strong> nasceu da paixão por tecnologia e games.
                    Somos uma loja especializada em <strong>hardware e periféricos</strong> para gamers e
                    entusiastas de PC que buscam qualidade, performance e preços justos.
                </p>
            </div>

            <div class="af-bloco-destaque">
                <p>
                    Nosso objetivo é simples: <strong class="af-destaque-texto">oferecer os melhores produtos
                    com atendimento de excelência</strong>. Trabalhamos com as principais marcas do mercado
                    — Redragon, HyperX, Logitech, AMD, Intel, Samsung, entre outras.
                </p>
            </div>

            <hr class="af-divisor">

            <h3 class="af-subtitulo">💡 Nossos Valores</h3>

            <p><strong class="af-destaque-texto">✔ Qualidade Garantida:</strong> Só vendemos produtos originais, com garantia.</p>
            <p><strong class="af-destaque-texto">✔ Preços Justos:</strong> Trabalhamos com as melhores condições do mercado.</p>
            <p><strong class="af-destaque-texto">✔ Atendimento Rápido:</strong> Suporte via WhatsApp, e-mail e redes sociais.</p>
            <p><strong class="af-destaque-texto">✔ Entrega para todo o Brasil:</strong> Enviamos para todas as regiões.</p>

            <hr class="af-divisor">

            <h3 class="af-subtitulo">👥 Nossa Equipe</h3>

            <p class="af-paragrafo-centro">
                <strong class="af-destaque-texto">Luís Brito</strong> — Fundador & CEO<br>
                <span class="af-texto-cinza">Responsável pela visão e estratégia da loja</span>
            </p>

            <p class="af-paragrafo-centro">
                <strong class="af-destaque-texto">Anna Engelhardt</strong> — Co-fundadora & Desenvolvedora<br>
                <span class="af-texto-cinza">Responsável pela plataforma e tecnologia</span>
            </p>

            <hr class="af-divisor">

            <p class="af-citacao-final">
                "Tecnologia que conecta. Qualidade que transforma."
            </p>
        </div>

        <div class="af-botoes-laterais">
            <a href="index.php" class="af-btn-login">← Voltar para a loja</a>
        </div>
    </div>

</div>

<div id="af-rodape">Desenvolvido por Anna Engelhardt e Luís Brito © 2026</div>

</body>
</html>