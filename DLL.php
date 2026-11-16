<?php
// ============================================================
// DLL.php - Funções de banco de dados e helpers do sistema
// ============================================================

// ------------------------------------------------------------
// teste_login() - Verifica se a sessão de login é válida
// ------------------------------------------------------------
function teste_login($sessao) {
    if ($sessao != "ok") {
        header("Location: index.php?erro=1");
        exit;
    }
}

// ------------------------------------------------------------
// banco() - Executa uma consulta SQL no banco annafell
// ------------------------------------------------------------
// $keep_open = true → mantém a conexão aberta (necessário para
//                      pegar insert_id após um INSERT)
// ------------------------------------------------------------
function banco($server, $user, $password, $db, $consulta, $keep_open = false)
{
    $banco = new mysqli($server, $user, $password, $db);

    if ($banco->connect_error) {
        echo "Falha de conexão referência: (" . $banco->connect_errno . ") - " . $banco->connect_error;
        echo "<a href='incluir.php'><img src='img/fundo/voltar.png' width='40' height='40'></a>";
        exit();
    }

    if (!$resultado = $banco->query($consulta)) {
        echo "Falha na consulta referência: (" . $banco->errno . ") - " . $banco->error;
        echo "<a href='incluir.php'><img src='img/fundo/voltar.png' width='40' height='40'></a>";
        exit();
    }

    if ($keep_open) {
        return (object) ['resultado' => $resultado, 'conn' => $banco];
    } else {
        $banco->close();
        return $resultado;
    }
}

// ------------------------------------------------------------
// contarCarrinho() - Conta quantos itens o usuário tem no carrinho
// ------------------------------------------------------------
function contarCarrinho($server, $user, $password, $db, $cpf) {
    if (empty($cpf)) return 0;

    $consulta = "SELECT COALESCE(SUM(quantidade), 0) AS total
                 FROM carrinho
                 WHERE cpf_usuario = '$cpf' AND status = 'carrinho'";
    $res = banco($server, $user, $password, $db, $consulta);

    if ($linha = $res->fetch_assoc()) {
        return (int)$linha['total'];
    }
    return 0;
}

// ------------------------------------------------------------
// form() - Gera um formulário genérico (já existente no projeto)
// ------------------------------------------------------------
function form($action, $var1, $var2, $var3, $var4, $var5, $var6, $var7, $b1, $b2, $b3) {
    echo "
    <style type='text/css'>
    label.incluir {
        display: inline-block;
        width: 120px;
    }
    </style>";
    echo "<fieldset>";
    echo "<form action='$action' method='post'>";

    if (isset($var1)) { echo "<label for=$var1 class='incluir'>$var1:</label>"; echo "<input type='text' name='$var1'/><br/>"; }
    if (isset($var2)) { echo "<label for=$var2 class='incluir'>$var2:</label>"; echo "<input type='text' name='$var2'/><br/>"; }
    if (isset($var3)) { echo "<label for=$var3 class='incluir'>$var3:</label>"; echo "<input type='text' name='$var3'/><br/>"; }
    if (isset($var4)) { echo "<label for=$var4 class='incluir'>$var4:</label>"; echo "<input type='text' name='$var4'/><br/>"; }
    if (isset($var5)) { echo "<label for=$var5 class='incluir'>$var5:</label>"; echo "<input type='text' name='$var5'/><br/>"; }
    if (isset($var6)) { echo "<label for=$var6 class='incluir'>$var6:</label>"; echo "<input type='text' name='$var6'/><br/>"; }
    if (isset($var7)) { echo "<label for=$var7 class='incluir'>$var7:</label>"; echo "<input type='text' name='$var7'/><br/>"; }

    if (isset($b1)) echo "<input type='submit' value='$b1' name='$b1'/>";
    if (isset($b2)) echo "<input type='submit' value='$b2' name='$b2'/>";
    if (isset($b3)) echo "<input type='submit' value='$b3' name='$b3'/>";

    echo "</form>";
    echo "</fieldset>";
}

// ------------------------------------------------------------
// form_foto() - Gera o formulário de upload de foto de perfil
// ------------------------------------------------------------
// $action   = action do form (ex: "salvar_foto.php")
// $foto     = caminho da foto atual (customizada OU padrão)
// $tem_foto = true se o usuário já tem foto personalizada
// $mensagem = mensagem de sucesso/erro (opcional)
// ------------------------------------------------------------
function form_foto($action, $foto, $tem_foto, $mensagem = '') {
    echo "<div class='af-bloco-foto'>";

    if (!empty($mensagem)) {
        echo "<p class='af-mensagem-foto'>$mensagem</p>";
    }

    // Exibe a foto atual
    echo "<img src='$foto' class='af-foto-perfil-grande' alt='Foto de perfil'>";

    // Formulário de upload (enctype é obrigatório para arquivos)
    echo "<form action='$action' method='post' enctype='multipart/form-data' class='af-form-foto'>";
    echo "  <label class='af-label-foto'>Escolher imagem (JPG, PNG ou GIF - máximo 2MB):</label>";
    echo "  <input type='file' name='foto' accept='image/jpeg,image/png,image/gif' required>";
    echo "  <input type='submit' value='Enviar foto' class='af-btn-foto-enviar'>";
    echo "</form>";

    // Botão remover (só se tiver foto personalizada)
    if ($tem_foto) {
        echo "<form action='remover_foto.php' method='post' class='af-form-remover'>";
        echo "  <input type='submit' value='Remover foto' class='af-btn-foto-remover' onclick=\"return confirm('Deseja remover a foto atual?');\">";
        echo "</form>";
    }

    echo "</div>";
}

// ------------------------------------------------------------
// XML() - Gera arquivo XML (mantida do projeto original)
// ------------------------------------------------------------
function XML($label, $x1, $x2, $x3, $x4, $x5, $x6, $x7, $x8, $x9, $x10, $file) {
    $xml  = '<?xml version="1.0" encoding="utf-8"?>';
    $xml .= '<links>';
    $xml .= '<link>';

    if (isset($x1))  $xml .= '<' . $label[0] . '>' . $x1  . '</' . $label[0] . '>';
    if (isset($x2))  $xml .= '<' . $label[1] . '>' . $x2  . '</' . $label[1] . '>';
    if (isset($x3))  $xml .= '<' . $label[2] . '>' . $x3  . '</' . $label[2] . '>';
    if (isset($x4))  $xml .= '<' . $label[3] . '>' . $x4  . '</' . $label[3] . '>';
    if (isset($x5))  $xml .= '<' . $label[4] . '>' . $x5  . '</' . $label[4] . '>';
    if (isset($x6))  $xml .= '<' . $label[5] . '>' . $x6  . '</' . $label[5] . '>';
    if (isset($x7))  $xml .= '<' . $label[6] . '>' . $x7  . '</' . $label[6] . '>';
    if (isset($x8))  $xml .= '<' . $label[7] . '>' . $x8  . '</' . $label[7] . '>';
    if (isset($x9))  $xml .= '<' . $label[8] . '>' . $x9  . '</' . $label[8] . '>';
    if (isset($x10)) $xml .= '<' . $label[9] . '>' . $x10 . '</' . $label[9] . '>';

    $fim = count($label) - 1;
    $xml .= '<modo>' . $label[$fim] . '</modo>';
    $xml .= '</link>';
    $xml .= '</links>';

    $fp = fopen($file, 'w+');
    fwrite($fp, $xml);
    fclose($fp);
}

// ------------------------------------------------------------
// Envia_doc() e recebe_doc() - FTP (mantidas do projeto)
// ------------------------------------------------------------
function Envia_doc($host_ftp, $user_ftp, $pass_ftp, $file_origem, $file_destino) {
    $ftp_con = ftp_connect($host_ftp);
    ftp_login($ftp_con, $user_ftp, $pass_ftp);
    ftp_pasv($ftp_con, true);
    ftp_put($ftp_con, $file_destino, $file_origem, FTP_ASCII);
    ftp_close($ftp_con);
}

function recebe_doc($host_ftp, $user_ftp, $pass_ftp, $file_origem, $file_destino) {
    $ftp = ftp_connect($host_ftp);
    ftp_login($ftp, $user_ftp, $pass_ftp);
    ftp_pasv($ftp, true);
    ftp_get($ftp, $file_destino, $file_origem, FTP_BINARY);
    ftp_close($ftp);
}

// ------------------------------------------------------------
// carregarXML() e salvarXML() - (mantidas do projeto)
// ------------------------------------------------------------
function carregarXML($folder, $salvar) {
    include "cons.php";
    if ($handle = opendir($folder)) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                $ler = $folder . '/' . $entry;
                if ($ler != NULL) {
                    $xml = simplexml_load_file($ler);
                    unlink($ler);
                    if ($salvar == 1) salvarXML($server, $user, $password, $db, $xml);
                }
            }
        }
        closedir($handle);
    }
}

function salvarXML($server, $user, $password, $db, $xml) {
    if ($xml->link->modo == "incluir") {
        $sql = "INSERT INTO vendas (num_cartao, nome_titular, validade, cod_seguranca, num_venda, valor, cod_cliente) 
                VALUES ('" . $xml->link->num_cartao . "', '" . $xml->link->nome . "', '" . $xml->link->validade . "', 
                " . $xml->link->cod_seg . ", " . $xml->link->num_venda . ", '" . $xml->link->valor . "', " . $xml->link->cod_cliente . ")";
    }
    if ($xml->link->modo == "lista") {
        $sql = "SELECT * FROM vendas ORDER BY nome_titular";
    }
    $resultado = banco($server, $user, $password, $db, $sql);
    return $resultado;
}
?>