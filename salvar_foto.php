<?php
if(!isset($_SESSION)) {
    session_start();
}
include "cons.php";
require_once "DLL.php";

if (!isset($_SESSION['cpf_usuario']) || empty($_SESSION['cpf_usuario'])) {
    $_SESSION['flash'] = ['tipo' => 'erro', 'texto' => '❌ Você precisa estar logado.'];
    header("Location: nao_logado.php");
    exit();
}

$cpf = $_SESSION['cpf_usuario'];

if (!isset($_FILES['foto']) || $_FILES['foto']['error'] != UPLOAD_ERR_OK) {
    $_SESSION['flash'] = ['tipo' => 'erro', 'texto' => '❌ Nenhum arquivo foi enviado. Selecione uma imagem.'];
    header("Location: perfil.php?aba=foto");
    exit();
}

$arquivo = $_FILES['foto'];

$info_imagem = @getimagesize($arquivo['tmp_name']);
$tipos_permitidos = ['image/jpeg', 'image/png', 'image/gif'];
$extensoes        = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif'];

if ($info_imagem === false || !in_array($info_imagem['mime'], $tipos_permitidos)) {
    $_SESSION['flash'] = ['tipo' => 'erro', 'texto' => '❌ Formato inválido. Use JPG, PNG ou GIF.'];
    header("Location: perfil.php?aba=foto");
    exit();
}

if ($arquivo['size'] > 2 * 1024 * 1024) {
    $_SESSION['flash'] = ['tipo' => 'erro', 'texto' => '❌ Imagem muito grande. Máximo 2MB.'];
    header("Location: perfil.php?aba=foto");
    exit();
}

$pasta = "img/usuarios/";
if (!is_dir($pasta)) {
    mkdir($pasta, 0755, true);
}

$extensao     = $extensoes[$info_imagem['mime']];
$nome_arquivo = $pasta . $cpf . "." . $extensao;

foreach (['jpg', 'jpeg', 'png', 'gif'] as $ext) {
    $antiga = $pasta . $cpf . "." . $ext;
    if (file_exists($antiga)) {
        @unlink($antiga);
    }
}

if (!move_uploaded_file($arquivo['tmp_name'], $nome_arquivo)) {
    $_SESSION['flash'] = ['tipo' => 'erro', 'texto' => '❌ Não foi possível salvar a imagem.'];
    header("Location: perfil.php?aba=foto");
    exit();
}

$consulta = "UPDATE usuarios SET foto = '$nome_arquivo' WHERE cpf = '$cpf'";
banco($server, $user, $password, $db, $consulta);

$_SESSION['flash'] = ['tipo' => 'sucesso', 'texto' => '✅ Foto atualizada com sucesso!'];
header("Location: perfil.php?aba=foto");
exit();
?>