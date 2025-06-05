<?php

namespace App\adms\Helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class SendEmailService
{
    public static function sendEmail(string $email, string $name, string $subject, string $body, string $altBody) : bool
    {
        $mail = new PHPMailer(true);

        try {
            //Configurações do servidor
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                   //Habilita saída de depuração detalhada
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();                                            //Envia via SMTP
            $mail->Host       = $_ENV['MAIL_HOST'];                     //Define o servidor SMTP para enviar
            $mail->SMTPAuth   = true;                                   //Habilita autenticação SMTP
            $mail->Username   = $_ENV['MAIL_USERNAME'];                 //Nome de usuário SMTP
            $mail->Password   = $_ENV['MAIL_PASSWORD'];                 //senha SMTP
            $mail->SMTPSecure = $_ENV['MAIL_ENCRYPTI'];                 //Habilita criptografia TLS implícita
            $mail->Port       = $_ENV['MAIL_PORT'];

            //Recipients
            $mail->setFrom($_ENV['EMAIL_TI'], $_ENV['NAME_EMAIL_TI']);
            $mail->addAddress($email, $name);                           //Adiciona um destinatário

            //Content
            $mail->isHTML(true);                                        //Define o formato do e-mail para HTML
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = $altBody;

            $mail->send();

            GenerateLog::generateLog("info", "Email enviado com sucesso.", ['email' => $email, 'subject' => $subject]);

            return true;

        } catch (Exception $e) {
            GenerateLog::generateLog("error", "Email não enviado", ['email' => $email, 'error' => $e->getMessage()]);
            return false;
        }
    }
}
