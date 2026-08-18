<?php
if(!isset($_SESSION)) {
    session_start();
}


?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro - Etapa 1</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <div class="container">
        <div class="form-box">
            <h2>Cadastro de Usuário - Etapa 1</h2>
            
            <form method="POST" action="salvar_usuario.php">
                <div class="campo">
                    <label>Nome Completo:</label>
                    <input type="text" name="nome" required>
                </div>
                
                <div class="campo">
                    <label>CPF:</label>
                    <input type="text" name="cpf" required placeholder="Apenas números">
                </div>
                
                <div class="campo">
                    <label>Endereço:</label>
                    <input type="text" name="endereco" required>
                </div>
                
                <div class="campo">
                    <label>Bairro:</label>
                    <input type="text" name="bairro" required>
                </div>
                
                <div class="campo">
                    <label>Cidade:</label>
                    <input type="text" name="cidade" required>
                </div>
                
                <div class="campo">
                    <label>Estado:</label>
                    <input type="text" name="estado" required maxlength="2">
                </div>
                
                <div class="campo">
                    <label>CEP:</label>
                    <input type="text" name="cep" required>
                </div>
                
                <button type="submit" class="btn">Próxima Etapa</button>  <!-- vai para salvar usuario -->
            </form>
        </div>
    </div>
</body>
</html>