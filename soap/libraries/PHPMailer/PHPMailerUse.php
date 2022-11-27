<?php
/**
 * Created by PhpStorm.
 * User: rcastillo
 * Date: 08/06/2020
 * Time: 9:52
 */

// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
//use PHPMailer\PHPMailer\PHPMailer;
//use PHPMailer\PHPMailer\Exception;
require_once(dirname(__FILE__) . "/src/PHPMailer.php");
//require(dirname(__FILE__) . "/src/Exception.php");
require_once(dirname(__FILE__) . "/src/SMTP.php");

class PHPMailerUse {

    public function config() {
        $mail = new PHPMailer();
        // Configuracion del servidor de SMTP
        $mail->IsSMTP();                                        // Set mailer to use SMTP
//        $mail->SMTPDebug = 2;                                 // Enable verbose debug output
        $mail->Host = 'smtp.gmail.com';                     // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                                 // Enable SMTP authentication
        $mail->Username = 'rogertcd@gmail.com';    // SMTP username
        $mail->Password = 'xezfldpskxuzkrnc';                         // SMTP password
        $mail->SMTPSecure = 'ssl';                              // Enable TLS encryption, SSL also accepted
//        $mail->SMTPAutoTLS = true;
        $mail->Port = 465;                                      // TCP port to connect to
//        $mail->CharSet = 'UTF-8';                               // Codificacion de caracteres para el mensaje
        $mail->SetFrom('rogertcd@gmail.com', 'Rogert Castillo');

        return $mail;
    }

    /**
     * Envia los correos de alerta en caso de producirse algun error durante un proceso de pago o facturacion
     *
     * @param $to
     * @param $subject
     * @param $message
     * @param null $cc
     * @param null $bcc
     * @param null $attach
     */
    public function sendMail($to, $subject, $message, $cc=NULL, $bcc=NULL, $attach=NULL) {
        // Configurando los parametros para el envio del correo
        $mail = $this->config();
        try {
            //Recipients
            $destinatarios = explode(',', $to);
            foreach ($destinatarios as $key => $destinatario) {
                $mail->addAddress($destinatario);// Add address, Name is optional
            }

//      $mail->addReplyTo('info@example.com', 'Information');
            if ($cc != NULL) $mail->addCC($cc);
            if ($bcc != NULL) $mail->addBCC($bcc);

            //Attachments
            if ($attach != NULL) {
                $archivos = explode('|', $attach);
                foreach ($archivos as $key => $archivo) {
                    $mail->addAttachment($archivo); // Add attachments
                    //      $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
                }
            }

            //Content
            $mail->isHTML(true);// Set email format to HTML
            $mail->Subject = $subject;  // Motivo
            $mail->Body = $message;  // Mensaje
//            $mail->MsgHTML($message);  // Mensaje

            if ($mail->send()) {// Si se envio correctamente
                LOG::write_log("El correo fue enviado correctamente a el(los) destinatario(s): $to .");
            } else {
                LOG::write_error('No se pudo enviar el correo de alerta, motivo: ' . $mail->ErrorInfo);
            }
            $mail->smtpClose();
        } catch (Exception $e) {
            LOG::write_error('Exception, message could not be sent. Mailer Error: ' . $e->getMessage());
        }
    }

}