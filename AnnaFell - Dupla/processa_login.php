<?php
if(!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['carrinho']) || empty($_SESSION['carrinho'])) { //se nao tiver nada, index
    header("Location: index.php");
    exit();
}

extract($_POST);

$senha_criptografada = md5($senha);
$caminho = "login/" . $login . ".dat"; //caminho do arquivo

if (file_exists($caminho)) {    //verificar login
    $arq = fopen($caminho, "r");
    fgets($arq); // pula linha do login
    $senha_arquivo = trim(fgets($arq));
    $cpf_usuario = trim(fgets($arq));
    fclose($arq);
    
    if ($senha_criptografada === $senha_arquivo) { //verificar senha
        $_SESSION['usuario_logado'] = $login;
        $_SESSION['cpf_usuario'] = $cpf_usuario;
        $_SESSION['Logado'] = 'ok';
        $_SESSION['Nome'] = $login;
        header("Location: confirmar.php"); //vai para confirmar
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