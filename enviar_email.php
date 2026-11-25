<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function enviarEmailRecuperacao($destinatario, $token) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = ''; // E-mail que já funciona
        $mail->Password   = ''; // A senha de app
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom('', 'AnnaFell');
        $mail->addAddress($destinatario);

        $mail->isHTML(true);
        $mail->Subject = 'Recuperacao de Senha - AnnaFell';
        $mail->Body    = "
            <div style='background-color: #1a0b2e; padding: 30px; font-family: Arial, sans-serif; color: #ffffff;'>
                <h2 style='color: #c77dff;'>AnnaFell</h2>
                <p>Seu codigo de recuperacao e: <b style='font-size: 20px; color: #c77dff;'>$token</b></p>
                <p>Insira este codigo no site para criar uma nova senha.</p>
            </div>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        file_put_contents('erro_email.log', date('Y-m-d H:i:s') . ' - Erro: ' . $mail->ErrorInfo . "\n", FILE_APPEND);
        return false;
    }
}
?>