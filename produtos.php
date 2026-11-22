<?php
// produtos.php - Busca os produtos do banco de dados

function getProdutos($server, $user, $password, $db) {
    $produtos = [];
    $consulta = "SELECT id, nome, descricao, preco, imagem, categoria, subcategoria, estoque
                 FROM produtos
                 WHERE ativo = 1
                 ORDER BY id";
    $resultado = banco($server, $user, $password, $db, $consulta);
    while ($linha = $resultado->fetch_assoc()) {
        $produtos[$linha['id']] = $linha;
    }
    return $produtos;
}

// Formata o aviso de estoque (reutilizável em qualquer página)
function textoEstoque($estoque) {
    if ($estoque <= 0) return ['classe' => 'af-estoque-esgotado', 'texto' => '❌ Esgotado'];
    if ($estoque <= 10) return ['classe' => 'af-estoque-baixo',     'texto' => '⚠️ Últimas ' . $estoque . ' unidades!'];
    return ['classe' => 'af-estoque-ok', 'texto' => '✅ Em estoque: ' . $estoque . ' unidades'];
}
?>