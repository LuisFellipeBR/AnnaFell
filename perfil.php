<?php
if(!isset($_SESSION)) {
    session_start();
}
include "cons.php";
require_once "DLL.php";

// Exige login
if (!isset($_SESSION['cpf_usuario']) || empty($_SESSION['cpf_usuario'])) {
    header("Location: nao_logado.php");
    exit();
}

$cpf = $_SESSION['cpf_usuario'];

// Busca dados do usuário
$consulta = "SELECT * FROM usuarios WHERE cpf = '$cpf'";
$res = banco($server, $user, $password, $db, $consulta);
$usuario = $res->fetch_assoc();

if (!$usuario) {
    header("Location: nao_logado.php");
    exit();
}

// Fallback da foto
$foto_padrao = "img/avatar_padrao.png";
$tem_foto    = false;
$foto_atual  = $foto_padrao;
$inicial     = strtoupper(substr($usuario['nome'], 0, 1));

if (!empty($usuario['foto']) && file_exists($usuario['foto'])) {
    $foto_atual = $usuario['foto'];
    $tem_foto   = true;
}

// Aba ativa (whitelist)
$aba = isset($_GET['aba']) ? $_GET['aba'] : 'inicio';
$abas_validas = ['inicio', 'compras', 'foto', 'ajuda'];
if (!in_array($aba, $abas_validas)) $aba = 'inicio';

// Flash message
$mensagem      = '';
$tipo_mensagem = '';
if (isset($_SESSION['flash'])) {
    $mensagem      = $_SESSION['flash']['texto'];
    $tipo_mensagem = $_SESSION['flash']['tipo'];
    unset($_SESSION['flash']);
}

// Histórico agrupado por numero_venda
$historico = [];
$consulta_hist = "SELECT c.id, c.numero_venda, c.quantidade, c.preco_unitario, c.subtotal,
                         c.forma_pagamento, c.data_finalizacao,
                         p.nome AS produto_nome, p.imagem AS produto_imagem
                  FROM carrinho c
                  INNER JOIN produtos p ON p.id = c.id_produto
                  WHERE c.cpf_usuario = '$cpf' AND c.status = 'finalizado'
                  ORDER BY c.data_finalizacao DESC, c.id DESC";
$res_hist = banco($server, $user, $password, $db, $consulta_hist);
while ($linha = $res_hist->fetch_assoc()) {
    $num = $linha['numero_venda'];
    if (!isset($historico[$num])) {
        $historico[$num] = [
            'numero'    => $num,
            'data'      => $linha['data_finalizacao'],
            'pagamento' => $linha['forma_pagamento'],
            'itens'     => [],
            'total'     => 0
        ];
    }
    $historico[$num]['itens'][] = $linha;
    $historico[$num]['total']  += $linha['subtotal'];
}
$qtd_compras = count($historico);

$cpf_logado   = $_SESSION['cpf_usuario'] ?? '';
$qtd_carrinho = contarCarrinho($server, $user, $password, $db, $cpf_logado);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meu Perfil - AnnaFell</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<?php include "header.php"; ?>

<div class="af-container">
    <div class="af-perfil-wrapper">

        <!-- ================= SIDEBAR ================= -->
        <aside class="af-perfil-sidebar">
            <div class="af-sidebar-usuario">
                <?php if ($tem_foto): ?>
                    <img src="<?php echo $foto_atual; ?>" class="af-sidebar-foto" alt="Foto">
                <?php else: ?>
                    <div class="af-sidebar-foto af-inicial-avatar"><?php echo $inicial; ?></div>
                <?php endif; ?>

                <div class="af-sidebar-nome">
                    <strong><?php echo $usuario['nome']; ?></strong>
                    <span><?php echo $usuario['cpf']; ?></span>
                </div>
            </div>

            <nav class="af-sidebar-menu">
                <a href="perfil.php?aba=inicio" class="af-sidebar-link <?php echo $aba === 'inicio' ? 'ativo' : ''; ?>">
                    <img src="img/icones/casa.png" class="af-icone-img" alt="Início"> Início
                </a>
                <a href="perfil.php?aba=compras" class="af-sidebar-link <?php echo $aba === 'compras' ? 'ativo' : ''; ?>">
                    <img src="img/icones/sacola.png" class="af-icone-img" alt="Minhas compras"> Minhas compras
                    <?php if ($qtd_compras > 0): ?>
                        <span class="af-badge"><?php echo $qtd_compras; ?></span>
                    <?php endif; ?>
                </a>
                <a href="perfil.php?aba=foto" class="af-sidebar-link <?php echo $aba === 'foto' ? 'ativo' : ''; ?>">
                    <img src="img/icones/camera.png" class="af-icone-img" alt="Editar foto"> Editar foto
                </a>
                <a href="perfil.php?aba=ajuda" class="af-sidebar-link <?php echo $aba === 'ajuda' ? 'ativo' : ''; ?>">
                    <img src="img/icones/interrogacao.png" class="af-icone-img" alt="Ajuda"> Ajuda
                </a>

                <hr class="af-sidebar-divisor">

                <a href="index.php" class="af-sidebar-link">
                    <img src="img/icones/carrinho.png" class="af-icone-img" alt="Carrinho"> Continuar comprando
                </a>
                <a href="logout.php" class="af-sidebar-link af-sidebar-sair">
                    <img src="img/icones/porta.png" class="af-icone-img" alt="Sair"> Sair
                </a>
            </nav>
        </aside>

        <!-- ================= CONTEÚDO ================= -->
        <main class="af-perfil-conteudo">

            <?php if (!empty($mensagem)): ?>
                <div class="af-flash af-flash-<?php echo $tipo_mensagem; ?>" id="afFlash">
                    <?php echo $mensagem; ?>
                </div>
            <?php endif; ?>

            <!-- ---------- ABA: INÍCIO ---------- -->
            <?php if ($aba === 'inicio'): ?>
                <div class="af-card-painel">
                    <h2>Olá, <?php echo $usuario['nome']; ?> 👋</h2>
                    <p class="af-texto-cinza">Bem-vindo ao seu painel AnnaFell.</p>

                    <div class="af-resumo-grid">
                        <div class="af-resumo-card">
                            <span class="af-resumo-icone">🛍️</span>
                            <strong><?php echo $qtd_compras; ?></strong>
                            <span>Compra<?php echo $qtd_compras != 1 ? 's' : ''; ?> realizada<?php echo $qtd_compras != 1 ? 's' : ''; ?></span>
                            <a href="perfil.php?aba=compras" class="af-resumo-link">Ver histórico →</a>
                        </div>

                        <div class="af-resumo-card">
                            <span class="af-resumo-icone">🛒</span>
                            <strong><?php echo $qtd_carrinho; ?></strong>
                            <span>Item<?php echo $qtd_carrinho != 1 ? 's' : ''; ?> no carrinho</span>
                            <a href="carrinho.php" class="af-resumo-link">Ir ao carrinho →</a>
                        </div>

                        <div class="af-resumo-card">
                            <span class="af-resumo-icone">📷</span>
                            <strong><?php echo $tem_foto ? 'Sim' : 'Não'; ?></strong>
                            <span>Foto personalizada</span>
                            <a href="perfil.php?aba=foto" class="af-resumo-link">Editar foto →</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ---------- ABA: MINHAS COMPRAS ---------- -->
            <?php if ($aba === 'compras'): ?>
                <div class="af-card-painel">
                    <h2>🛍️ Minhas compras</h2>

                    <?php if ($qtd_compras === 0): ?>
                        <div class="af-historico-vazio">
                            <p>Você ainda não realizou nenhuma compra.</p>
                            <a href="index.php" class="af-btn-inline af-btn">Explorar Produtos</a>
                        </div>
                    <?php else: ?>
                        <p class="af-historico-subtitulo">
                            <?php echo $qtd_compras; ?> pedido<?php echo $qtd_compras > 1 ? 's' : ''; ?> finalizado<?php echo $qtd_compras > 1 ? 's' : ''; ?>
                        </p>

                        <?php foreach ($historico as $compra): ?>
                            <div class="af-compra-card">
                                <div class="af-compra-header">
                                    <div class="af-compra-numero">
                                        <span class="af-compra-label">Pedido</span>
                                        <strong>#<?php echo $compra['numero']; ?></strong>
                                    </div>
                                    <div class="af-compra-data">
                                        <?php echo date('d/m/Y \à\s H:i', strtotime($compra['data'])); ?>
                                    </div>
                                    <div class="af-compra-pagamento">
                                        💳 <?php echo $compra['pagamento']; ?>
                                    </div>
                                </div>

                                <div class="af-compra-itens">
                                    <?php foreach ($compra['itens'] as $item): ?>
                                        <div class="af-compra-item">
                                            <img src="<?php echo $item['produto_imagem']; ?>" class="af-compra-item-img" alt="<?php echo $item['produto_nome']; ?>">
                                            <div class="af-compra-item-info">
                                                <strong><?php echo $item['produto_nome']; ?></strong>
                                                <span>
                                                    <?php echo $item['quantidade']; ?> ×
                                                    R$ <?php echo number_format($item['preco_unitario'], 2, ',', '.'); ?>
                                                </span>
                                            </div>
                                            <div class="af-compra-item-subtotal">
                                                R$ <?php echo number_format($item['subtotal'], 2, ',', '.'); ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="af-compra-total">
                                    <span>Total do pedido:</span>
                                    <strong>R$ <?php echo number_format($compra['total'], 2, ',', '.'); ?></strong>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- ---------- ABA: EDITAR FOTO ---------- -->
            <?php if ($aba === 'foto'): ?>
                <div class="af-card-painel af-card-painel-foto">
                    <h2>📷 Editar foto</h2>
                    <p class="af-texto-cinza af-texto-centro">
                        Envie uma imagem JPG, PNG ou GIF com no máximo 2MB.
                    </p>

                    <?php form_foto("salvar_foto.php", $foto_atual, $tem_foto); ?>
                </div>
            <?php endif; ?>

            <!-- ---------- ABA: AJUDA ---------- -->
            <?php if ($aba === 'ajuda'): ?>
                <div class="af-card-painel">
                    <h2>❓ Ajuda</h2>
                    <p class="af-texto-cinza">Precisa de suporte? Fale com a AnnaFell:</p>

                    <div class="af-ajuda-lista">
                        <div class="af-ajuda-item">
                            <span class="af-ajuda-icone">📱</span>
                            <div>
                                <strong>WhatsApp</strong>
                                <span>(73) 99999-9999</span>
                            </div>
                        </div>
                        <div class="af-ajuda-item">
                            <span class="af-ajuda-icone">📧</span>
                            <div>
                                <strong>E-mail</strong>
                                <span>contato@AnnaFell.com</span>
                            </div>
                        </div>
                        <div class="af-ajuda-item">
                            <span class="af-ajuda-icone">📷</span>
                            <div>
                                <strong>Instagram</strong>
                                <span>@AnnaFell</span>
                            </div>
                        </div>
                        <div class="af-ajuda-item">
                            <span class="af-ajuda-icone">🕐</span>
                            <div>
                                <strong>Atendimento</strong>
                                <span>Seg a Sex: 9h–18h | Sáb: 9h–13h</span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </main>
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
    }, 4000);
}
</script>

</body>
</html>