<?php
if(!isset($_SESSION)) {
    session_start();
}

include_once "cons.php";
require_once "DLL.php";

$logado = isset($_SESSION['cpf_usuario']) && !empty($_SESSION['cpf_usuario']);

$nome_header  = '';
$foto_header  = '';
$inicial      = '?';
$qtd_carrinho = 0;

if ($logado) {
    $cpf_h = $_SESSION['cpf_usuario'];
    $res_h = banco($server, $user, $password, $db,
                   "SELECT nome, foto FROM usuarios WHERE cpf = '$cpf_h'");
    if ($linha_h = $res_h->fetch_assoc()) {
        $nome_header = $linha_h['nome'];
        if (!empty($linha_h['foto']) && file_exists($linha_h['foto'])) {
            $foto_header = $linha_h['foto'];
        }
        if (!empty($nome_header)) {
            $inicial = strtoupper(substr($nome_header, 0, 1));
        }
    }
    $qtd_carrinho = contarCarrinho($server, $user, $password, $db, $cpf_h);
}
?>

<div class="af-topo-site">
    <img src="img/banner.jpg" alt="AnnaFell Banner" class="af-banner-principal">
</div>

<div class="af-navbar">
    <div class="af-nav-container">

        <div class="af-nav-links">
            <a href="index.php">Página Inicial</a>

            <div class="af-dropdown">
                <a>Produtos ▼</a>
                <div class="af-dropdown-content">
                    <div class="af-submenu">
                        <a href="index.php?categoria=perifericos">Periféricos ▶</a>
                        <div class="af-submenu-content">
                            <a href="index.php?categoria=perifericos&subcategoria=monitores">Monitores</a>
                            <a href="index.php?categoria=perifericos&subcategoria=teclados">Teclados</a>
                            <a href="index.php?categoria=perifericos&subcategoria=mouses">Mouses</a>
                            <a href="index.php?categoria=perifericos&subcategoria=headsets">Headsets</a>
                            <a href="index.php?categoria=perifericos&subcategoria=microfones">Microfones</a>
                            <a href="index.php?categoria=perifericos&subcategoria=webcams">Webcams</a>
                            <a href="index.php?categoria=perifericos&subcategoria=cadeiras">Cadeiras</a>
                        </div>
                    </div>
                    <div class="af-submenu">
                        <a href="index.php?categoria=componentes">Componentes ▶</a>
                        <div class="af-submenu-content">
                            <a href="index.php?categoria=componentes&subcategoria=processadores">Processadores</a>
                            <a href="index.php?categoria=componentes&subcategoria=placas-mae">Placas-Mãe</a>
                            <a href="index.php?categoria=componentes&subcategoria=placas-video">Placas de Vídeo</a>
                            <a href="index.php?categoria=componentes&subcategoria=memorias-ram">Memórias RAM</a>
                            <a href="index.php?categoria=componentes&subcategoria=armazenamento">Armazenamento</a>
                            <a href="index.php?categoria=componentes&subcategoria=fontes">Fontes</a>
                            <a href="index.php?categoria=componentes&subcategoria=gabinetes">Gabinetes</a>
                            <a href="index.php?categoria=componentes&subcategoria=refrigeradores">Refrigeradores</a>
                        </div>
                    </div>
                    <div class="af-submenu">
                        <a href="index.php?categoria=combos">Combos ▶</a>
                        <div class="af-submenu-content">
                            <a href="index.php?categoria=combos&subcategoria=camisetas">Camisetas</a>
                            <a href="index.php?categoria=combos&subcategoria=kits">Kits</a>
                        </div>
                    </div>
                </div>
            </div>

            <a href="carrinho.php">Carrinho <?php if($qtd_carrinho > 0) echo "($qtd_carrinho)"; ?></a>
            <a href="contato.php">Contato</a>
            <a href="sobre.php">Sobre nós</a>
        </div>

        <!-- Área do usuário dentro da navbar -->
        <div class="af-usuario-header">
            <?php if ($logado): ?>
                <a href="perfil.php" class="af-usuario-info-link" title="Meu Perfil">
                    <?php if (!empty($foto_header)): ?>
                        <img src="<?php echo $foto_header; ?>" class="af-foto-header" alt="Foto de perfil">
                    <?php else: ?>
                        <div class="af-foto-header af-inicial-avatar"><?php echo $inicial; ?></div>
                    <?php endif; ?>
                </a>

                <div class="af-info-header">
                    <a href="perfil.php" class="af-nome-header af-nome-link"><?php echo $nome_header; ?></a>
                    <a href="logout.php" class="af-link-header af-link-sair">Sair</a>
                </div>
            <?php else: ?>
                <div class="af-foto-header af-inicial-avatar af-inicial-deslogado">?</div>
                <div class="af-info-header">
                    <span class="af-nome-header af-nome-deslogado">Não logado</span>
                    <a href="login.php" class="af-link-header af-link-entrar">Entrar</a>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>