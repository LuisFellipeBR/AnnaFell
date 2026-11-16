<?php
if(!isset($_SESSION)) {
    session_start();
}
include "cons.php";
require_once "DLL.php";

if (!isset($_SESSION['cpf_usuario']) || empty($_SESSION['cpf_usuario'])) {
    header("Location: nao_logado.php");
    exit();
}

$cpf = $_SESSION['cpf_usuario'];

foreach (['jpg', 'jpeg', 'png', 'gif'] as $ext) {
    $arq = "img/usuarios/" . $cpf . "." . $ext;
    if (file_exists($arq)) {
        @unlink($arq);
    }
}

banco($server, $user, $password, $db,
      "UPDATE usuarios SET foto = NULL WHERE cpf = '$cpf'");

$_SESSION['flash'] = ['tipo' => 'sucesso', 'texto' => '✅ Foto removida com sucesso.'];
header("Location: perfil.php?aba=foto");
exit();
?>