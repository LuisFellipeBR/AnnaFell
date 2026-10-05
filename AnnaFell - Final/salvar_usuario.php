<?php
session_start();
include "cons.php";
require_once "DLL.php";

extract($_POST);

// ============================================================
// VALIDAÇÃO: CPF
// ============================================================
$cpf_limpo = preg_replace('/[^0-9]/', '', $cpf);

if (strlen($cpf_limpo) < 11) {
    $_SESSION['flash_cadastro'] = [
        'tipo'  => 'erro',
        'texto' => '❌ CPF inválido. Digite os 11 números do CPF.'
    ];
    header("Location: cadastro1.php");
    exit();
}

if (strlen($cpf_limpo) > 11) {
    $_SESSION['flash_cadastro'] = [
        'tipo'  => 'erro',
        'texto' => '❌ CPF com números a mais. Deve ter exatamente 11 dígitos.'
    ];
    header("Location: cadastro1.php");
    exit();
}

// ============================================================
// VALIDAÇÃO: CEP
// ============================================================
$cep_limpo = preg_replace('/[^0-9]/', '', $cep);

if (strlen($cep_limpo) < 8) {
    $_SESSION['flash_cadastro'] = [
        'tipo'  => 'erro',
        'texto' => '❌ CEP inválido. Digite os 8 números do CEP.'
    ];
    header("Location: cadastro1.php");
    exit();
}

if (strlen($cep_limpo) > 8) {
    $_SESSION['flash_cadastro'] = [
        'tipo'  => 'erro',
        'texto' => '❌ CEP com números a mais. Deve ter exatamente 8 dígitos.'
    ];
    header("Location: cadastro1.php");
    exit();
}

// ============================================================
// VALIDAÇÃO: Campos obrigatórios
// ============================================================
if (empty(trim($nome)) || empty(trim($endereco)) || empty(trim($bairro)) ||
    empty(trim($cidade)) || empty(trim($estado))) {
    $_SESSION['flash_cadastro'] = [
        'tipo'  => 'erro',
        'texto' => '❌ Preencha todos os campos obrigatórios.'
    ];
    header("Location: cadastro1.php");
    exit();
}

// ============================================================
// Limita tamanho para respeitar as colunas do banco
// ============================================================
$nome     = substr(trim($nome), 0, 100);
$endereco = substr(trim($endereco), 0, 100);
$bairro   = substr(trim($bairro), 0, 50);
$cidade   = substr(trim($cidade), 0, 50);
$estado   = strtoupper(substr(trim($estado), 0, 2));

// ============================================================
// Salva no banco (INSERT ou UPDATE)
// ============================================================
$verifica = "SELECT cpf FROM usuarios WHERE cpf = '$cpf_limpo'";
$res = banco($server, $user, $password, $db, $verifica);

if ($res->fetch_assoc()) {
    $sql = "UPDATE usuarios 
            SET nome = '$nome',
                endereco = '$endereco',
                bairro = '$bairro',
                cidade = '$cidade',
                estado = '$estado',
                cep = '$cep_limpo'
            WHERE cpf = '$cpf_limpo'";
} else {
    $sql = "INSERT INTO usuarios (cpf, nome, endereco, bairro, cidade, estado, cep) 
            VALUES ('$cpf_limpo', '$nome', '$endereco', '$bairro', '$cidade', '$estado', '$cep_limpo')";
}

banco($server, $user, $password, $db, $sql);

$_SESSION['cpf_usuario'] = $cpf_limpo;
header("Location: cadastro2.php");
exit();
?>