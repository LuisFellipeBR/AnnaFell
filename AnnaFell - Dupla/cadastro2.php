<?php
if(!isset($_SESSION)) {
    session_start();
}

// Cadastro1
if(!isset($_SESSION['cpf_usuario'])) {
    header("Location: cadastro1.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro - Etapa 2</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <div class="container">
        <div class="form-box">
            <h2>Cadastro de Login - Etapa 2</h2>
            
            <form method="POST" action="salvar_login.php">
                <div class="campo">
                    <label>Login:</label>
                    <input type="text" name="login" required>
                </div>
                
                <div class="campo">
                    <label>Senha:</label>
                    <input type="password" name="senha" required>
                </div>
                
                <button type="submit" class="btn">Finalizar Cadastro</button> <!-- Vai para login -->
            </form>
        </div>
    </div>
</body>
</html>