<?php

require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function enviarEmail(string $destinatario, string $assunto, string $mensagemHTML): bool {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp-relay.brevo.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = '8f5489001@smtp-brevo.com';
        $mail->Password   = 'WZaHbLOSGthkBJQ6';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->SMTPOptions = [
          'ssl' => [
            'verify_peer'       => false,
            'verify_peer_name'  => false,
            'allow_self_signed' => true,
          ]
        ];

        $mail->setFrom('mauro@mavpaz.com.br', 'Sistema APPProdução');
        $mail->addAddress($destinatario);

        $mail->isHTML(true);
        $mail->Subject = $assunto;
        $mail->Body    = $mensagemHTML;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
