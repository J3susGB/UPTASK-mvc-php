<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email {
    protected $email;
    protected $nombre;
    protected $token;

    public function __construct($email, $nombre, $token)
    {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;

    }

    public function enviarConfirmacion() {
        $mail = new PHPMailer();
        $mail->isSMTP();
        // $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->Host = $_ENV['EMAIL_HOST'];
        $mail->SMTPAuth = true;
        // $mail->Port = 2525;
        $mail->Port = $_ENV['EMAIL_PORT'];
        // $mail->Username = 'c9d500a3bcfbee';
        $mail->Username = $_ENV['EMAIL_USER'];
        // $mail->Password = '0da6a37a8c95b8';
        $mail->Password = $_ENV['EMAIL_PASS'];

        $mail->setFrom('cuentas@uptask.com');
        // $mail->addAddress('cuentas@uptask.com', 'uptask.com');
        $mail->addAddress($this->email, 'uptask.com');
        $mail->Subject = 'Confirma tu cuenta en UpTask';

        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido = '<html><body>'; // Abre el cuerpo del HTML
        $contenido .= "<p>¡Hola <strong>" . $this->nombre . "</strong>!<br><br>Has creado tu cuenta en Uptask. Para confirmarla, pulsa el siguiente enlace: <a href='". $_ENV['APP_URL'] . "/confirmar?token=" . $this->token . "'>Confirmar cuenta</a></p>";
        $contenido .= "<p>Si no creaste esta cuenta, ignora este mensaje</p>";
        $contenido .= '</body></html>'; // Cierra el cuerpo y el HTML

        $mail->Body = $contenido;
        $mail->send();
    }

    public function enviarInstrucciones() {
        $mail = new PHPMailer();
        $mail->isSMTP();
        // $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->Host = $_ENV['EMAIL_HOST'];
        $mail->SMTPAuth = true;
        // $mail->Port = 2525;
        $mail->Port = $_ENV['EMAIL_PORT'];
        // $mail->Username = 'c9d500a3bcfbee';
        $mail->Username = $_ENV['EMAIL_USER'];
        // $mail->Password = '0da6a37a8c95b8';
        $mail->Password = $_ENV['EMAIL_PASS'];

        $mail->setFrom('cuentas@uptask.com');
        // $mail->addAddress('cuentas@uptask.com', 'uptask.com');
        $mail->addAddress($this->email, 'uptask.com');
        $mail->Subject = 'Reestablece la contraseña de tu cuenta en UpTask';

        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido = '<html><body>'; // Abre el cuerpo del HTML
        $contenido .= "<p>¡Hola <strong>" . $this->nombre . "</strong>!<br><br>Parece que has olvidado tu constraseña de Uptask. Para recuperarla, pulsa el siguiente enlace: <a href='". $_ENV['APP_URL'] ."/reestablecer?token=" . $this->token . "'>Reestablecer contraseña</a></p>";
        $contenido .= "<p>Si no creaste esta cuenta, ignora este mensaje</p>";
        $contenido .= '</body></html>'; // Cierra el cuerpo y el HTML

        $mail->Body = $contenido;
        $mail->send();
    }
}