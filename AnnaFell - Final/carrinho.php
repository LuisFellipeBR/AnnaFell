<?php
if(!isset($_SESSION)) {
    session_start();
}
include "cons.php";
require_once "DLL.php";
require_once "produtos.php";

$logado = isset($_SESSION['cpf_usuario']) && isset($_SESSION['usuario_logado']);
$cpf    = $logado ? $_SESSION['cpf_usuario'] : '';

$subcategorias_por_categoria = [
    'perifericos' => [
        'monitores' => 'Monitores', 'teclados' => 'Teclados', 'mouses' => 'Mouses',
        'headsets' => 'Headsets', 'microfones' => 'Microfones', 'webcams' => 'Webcams',
        'cadeiras' => 'Cadeiras'
    ],
    'componentes' => [
        'processadores' => 'Processadores', 'placas-mae' => 'Placas-Mãe',
        'placas-video' => 'Placas de Vídeo', 'memorias-ram' => 'Memórias RAM',
        'armazenamento' => 'Armazenamento', 'fontes' => 'Fontes',
        'gabinetes' => 'Gabinetes', 'refrigeradores' => 'Refrigeradores'
    ],
    'combos' => [
        'camisetas' => 'Camisetas', 'kits' => 'Kits'
    ]
];

// ---------- Adicionar sem login (POST) ----------
if (isset($_POST['adicionar']) && !$logado) {
    $_SESSION['produto_pendente'] = (int)$_POST['adicionar'];
    header("Location: nao_logado.php");
    exit();
}

// ---------- Adicionar (logado) com validação de estoque (POST) ----------
if (isset($_POST['adicionar']) && $logado) {
    $id_produto = (int)$_POST['adicionar'];

    $res_p = banco($server, $user, $password, $db,
                   "SELECT preco, estoque FROM produtos WHERE id = $id_produto AND ativo = 1");

    if ($linha = $res_p->fetch_assoc()) {
        $preco   = $linha['preco'];
        $estoque = (int)$linha['estoque'];

        if ($estoque <= 0) {
            $_SESSION['flash'] = ['tipo' => 'erro', 'texto' => '❌ Produto esgotado. Não é possível adicionar.'];
            header("Location: index.php");
            exit();
        }

        $res_b = banco($server, $user, $password, $db,
                       "SELECT id, quantidade FROM carrinho
                        WHERE cpf_usuario = '$cpf' AND id_produto = $id_produto AND status = 'carrinho'");

        if ($item = $res_b->fetch_assoc()) {
            $nova_qtd = $item['quantidade'] + 1;

            if ($nova_qtd > $estoque) {
                $_SESSION['flash'] = ['tipo' => 'erro', 'texto' => '⚠️ Limite atingido. Só há ' . $estoque . ' unidades em estoque.'];
                header("Location: carrinho.php");
                exit();
            }

            banco($server, $user, $password, $db,
                  "UPDATE carrinho SET quantidade = $nova_qtd WHERE id = {$item['id']}");
        } else {
            banco($server, $user, $password, $db,
                  "INSERT INTO carrinho (cpf_usuario, id_produto, quantidade, preco_unitario, status)
                   VALUES ('$cpf', $id_produto, 1, $preco, 'carrinho')");
        }
    }
    header("Location: carrinho.php");
    exit();
}

// ---------- Remover (POST) ----------
if (isset($_POST['remover']) && $logado) {
    $id_carrinho = (int)$_POST['remover'];
    banco($server, $user, $password, $db,
          "DELETE FROM carrinho WHERE id = $id_carrinho AND cpf_usuario = '$cpf' AND status = 'carrinho'");
    header("Location: carrinho.php");
    exit();
}

// ---------- Atualizar (POST) ----------
if (isset($_POST['atualizar']) && $logado) {
    foreach ($_POST['quantidade'] as $id_carrinho => $qtd) {
        $id_carrinho = (int)$id_carrinho;
        $qtd = (int)$qtd;

        if ($qtd <= 0) {
            banco($server, $user, $password, $db,
                  "DELETE FROM carrinho WHERE id = $id_carrinho AND cpf_usuario = '$cpf'");
        } else {
            $res_e = banco($server, $user, $password, $db,
                "SELECT p.estoque FROM carrinho c
                 INNER JOIN produtos p ON p.id = c.id_produto
                 WHERE c.id = $id_carrinho AND c.cpf_usuario = '$cpf'");
            if ($linha_e = $res_e->fetch_assoc()) {
                $estoque = (int)$linha_e['estoque'];
                if ($qtd > $estoque) $qtd = $estoque;
            }
            banco($server, $user, $password, $db,
                  "UPDATE carrinho SET quantidade = $qtd WHERE id = $id_carrinho AND cpf_usuario = '$cpf'");
        }
    }
    header("Location: carrinho.php");
    exit();
}

// ---------- Finalizar (POST) ----------
if (isset($_POST['finalizar']) && $logado) {
    header("Location: confirmar.php");
    exit();
}

// ---------- Flash ----------
$flash_msg  = '';
$flash_tipo = '';
if (isset($_SESSION['flash'])) {
    $flash_msg  = $_SESSION['flash']['texto'];
    $flash_tipo = $_SESSION['flash']['tipo'];
    unset($_SESSION['flash']);
}

// ---------- Listar itens ----------
$itens = [];
$total = 0;
if ($logado) {
    $consulta = "SELECT c.id AS id_carrinho, c.id_produto, c.quantidade, c.preco_unitario, c.subtotal,
                        p.nome, p.imagem, p.estoque
                 FROM carrinho c
                 INNER JOIN produtos p ON p.id = c.id_produto
                 WHERE c.cpf_usuario = '$cpf' AND c.status = 'carrinho'
                 ORDER BY c.adicionado_em DESC";
    $res = banco($server, $user, $password, $db, $consulta);
    while ($linha = $res->fetch_assoc()) {
        $itens[] = $linha;
        $total  += $linha['subtotal'];
    }
}

$qtd_total = 0;
foreach ($itens as $it) { $qtd_total += $it['quantidade']; }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meu Carrinho - AnnaFell</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<?php include "header.php"; ?>

<div class="af-container">
    <h1 class="af-secao-titulo">Meu Carrinho</h1>

    <?php if (!empty($flash_msg)): ?>
        <div class="af-flash af-flash-<?php echo $flash_tipo; ?>" id="afFlash">
            <?php echo $flash_msg; ?>
        </div>
    <?php endif; ?>

    <?php if (!$logado): ?>
        <div class="af-carrinho-vazio">
            <p>Você precisa estar logado para ver o carrinho.</p>
            <a href="login.php" class="af-btn">Fazer Login</a>
        </div>
    <?php elseif (empty($itens)): ?>
        <div class="af-carrinho-vazio">
            <p>Seu carrinho está vazio.</p>
            <a href="index.php" class="af-btn">Continuar Comprando</a>
        </div>
    <?php else: ?>
        <form method="POST">
            <div class="af-carrinho-tabela">
                <div class="af-carrinho-linha af-cabecalho">
                    <div class="af-celula-produto">Produto</div>
                    <div class="af-celula-preco">Preço</div>
                    <div class="af-celula-quantidade">Quantidade</div>
                    <div class="af-celula-subtotal">Subtotal</div>
                    <div class="af-celula-acao"></div>
                </div>
                <?php foreach ($itens as $item): 
                    $est = textoEstoque($item['estoque']);
                ?>
                <div class="af-carrinho-linha">
                    <div class="af-celula-produto">
                        <img src="<?php echo $item['imagem']; ?>" class="af-carrinho-img">
                        <div>
                            <?php echo $item['nome']; ?>
                            <div class="af-estoque af-estoque-mini <?php echo $est['classe']; ?>">
                                <?php echo $est['texto']; ?>
                            </div>
                        </div>
                    </div>
                    <div class="af-celula-preco">R$ <?php echo number_format($item['preco_unitario'], 2, ',', '.'); ?></div>
                    <div class="af-celula-quantidade">
                        <input type="number" name="quantidade[<?php echo $item['id_carrinho']; ?>]"
                               value="<?php echo $item['quantidade']; ?>"
                               min="1"
                               max="<?php echo $item['estoque']; ?>"
                               class="af-input-quantidade">
                    </div>
                    <div class="af-celula-subtotal">R$ <?php echo number_format($item['subtotal'], 2, ',', '.'); ?></div>
                    <div class="af-celula-acao">
                        <button type="submit" name="remover" value="<?php echo $item['id_carrinho']; ?>" class="af-btn-remover">Remover</button>
                    </div>
                </div>
                <?php endforeach; ?>
                <div class="af-carrinho-total">
                    <div>Total:</div>
                    <div class="af-total-valor">R$ <?php echo number_format($total, 2, ',', '.'); ?></div>
                </div>
            </div>
            <div class="af-carrinho-acoes">
                <button type="submit" name="atualizar" class="af-btn af-btn-auto">Atualizar Quantidades</button>
                <button type="submit" name="finalizar" class="af-btn-finalizar">Finalizar Compra</button>
                <a href="index.php" class="af-btn-voltar">Continuar Comprando</a>
            </div>
        </form>
    <?php endif; ?>
</div>

<div id="af-rodape">Desenvolvido por Anna Engelhardt e Luís Brito © 2026</div>

<script>
var flash = document.getElementById('afFlash');
if (flash) {
    setTimeout(function() {
        flash.style.transition = 'opacity 0.5s';
        flash.style.opacity = '0';
        setTimeout(function() { flash.style.display = 'none'; }, 4000);
    }, 4000);
}
</script>

</body>
</html>