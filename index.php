<?php
if(!isset($_SESSION)) {
    session_start();
}
include "cons.php";
require_once "DLL.php";
require_once 'produtos.php';

$cpf_logado   = $_SESSION['cpf_usuario'] ?? '';
$qtd_carrinho = contarCarrinho($server, $user, $password, $db, $cpf_logado);

$produtos = getProdutos($server, $user, $password, $db);

$subcategorias_por_categoria = [
    'perifericos' => [
        'monitores'  => 'Monitores',  'teclados'   => 'Teclados',
        'mouses'     => 'Mouses',     'headsets'   => 'Headsets',
        'microfones' => 'Microfones', 'webcams'    => 'Webcams',
        'cadeiras'   => 'Cadeiras'
    ],
    'componentes' => [
        'processadores'  => 'Processadores', 'placas-mae'     => 'Placas-Mãe',
        'placas-video'   => 'Placas de Vídeo', 'memorias-ram'   => 'Memórias RAM',
        'armazenamento'  => 'Armazenamento', 'fontes'         => 'Fontes',
        'gabinetes'      => 'Gabinetes',     'refrigeradores' => 'Refrigeradores'
    ],
    'combos' => [
        'camisetas' => 'Camisetas', 'kits' => 'Kits'
    ]
];

$nomes_categorias = [
    'perifericos' => 'Periféricos',
    'componentes' => 'Componentes',
    'combos'      => 'Combos'
];

function imagemExiste($caminho) {
    return file_exists(__DIR__ . '/' . $caminho);
}

$categoria_filtro    = isset($_GET['categoria'])    ? trim($_GET['categoria'])    : '';
$subcategoria_filtro = isset($_GET['subcategoria']) ? trim($_GET['subcategoria']) : '';

$nivel = 1;
if (!empty($subcategoria_filtro)) $nivel = 3;
elseif (!empty($categoria_filtro)) $nivel = 2;

$titulo = '';
if ($nivel === 3) {
    $produtos = array_filter($produtos, function($p) use ($subcategoria_filtro) {
        return isset($p['subcategoria']) && $p['subcategoria'] === $subcategoria_filtro;
    });
    $titulo = $subcategorias_por_categoria[$categoria_filtro][$subcategoria_filtro] ?? 'Produtos';
} elseif ($nivel === 2) {
    $titulo = $nomes_categorias[$categoria_filtro] ?? 'Categoria';
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>AnnaFell - Loja Virtual</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<?php include "header.php"; ?>

<div class="af-container">

    <div class="af-banner-destaque">
        <h1>AnnaFell</h1>
        <p>TECNOLOGIA QUE CONECTA. QUALIDADE QUE TRANSFORMA.</p>
    </div>

    <?php if ($nivel === 1): ?>
        <div class="af-categorias-grid">
            <?php 
            $cats_principais = [
                'perifericos' => 'Periféricos',
                'componentes' => 'Componentes',
                'combos'      => 'Combos'
            ];
            foreach ($cats_principais as $slug => $nome): 
                $img = "img/cat-$slug.jpg";
            ?>
                <a href="index.php?categoria=<?php echo $slug; ?>" class="af-link-limpo">
                    <div class="af-categoria-card">
                        <?php if (imagemExiste($img)): ?>
                            <img src="<?php echo $img; ?>" alt="<?php echo $nome; ?>">
                        <?php endif; ?>
                        <h3><?php echo $nome; ?></h3>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <h2 class="af-secao-titulo-destaque">🔥 Mais Vendidos</h2>
        <div class="af-produtos">
            <?php 
            $produtos_destaque = [];
            $todas_subcats = [];
            foreach ($subcategorias_por_categoria as $cat => $subs) {
                foreach ($subs as $slug => $nome) {
                    $todas_subcats[] = ['categoria' => $cat, 'subcategoria' => $slug];
                }
            }
            foreach ($todas_subcats as $alvo) {
                foreach ($produtos as $id => $produto) {
                    if (isset($produto['categoria']) && $produto['categoria'] === $alvo['categoria']
                        && isset($produto['subcategoria']) && $produto['subcategoria'] === $alvo['subcategoria']) {
                        $produtos_destaque[] = ['id' => $id, 'produto' => $produto];
                        break;
                    }
                }
            }
            foreach ($produtos_destaque as $item): 
                $id = $item['id'];
                $produto = $item['produto'];
                $est = textoEstoque($produto['estoque']);
            ?>
                <div class="af-produto">
                    <img src="<?php echo $produto['imagem']; ?>" class="af-produto-img" alt="<?php echo $produto['nome']; ?>">
                    <h3><?php echo $produto['nome']; ?></h3>
                    <p><?php echo $produto['descricao']; ?></p>

                    <span class="af-estoque <?php echo $est['classe']; ?>">
                        <?php echo $est['texto']; ?>
                    </span>

                    <p class="af-preco">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>

                    <?php if ($produto['estoque'] > 0): ?>
                        <form method="POST" action="carrinho.php" class="af-form-comprar">
                            <input type="hidden" name="adicionar" value="<?php echo $id; ?>">
                            <button type="submit" class="af-btn-comprar">Comprar</button>
                        </form>
                    <?php else: ?>
                        <span class="af-btn-comprar af-btn-esgotado">Indisponível</span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

    <?php elseif ($nivel === 2): ?>
        <h2 class="af-secao-titulo"><?php echo $titulo; ?></h2>
        <div class="af-texto-centro">
            <a href="index.php" class="af-btn af-btn-inline">← Voltar às categorias</a>
        </div>
        <?php 
        $subs = $subcategorias_por_categoria[$categoria_filtro] ?? [];
        if (empty($subs)): 
        ?>
            <div class="af-carrinho-vazio">
                <p>Nenhuma subcategoria cadastrada.</p>
            </div>
        <?php else: ?>
            <div class="af-categorias-grid">
                <?php foreach ($subs as $slug => $nome): 
                    $img = "img/sub-$slug.jpg";
                ?>
                    <a href="index.php?categoria=<?php echo $categoria_filtro; ?>&subcategoria=<?php echo $slug; ?>" class="af-link-limpo">
                        <div class="af-categoria-card">
                            <?php if (imagemExiste($img)): ?>
                                <img src="<?php echo $img; ?>" alt="<?php echo $nome; ?>">
                            <?php endif; ?>
                            <h3><?php echo $nome; ?></h3>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <h2 class="af-secao-titulo"><?php echo $titulo; ?></h2>
        <div class="af-texto-centro">
            <a href="index.php?categoria=<?php echo $categoria_filtro; ?>" class="af-btn af-btn-inline">← Voltar para <?php echo $nomes_categorias[$categoria_filtro] ?? 'categoria'; ?></a>
        </div>
        <div class="af-produtos">
            <?php if (empty($produtos)): ?>
                <div class="af-carrinho-vazio">
                    <p>Nenhum produto nessa subcategoria.</p>
                </div>
            <?php else: ?>
                <?php foreach ($produtos as $id => $produto): 
                    $est = textoEstoque($produto['estoque']);
                ?>
                    <div class="af-produto">
                        <img src="<?php echo $produto['imagem']; ?>" class="af-produto-img" alt="<?php echo $produto['nome']; ?>">
                        <h3><?php echo $produto['nome']; ?></h3>
                        <p><?php echo $produto['descricao']; ?></p>

                        <span class="af-estoque <?php echo $est['classe']; ?>">
                            <?php echo $est['texto']; ?>
                        </span>

                        <p class="af-preco">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>

                        <?php if ($produto['estoque'] > 0): ?>
                            <form method="POST" action="carrinho.php" class="af-form-comprar">
                                <input type="hidden" name="adicionar" value="<?php echo $id; ?>">
                                <button type="submit" class="af-btn-comprar">Comprar</button>
                            </form>
                        <?php else: ?>
                            <span class="af-btn-comprar af-btn-esgotado">Indisponível</span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</div>

<div id="af-rodape">Desenvolvido por Anna Engelhardt e Luís Brito © 2026</div>

</body>
</html>