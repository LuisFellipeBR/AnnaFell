<?php
if(!isset($_SESSION)) {
    session_start();
}

include "cons.php";                                                                              //mudei
require_once "DLL.php";                                                                          //mudei

if (!isset($_SESSION['carrinho']) || empty($_SESSION['carrinho'])) { //se nao tiver nada, index
    header("Location: index.php");
    exit();
}

extract($_POST);

$senha_criptografada = md5($senha);

$sql = "SELECT * FROM logins WHERE login = '$login'";
$resultado = banco($server, $user, $password, $db, $sql);

if ($resultado->num_rows > 0) {
    $linha = $resultado->fetch_assoc();
    
    if ($senha_criptografada === $linha['senha']) {
        $_SESSION['usuario_logado'] = $login;
        $_SESSION['cpf_usuario'] = $linha['cpf'];
        $_SESSION['Logado'] = 'ok';
        $_SESSION['Nome'] = $login;
        header("Location: confirmar.php");                                                               //vai para confirmar
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