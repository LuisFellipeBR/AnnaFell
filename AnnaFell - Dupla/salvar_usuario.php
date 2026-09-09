<?php
session_start();
include "cons.php";
require_once "DLL.php";

extract($_POST); 

$cpf_limpo = preg_replace('/[^0-9]/', '', $cpf);

$sql = "INSERT INTO usuarios (cpf, nome, endereco, bairro, cidade, estado, cep) 
        VALUES ('$cpf_limpo', '$nome', '$endereco', '$bairro', '$cidade', '$estado', '$cep')";

banco($server, $user, $password, $db, $sql);

$_SESSION['cpf_usuario'] = $cpf_limpo;
header("Location: cadastro2.php");
exit();
?>
