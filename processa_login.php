<?php
if(!isset($_SESSION)) {
    session_start();
}
include "cons.php";
require_once "DLL.php";

extract($_POST);

$senha_criptografada = md5($senha);

$consulta = "SELECT senha, cpf FROM logins WHERE login = '$login'";
$resultado = banco($server, $user, $password, $db, $consulta);

if ($linha = $resultado->fetch_assoc()) {
    if ($senha_criptografada === $linha['senha']) {
        $_SESSION['usuario_logado'] = $login;
        $_SESSION['cpf_usuario']    = $linha['cpf'];

        if (isset($_SESSION['produto_pendente'])) {
            $id_pend = (int)$_SESSION['produto_pendente'];
            unset($_SESSION['produto_pendente']);

            $consulta_p = "SELECT preco FROM produtos WHERE id = $id_pend AND ativo = 1";
            $res_p = banco($server, $user, $password, $db, $consulta_p);
            if ($prod = $res_p->fetch_assoc()) {
                $cpf_user = $linha['cpf'];
                $preco    = $prod['preco'];

                $busca = "SELECT id, quantidade FROM carrinho
                          WHERE cpf_usuario = '$cpf_user' AND id_produto = $id_pend AND status = 'carrinho'";
                $res_b = banco($server, $user, $password, $db, $busca);
                if ($item = $res_b->fetch_assoc()) {
                    $nova = $item['quantidade'] + 1;
                    banco($server, $user, $password, $db,
                          "UPDATE carrinho SET quantidade = $nova WHERE id = {$item['id']}");
                } else {
                    banco($server, $user, $password, $db,
                          "INSERT INTO carrinho (cpf_usuario, id_produto, quantidade, preco_unitario, status)
                           VALUES ('$cpf_user', $id_pend, 1, $preco, 'carrinho')");
                }
            }
        }

        header("Location: carrinho.php");
        exit();
    }
}

header("Location: erro.php");
exit();
?>