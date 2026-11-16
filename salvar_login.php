<?php
if(!isset($_SESSION)) {
    session_start();
}
include "cons.php";
require_once "DLL.php";

extract($_POST);
$cpf = $_SESSION['cpf_usuario'] ?? '';

if (empty($cpf)) {
    header("Location: cadastro1.php");
    exit();
}

$senha_criptografada = md5($senha);

// Verifica se o login já existe
$verifica = "SELECT id FROM logins WHERE login = '$login'";
$res = banco($server, $user, $password, $db, $verifica);

if ($res->fetch_assoc()) {
    // Já existe → atualiza senha e cpf
    $sql = "UPDATE logins 
            SET senha = '$senha_criptografada',
                cpf = '$cpf'
            WHERE login = '$login'";
} else {
    // Não existe → insere
    $sql = "INSERT INTO logins (login, senha, cpf) 
            VALUES ('$login', '$senha_criptografada', '$cpf')";
}

banco($server, $user, $password, $db, $sql);

unset($_SESSION['cpf_usuario']);
header("Location: login.php?cadastro=ok");
exit();
?>