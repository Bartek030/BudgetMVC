<?php

namespace App;

use \PHPMailer\PHPMailer\PHPMailer;
use \PHPMailer\PHPMailer\SMTP;
use \PHPMailer\PHPMailer\Exception;

/**
 * Mail
 * 
 * PHP version 7.0
 */

 class Mail {
    /**
     * Send a message
     * 
     * @param string $to Recipient
     * @param string $subject Subject
     * @param string $text Text-only content of the message
     * @param string $html HTML content of the message
     * 
     * @return mixed
     */
   public static function send($to, $subject, $text, $html, $name) {
      $mail = new PHPMailer(true);
      $mail->CharSet = "UTF-8";

      try {
         //Server settings
         //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
         $mail->isSMTP();                                            //Send using SMTP
         $mail->Host       = Config::MAIL_HOST;                     //Set the SMTP server to send through
         $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
         $mail->Username   = Config::MAIL_USER;                     //SMTP username
         $mail->Password   = Config::MAIL_PASSWORD;                               //SMTP password
         $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
         $mail->Port       = 587;                                    //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
     
         //Recipients
         $mail->setFrom(Config::MAIL_USER, Config::MAIL_NAME);
         $mail->addAddress($to, $name);
     
         //Attachments
         //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
         //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
     
         //Content
         $mail->isHTML(true);                                  //Set email format to HTML
         $mail->Subject = $subject;
         $mail->Body    = $html;
         $mail->AltBody = $text;
        
         $mail->send();
         //echo 'Message has been sent';
     } catch (Exception $e) {
         echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
     }
   }
 }