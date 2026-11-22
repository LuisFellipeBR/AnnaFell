<?php
if(!isset($_SESSION)) { session_start(); }
include "cons.php";
require_once "DLL.php";

// ============================================================
// BLOQUEIO: só admin entra
// ============================================================
if (empty($_SESSION['admin'])) {
    header("Location: nao_logado.php");
    exit();
}

$cpf_admin    = $_SESSION['cpf_usuario'];
$qtd_carrinho = contarCarrinho($server, $user, $password, $db, $cpf_admin);

// ============================================================
// CATEGORIAS (fixas — mesmas do index.php)
// ============================================================
$subcats = [
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

// ============================================================
// FLASH
// ============================================================
$flash = '';
if (isset($_SESSION['flash_admin'])) {
    $flash = $_SESSION['flash_admin'];
    unset($_SESSION['flash_admin']);
}

// ============================================================
// AÇÃO: salvar estoques em massa (POST)
// ============================================================
if (isset($_POST['salvar_estoque']) && isset($_POST['estoque'])) {
    foreach ($_POST['estoque'] as $id => $qtd) {
        $id  = (int)$id;
        $qtd = max(0, (int)$qtd);
        banco($server, $user, $password, $db,
              "UPDATE produtos SET estoque = $qtd WHERE id = $id");
    }
    $_SESSION['flash_admin'] = ['tipo' => 'sucesso', 'texto' => '✅ Estoques atualizados.'];
    header("Location: admin.php?aba=produtos");
    exit();
}

// ============================================================
// AÇÃO: desativar / reativar produto (GET)
// ============================================================
if (isset($_GET['acao']) && in_array($_GET['acao'], ['desativar','reativar'])) {
    $id   = (int)($_GET['id'] ?? 0);
    $acao = $_GET['acao'];
    if ($id > 0) {
        $novo = ($acao === 'desativar') ? 0 : 1;
        banco($server, $user, $password, $db,
              "UPDATE produtos SET ativo = $novo WHERE id = $id");
        $_SESSION['flash_admin'] = [
            'tipo'  => 'sucesso',
            'texto' => $acao === 'desativar'
                        ? '✅ Produto desativado (histórico preservado).'
                        : '✅ Produto reativado.'
        ];
    }
    header("Location: admin.php?aba=produtos");
    exit();
}

// ============================================================
// AÇÃO: promover / rebaixar usuário (GET)
// ============================================================
if (isset($_GET['acao']) && in_array($_GET['acao'], ['promover','rebaixar'])) {
    $cpf_alvo = addslashes($_GET['cpf'] ?? '');
    $acao     = $_GET['acao'];
    if ($cpf_alvo !== '' && $cpf_alvo !== $cpf_admin) { // não deixa o admin se rebaixar
        $novo = ($acao === 'promover') ? 1 : 0;
        banco($server, $user, $password, $db,
              "UPDATE usuarios SET admin = $novo WHERE cpf = '$cpf_alvo'");
        $_SESSION['flash_admin'] = ['tipo' => 'sucesso', 'texto' => '✅ Permissão atualizada.'];
    } else {
        $_SESSION['flash_admin'] = ['tipo' => 'erro', 'texto' => '❌ Você não pode alterar seu próprio nível.'];
    }
    header("Location: admin.php?aba=usuarios");
    exit();
}

// ============================================================
// AÇÃO: salvar produto (POST — cadastro/edição)
// ============================================================
if (isset($_POST['salvar_produto'])) {
    $id        = (int)($_POST['id'] ?? 0);
    $nome      = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $preco     = (float)($_POST['preco'] ?? 0);
    $estoque   = max(0, (int)($_POST['estoque'] ?? 0));
    $categoria = trim($_POST['categoria'] ?? '');
    $subcat    = trim($_POST['subcategoria'] ?? '');
    $ativo     = isset($_POST['ativo']) ? 1 : 0;

    if ($nome === '' || $preco <= 0 || $categoria === '' || $subcat === '') {
        $_SESSION['flash_admin'] = ['tipo' => 'erro', 'texto' => '❌ Preencha nome, preço, categoria e subcategoria.'];
        header("Location: admin.php?aba=form" . ($id ? "&id=$id" : ""));
        exit();
    }

    $nome_e = addslashes($nome);
    $desc_e = addslashes($descricao);
    $cat_e  = addslashes($categoria);
    $sub_e  = addslashes($subcat);

    // ---------- Upload ----------
    $caminho_imagem = null;

    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $arq  = $_FILES['imagem'];
        $info = @getimagesize($arq['tmp_name']);
        $tipos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif'];

        if ($info === false || !isset($tipos[$info['mime']])) {
            $_SESSION['flash_admin'] = ['tipo' => 'erro', 'texto' => '❌ Formato inválido. Use JPG, PNG ou GIF.'];
            header("Location: admin.php?aba=form" . ($id ? "&id=$id" : ""));
            exit();
        }
        if ($arq['size'] > 2 * 1024 * 1024) {
            $_SESSION['flash_admin'] = ['tipo' => 'erro', 'texto' => '❌ Imagem muito grande (máx 2MB).'];
            header("Location: admin.php?aba=form" . ($id ? "&id=$id" : ""));
            exit();
        }

        $pasta = "img/produtos/";
        if (!is_dir($pasta)) mkdir($pasta, 0755, true);

        $ext  = $tipos[$info['mime']];
        $base = $id > 0 ? "prod_$id" : "prod_" . time();
        $caminho_imagem = $pasta . $base . "." . $ext;

        foreach (['jpg','jpeg','png','gif'] as $e) {
            $antigo = $pasta . $base . "." . $e;
            if (file_exists($antigo)) @unlink($antigo);
        }

        if (!move_uploaded_file($arq['tmp_name'], $caminho_imagem)) {
            $_SESSION['flash_admin'] = ['tipo' => 'erro', 'texto' => '❌ Falha ao salvar a imagem.'];
            header("Location: admin.php?aba=form" . ($id ? "&id=$id" : ""));
            exit();
        }
    }

    // ---------- INSERT / UPDATE ----------
    if ($id > 0) {
        $set_img = $caminho_imagem ? ", imagem = '" . addslashes($caminho_imagem) . "'" : "";
        banco($server, $user, $password, $db,
              "UPDATE produtos
               SET nome = '$nome_e', descricao = '$desc_e', preco = $preco,
                   estoque = $estoque, categoria = '$cat_e', subcategoria = '$sub_e',
                   ativo = $ativo $set_img
               WHERE id = $id");
        $_SESSION['flash_admin'] = ['tipo' => 'sucesso', 'texto' => '✅ Produto atualizado com sucesso.'];
    } else {
        $img_sql = $caminho_imagem ? "'" . addslashes($caminho_imagem) . "'" : "NULL";
        banco($server, $user, $password, $db,
              "INSERT INTO produtos
                 (nome, descricao, preco, imagem, categoria, subcategoria, estoque, ativo)
               VALUES
                 ('$nome_e', '$desc_e', $preco, $img_sql, '$cat_e', '$sub_e', $estoque, $ativo)");
        $_SESSION['flash_admin'] = ['tipo' => 'sucesso', 'texto' => '✅ Produto cadastrado com sucesso.'];
    }

    header("Location: admin.php?aba=produtos");
    exit();
}

// ============================================================
// ABA ATIVA (whitelist)
// ============================================================
$aba = $_GET['aba'] ?? 'dashboard';
$abas_validas = ['dashboard','produtos','form','vendas','usuarios'];
if (!in_array($aba, $abas_validas)) $aba = 'dashboard';

// ============================================================
// DADOS PARA CADA ABA
// ============================================================
$dados = [];

// ---- Dashboard ----
if ($aba === 'dashboard') {
    $r = banco($server, $user, $password, $db,
               "SELECT COUNT(*) AS t FROM produtos WHERE ativo = 1");
    $dados['total_produtos'] = (int)($r->fetch_assoc()['t'] ?? 0);

    $r = banco($server, $user, $password, $db,
               "SELECT COUNT(*) AS t FROM produtos WHERE ativo = 1 AND estoque <= 10");
    $dados['produtos_baixos'] = (int)($r->fetch_assoc()['t'] ?? 0);

    $r = banco($server, $user, $password, $db,
               "SELECT COUNT(*) AS t FROM usuarios");
    $dados['total_usuarios'] = (int)($r->fetch_assoc()['t'] ?? 0);

    $r = banco($server, $user, $password, $db,
               "SELECT COUNT(DISTINCT numero_venda) AS t
                FROM carrinho WHERE status = 'finalizado'");
    $dados['total_vendas'] = (int)($r->fetch_assoc()['t'] ?? 0);

    $r = banco($server, $user, $password, $db,
               "SELECT COALESCE(SUM(subtotal),0) AS t
                FROM carrinho WHERE status = 'finalizado'");
    $dados['faturamento'] = (float)($r->fetch_assoc()['t'] ?? 0);
}

// ---- Produtos ----
if ($aba === 'produtos') {
    $busca = trim($_GET['busca'] ?? '');
    $cat   = trim($_GET['cat']   ?? '');
    $where = [];
    if ($busca !== '') {
        $b = addslashes($busca);
        $where[] = "(nome LIKE '%$b%' OR descricao LIKE '%$b%')";
    }
    if ($cat !== '') {
        $c = addslashes($cat);
        $where[] = "categoria = '$c'";
    }
    $sql_where = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

    $r = banco($server, $user, $password, $db,
               "SELECT id, nome, preco, imagem, categoria, subcategoria, estoque, ativo
                FROM produtos $sql_where
                ORDER BY ativo DESC, id DESC");
    $dados['produtos'] = [];
    while ($l = $r->fetch_assoc()) $dados['produtos'][] = $l;
    $dados['busca'] = $busca;
    $dados['cat']   = $cat;
}

// ---- Formulário (novo/editar) ----
if ($aba === 'form') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $dados['editando'] = $id > 0;
    $dados['id']       = $id;
    $dados['produto']  = [
        'nome' => '', 'descricao' => '', 'preco' => '', 'categoria' => 'perifericos',
        'subcategoria' => 'monitores', 'estoque' => 0, 'ativo' => 1, 'imagem' => ''
    ];
    if ($id > 0) {
        $r = banco($server, $user, $password, $db, "SELECT * FROM produtos WHERE id = $id");
        if ($l = $r->fetch_assoc()) {
            $dados['produto'] = $l;
        } else {
            header("Location: admin.php?aba=produtos");
            exit();
        }
    }
}

// ---- Vendas ----
if ($aba === 'vendas') {
    $r = banco($server, $user, $password, $db,
               "SELECT c.id, c.numero_venda, c.quantidade, c.preco_unitario, c.subtotal,
                       c.forma_pagamento, c.data_finalizacao, c.cpf_usuario,
                       p.nome AS produto_nome, p.imagem AS produto_imagem,
                       u.nome AS usuario_nome
                FROM carrinho c
                INNER JOIN produtos p ON p.id = c.id_produto
                LEFT JOIN usuarios u  ON u.cpf = c.cpf_usuario
                WHERE c.status = 'finalizado'
                ORDER BY c.data_finalizacao DESC, c.id DESC");
    $dados['historico']   = [];
    $dados['faturamento'] = 0;
    while ($l = $r->fetch_assoc()) {
        $n = $l['numero_venda'];
        if (!isset($dados['historico'][$n])) {
            $dados['historico'][$n] = [
                'numero' => $n, 'data' => $l['data_finalizacao'],
                'pagamento' => $l['forma_pagamento'], 'cpf' => $l['cpf_usuario'],
                'usuario' => $l['usuario_nome'] ?? '—', 'itens' => [], 'total' => 0
            ];
        }
        $dados['historico'][$n]['itens'][] = $l;
        $dados['historico'][$n]['total']  += $l['subtotal'];
        $dados['faturamento']             += $l['subtotal'];
    }
}

// ---- Usuários ----
if ($aba === 'usuarios') {
    $r = banco($server, $user, $password, $db,
               "SELECT u.cpf, u.nome, u.email, u.admin, u.foto,
                       (SELECT login FROM logins WHERE cpf = u.cpf LIMIT 1) AS login
                FROM usuarios u
                ORDER BY u.admin DESC, u.nome ASC");
    $dados['usuarios'] = [];
    while ($l = $r->fetch_assoc()) $dados['usuarios'][] = $l;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel Admin - AnnaFell</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
<?php include "header.php"; ?>

<div class="af-container">

    <div class="af-admin-topo">
        <h1>⚙️ Painel Administrativo</h1>
        <p>Bem-vindo, <strong><?php echo $_SESSION['usuario_logado']; ?></strong>.</p>
    </div>

    <div class="af-admin-nav">
        <a href="admin.php?aba=dashboard" class="af-admin-nav-link <?php echo $aba==='dashboard'?'ativo':''; ?>">📊 Dashboard</a>
        <a href="admin.php?aba=produtos"  class="af-admin-nav-link <?php echo $aba==='produtos' ?'ativo':''; ?>">📦 Produtos</a>
        <a href="admin.php?aba=form"      class="af-admin-nav-link <?php echo $aba==='form'     ?'ativo':''; ?>">➕ Novo Produto</a>
        <a href="admin.php?aba=vendas"    class="af-admin-nav-link <?php echo $aba==='vendas'   ?'ativo':''; ?>">🧾 Vendas</a>
        <a href="admin.php?aba=usuarios"  class="af-admin-nav-link <?php echo $aba==='usuarios' ?'ativo':''; ?>">👥 Usuários</a>
        <a href="index.php"               class="af-admin-nav-link">← Loja</a>
    </div>

    <?php if ($flash): ?>
        <div class="af-flash af-flash-<?php echo $flash['tipo']; ?>" id="afFlash"><?php echo $flash['texto']; ?></div>
    <?php endif; ?>

    <?php /* ==================== DASHBOARD ==================== */ ?>
    <?php if ($aba === 'dashboard'): ?>
        <div class="af-admin-stats">
            <div class="af-admin-stat">
                <span class="af-admin-stat-icone">📦</span>
                <strong><?php echo $dados['total_produtos']; ?></strong>
                <span>Produtos ativos</span>
            </div>
            <div class="af-admin-stat af-admin-stat-alerta">
                <span class="af-admin-stat-icone">⚠️</span>
                <strong><?php echo $dados['produtos_baixos']; ?></strong>
                <span>Estoque baixo (≤10)</span>
            </div>
            <div class="af-admin-stat">
                <span class="af-admin-stat-icone">🛍️</span>
                <strong><?php echo $dados['total_vendas']; ?></strong>
                <span>Vendas finalizadas</span>
            </div>
            <div class="af-admin-stat">
                <span class="af-admin-stat-icone">💰</span>
                <strong>R$ <?php echo number_format($dados['faturamento'], 2, ',', '.'); ?></strong>
                <span>Faturamento total</span>
            </div>
            <div class="af-admin-stat">
                <span class="af-admin-stat-icone">👥</span>
                <strong><?php echo $dados['total_usuarios']; ?></strong>
                <span>Usuários</span>
            </div>
        </div>

        <div class="af-admin-acoes">
            <a href="admin.php?aba=form"     class="af-admin-btn af-admin-btn-primario">➕ Cadastrar Produto</a>
            <a href="admin.php?aba=produtos" class="af-admin-btn">📦 Gerenciar Produtos</a>
            <a href="admin.php?aba=vendas"   class="af-admin-btn">🧾 Ver Vendas</a>
        </div>
    <?php endif; ?>

    <?php /* ==================== PRODUTOS ==================== */ ?>
    <?php if ($aba === 'produtos'): ?>
        <form method="GET" class="af-admin-filtros">
            <input type="hidden" name="aba" value="produtos">
            <input type="text" name="busca" placeholder="Buscar por nome/descrição..." value="<?php echo htmlspecialchars($dados['busca']); ?>">
            <select name="cat">
                <option value="">Todas as categorias</option>
                <option value="perifericos" <?php if($dados['cat']==='perifericos') echo 'selected'; ?>>Periféricos</option>
                <option value="componentes" <?php if($dados['cat']==='componentes') echo 'selected'; ?>>Componentes</option>
                <option value="combos"      <?php if($dados['cat']==='combos')      echo 'selected'; ?>>Combos</option>
            </select>
            <button type="submit" class="af-admin-btn">🔍 Filtrar</button>
            <a href="admin.php?aba=produtos" class="af-admin-btn af-admin-btn-neutro">Limpar</a>
        </form>

        <?php if (empty($dados['produtos'])): ?>
            <div class="af-carrinho-vazio"><p>Nenhum produto encontrado.</p></div>
        <?php else: ?>
        <form method="POST">
            <div class="af-admin-tabela-wrapper">
                <table class="af-admin-tabela">
                    <thead>
                        <tr>
                            <th>ID</th><th>Imagem</th><th>Nome</th><th>Categoria</th>
                            <th>Preço</th><th>Estoque</th><th>Status</th><th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($dados['produtos'] as $p): ?>
                        <tr class="<?php echo $p['ativo'] ? '' : 'af-admin-linha-inativa'; ?>">
                            <td><?php echo $p['id']; ?></td>
                            <td>
                                <?php if (!empty($p['imagem']) && file_exists($p['imagem'])): ?>
                                    <img src="<?php echo $p['imagem']; ?>" class="af-admin-thumb">
                                <?php else: ?>
                                    <span class="af-admin-thumb af-admin-thumb-vazio">?</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($p['nome']); ?></td>
                            <td><small><?php echo $p['categoria']; ?> / <?php echo $p['subcategoria']; ?></small></td>
                            <td>R$ <?php echo number_format($p['preco'], 2, ',', '.'); ?></td>
                            <td>
                                <input type="number" name="estoque[<?php echo $p['id']; ?>]"
                                       value="<?php echo $p['estoque']; ?>" min="0"
                                       class="af-admin-input-estoque">
                            </td>
                            <td>
                                <?php if ($p['ativo']): ?>
                                    <span class="af-admin-badge af-admin-badge-ativo">Ativo</span>
                                <?php else: ?>
                                    <span class="af-admin-badge af-admin-badge-inativo">Inativo</span>
                                <?php endif; ?>
                            </td>
                            <td class="af-admin-acoes-td">
                                <a href="admin.php?aba=form&id=<?php echo $p['id']; ?>" class="af-admin-btn-mini">✏️ Editar</a>
                                <?php if ($p['ativo']): ?>
                                    <a href="admin.php?acao=desativar&id=<?php echo $p['id']; ?>"
                                       class="af-admin-btn-mini af-admin-btn-mini-perigo"
                                       onclick="return confirm('Desativar este produto? Ele some da loja, mas o histórico é mantido.');">🚫 Desativar</a>
                                <?php else: ?>
                                    <a href="admin.php?acao=reativar&id=<?php echo $p['id']; ?>"
                                       class="af-admin-btn-mini af-admin-btn-mini-sucesso"
                                       onclick="return confirm('Reativar este produto?');">✅ Reativar</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="af-admin-salvar-estoques">
                <button type="submit" name="salvar_estoque" class="af-admin-btn af-admin-btn-primario">💾 Salvar Estoques</button>
            </div>
        </form>
        <?php endif; ?>
    <?php endif; ?>

    <?php /* ==================== FORM PRODUTO ==================== */ ?>
    <?php if ($aba === 'form'): ?>
        <?php $p = $dados['produto']; $editando = $dados['editando']; ?>
        <div class="af-form-box af-form-box-grande">
            <h2><?php echo $editando ? '✏️ Editar Produto #' . $dados['id'] : '➕ Novo Produto'; ?></h2>

            <form method="POST" action="admin.php" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $editando ? $dados['id'] : ''; ?>">

                <div class="af-campo">
                    <label>Nome do produto: *</label>
                    <input type="text" name="nome" required maxlength="150"
                           value="<?php echo htmlspecialchars($p['nome']); ?>">
                </div>

                <div class="af-campo">
                    <label>Descrição:</label>
                    <textarea name="descricao" rows="4" maxlength="1000"><?php echo htmlspecialchars($p['descricao']); ?></textarea>
                </div>

                <div class="af-campo">
                    <label>Preço (R$): *</label>
                    <input type="number" name="preco" step="0.01" min="0" required
                           value="<?php echo htmlspecialchars($p['preco']); ?>">
                </div>

                <div class="af-campo">
                    <label>Estoque: *</label>
                    <input type="number" name="estoque" min="0" required
                           value="<?php echo (int)$p['estoque']; ?>">
                </div>

                <div class="af-campo">
                    <label>Categoria: *</label>
                    <select name="categoria" id="sel-categoria" required onchange="atualizarSubcats()">
                        <?php foreach ($subcats as $slug => $subs): ?>
                            <option value="<?php echo $slug; ?>" <?php if($p['categoria']===$slug) echo 'selected'; ?>>
                                <?php echo ucfirst($slug); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="af-campo">
                    <label>Subcategoria: *</label>
                    <select name="subcategoria" id="sel-subcategoria" required>
                        <?php
                        $subs_atuais = $subcats[$p['categoria']] ?? [];
                        foreach ($subs_atuais as $slug => $nome): ?>
                            <option value="<?php echo $slug; ?>" <?php if($p['subcategoria']===$slug) echo 'selected'; ?>>
                                <?php echo $nome; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="af-campo">
                    <label>Imagem (JPG, PNG, GIF - máx 2MB):</label>
                    <?php if (!empty($p['imagem']) && file_exists($p['imagem'])): ?>
                        <img src="<?php echo $p['imagem']; ?>" class="af-admin-preview">
                        <small class="af-texto-cinza">Envie uma nova imagem para substituir.</small>
                    <?php endif; ?>
                    <input type="file" name="imagem" accept="image/jpeg,image/png,image/gif">
                </div>

                <div class="af-campo">
                    <label>
                        <input type="checkbox" name="ativo" value="1" <?php if($p['ativo']) echo 'checked'; ?>>
                        Produto ativo (aparece na loja)
                    </label>
                </div>

                <button type="submit" name="salvar_produto" class="af-admin-btn af-admin-btn-primario">
                    <?php echo $editando ? '💾 Salvar alterações' : '➕ Cadastrar produto'; ?>
                </button>
                <a href="admin.php?aba=produtos" class="af-admin-btn af-admin-btn-neutro">Cancelar</a>
            </form>
        </div>

        <script>
        var SUBCATS = <?php echo json_encode($subcats); ?>;
        function atualizarSubcats() {
            var cat  = document.getElementById('sel-categoria').value;
            var sel  = document.getElementById('sel-subcategoria');
            sel.innerHTML = '';
            if (!SUBCATS[cat]) return;
            Object.keys(SUBCATS[cat]).forEach(function(slug){
                var opt = document.createElement('option');
                opt.value = slug;
                opt.textContent = SUBCATS[cat][slug];
                sel.appendChild(opt);
            });
        }
        </script>
    <?php endif; ?>

    <?php /* ==================== VENDAS ==================== */ ?>
    <?php if ($aba === 'vendas'): ?>
        <h2>🧾 Vendas Finalizadas</h2>
        <p class="af-texto-cinza">
            <?php echo count($dados['historico']); ?> pedido(s) —
            Faturamento total: <strong>R$ <?php echo number_format($dados['faturamento'], 2, ',', '.'); ?></strong>
        </p>

        <?php if (empty($dados['historico'])): ?>
            <div class="af-carrinho-vazio"><p>Nenhuma venda finalizada ainda.</p></div>
        <?php else: ?>
            <?php foreach ($dados['historico'] as $compra): ?>
                <div class="af-compra-card">
                    <div class="af-compra-header">
                        <div class="af-compra-numero">
                            <span class="af-compra-label">Pedido</span>
                            <strong>#<?php echo $compra['numero']; ?></strong>
                        </div>
                        <div class="af-compra-data">
                            <?php echo date('d/m/Y \à\s H:i', strtotime($compra['data'])); ?>
                        </div>
                        <div class="af-compra-pagamento">💳 <?php echo $compra['pagamento']; ?></div>
                        <div class="af-compra-cliente">
                            👤 <?php echo htmlspecialchars($compra['usuario']); ?><br>
                            <small><?php echo $compra['cpf']; ?></small>
                        </div>
                    </div>

                    <div class="af-compra-itens">
                        <?php foreach ($compra['itens'] as $item): ?>
                            <div class="af-compra-item">
                                <img src="<?php echo $item['produto_imagem']; ?>" class="af-compra-item-img">
                                <div class="af-compra-item-info">
                                    <strong><?php echo htmlspecialchars($item['produto_nome']); ?></strong>
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
    <?php endif; ?>

    <?php /* ==================== USUÁRIOS ==================== */ ?>
    <?php if ($aba === 'usuarios'): ?>
        <h2>👥 Usuários Cadastrados</h2>
        <p class="af-texto-cinza"><?php echo count($dados['usuarios']); ?> usuário(s). Promova ou rebaixe administradores.</p>

        <div class="af-admin-tabela-wrapper">
            <table class="af-admin-tabela">
                <thead>
                    <tr>
                        <th>Foto</th><th>Nome</th><th>CPF</th><th>Login</th>
                        <th>E-mail</th><th>Nível</th><th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($dados['usuarios'] as $u): ?>
                    <tr>
                        <td>
                            <?php if (!empty($u['foto']) && file_exists($u['foto'])): ?>
                                <img src="<?php echo $u['foto']; ?>" class="af-admin-thumb">
                            <?php else: ?>
                                <span class="af-admin-thumb af-admin-thumb-vazio">
                                    <?php echo strtoupper(substr($u['nome'], 0, 1)); ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($u['nome']); ?></td>
                        <td><?php echo $u['cpf']; ?></td>
                        <td><?php echo htmlspecialchars($u['login'] ?? '—'); ?></td>
                        <td><small><?php echo htmlspecialchars($u['email'] ?? '—'); ?></small></td>
                        <td>
                            <?php if ($u['admin']): ?>
                                <span class="af-admin-badge af-admin-badge-admin">👑 Admin</span>
                            <?php else: ?>
                                <span class="af-admin-badge af-admin-badge-inativo">Cliente</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($u['cpf'] === $cpf_admin): ?>
                                <small class="af-texto-cinza">(você)</small>
                            <?php elseif ($u['admin']): ?>
                                <a href="admin.php?acao=rebaixar&cpf=<?php echo $u['cpf']; ?>"
                                   class="af-admin-btn-mini af-admin-btn-mini-perigo"
                                   onclick="return confirm('Rebaixar <?php echo htmlspecialchars($u['nome']); ?> para cliente?');">
                                   ⬇️ Rebaixar
                                </a>
                            <?php else: ?>
                                <a href="admin.php?acao=promover&cpf=<?php echo $u['cpf']; ?>"
                                   class="af-admin-btn-mini af-admin-btn-mini-sucesso"
                                   onclick="return confirm('Promover <?php echo htmlspecialchars($u['nome']); ?> a admin?');">
                                   ⬆️ Promover
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</div>

<div id="af-rodape">Desenvolvido por Anna Engelhardt e Luís Brito © 2026</div>

<script>
var flash = document.getElementById('afFlash');
if (flash) {
    setTimeout(function(){
        flash.style.transition = 'opacity .5s';
        flash.style.opacity = '0';
        setTimeout(function(){ flash.style.display = 'none'; }, 500);
    }, 4000);
}
</script>
</body>
</html>