<?php

namespace App\Services;

use App\Exceptions\EmailNotSendedException;
use PHPMailer\PHPMailer\PHPMailer;

class EmailService
{
    public function enviarEmail(string $para, string $assunto, string $mensagem): void
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $_ENV['MAIL_HOST'] ?? 'smtp.example.com';
            $mail->Port = $_ENV['MAIL_PORT'] ?? 587;
            $mail->Username = $_ENV['MAIL_USERNAME'] ?? 'seu_email';
            $mail->Password = $_ENV['MAIL_PASSWORD'] ?? 'sua_senha';
            $mail->setFrom(
                $_ENV['MAIL_FROM_EMAIL'] ?? 'sitema@exemple.com', 
                $_ENV['MAIL_FROM_NAME'] ?? 'Freddy App'
            );
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->SMTPAuth = true;
            $mail->CharSet = 'UTF-8';
            $mail->addAddress($para);
            $mail->isHTML(true);
            $mail->Subject = $assunto;
            $mail->Body = $mensagem;

            $mail->send();
        } catch (\Exception $e) {
            throw new EmailNotSendedException("Erro ao enviar email: " . $mail->ErrorInfo);
        }
    }
}