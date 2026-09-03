<?php
if(!isset($_SESSION)) {
    session_start();
}

include "cons.php";                                                                            //mudei
require_once "DLL.php";                                                                        //mudei

extract($_POST); 

$cpf_limpo = preg_replace('/[^0-9]/', '', $cpf);

$sql_verifica = "SELECT cpf FROM usuarios WHERE cpf = '$cpf_limpo'";                                  //mudei
$resultado = banco($server, $user, $password, $db, $sql_verifica);                                    //mudei

if ($resultado->num_rows > 0) {
    header("Location: cadastro1.php?erro=cpf_existente");
    exit();
}

$sql = "INSERT INTO usuarios (cpf, nome, endereco, bairro, cidade, estado, cep)
        VALUES ('$cpf_limpo', '$nome', '$endereco', '$bairro', '$cidade', '$estado', '$cep')";      //mudei

banco($server, $user, $password, $db, $sql);                                                        //mudei

$_SESSION['cpf_usuario'] = $cpf_limpo;
header("Location: cadastro2.php");
exit();
?>