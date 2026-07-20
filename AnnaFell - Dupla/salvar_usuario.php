<?php
if(!isset($_SESSION)) {
    session_start();
}
extract($_POST); 

$cpf_limpo = preg_replace('/[^0-9]/', '', $cpf);

$arq = fopen("usuarios/" . $cpf_limpo . ".dat", "w");
fwrite($arq, $nome . "\n");
fwrite($arq, $cpf . "\n");
fwrite($arq, $endereco . "\n");
fwrite($arq, $bairro . "\n");
fwrite($arq, $cidade . "\n");
fwrite($arq, $estado . "\n");
fwrite($arq, $cep . "\n");
fclose($arq);

$_SESSION['cpf_usuario'] = $cpf_limpo;
header("Location: cadastro2.php");
exit();
?>