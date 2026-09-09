<?php
if(!isset($_SESSION)) {
    session_start();
}
include "cons.php";
require_once "DLL.php";

if (!isset($_SESSION['carrinho']) || empty($_SESSION['carrinho'])) {
    header("Location: index.php");
    exit();
}

extract($_POST);

$senha_criptografada = md5($senha);


$consulta = "SELECT senha, cpf FROM logins WHERE login = '$login'";
$resultado = banco($server, $user, $password, $db, $consulta);

if ($linha = $resultado->fetch_assoc()) {
    $senha_arquivo = $linha['senha'];
    $cpf_usuario = $linha['cpf'];
    if ($senha_criptografada === $senha_arquivo) {
        $_SESSION['usuario_logado'] = $login;
        $_SESSION['cpf_usuario'] = $cpf_usuario;
        header("Location: confirmar.php");
        exit();
    } else {
        header("Location: erro.php");
        exit();
    }
} else {
    header("Location: erro.php");
    exit();
}
?>