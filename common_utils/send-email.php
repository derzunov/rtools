<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require $_SERVER["DOCUMENT_ROOT"] . '/tools/vendors/PHPMailer/src/PHPMailer.php';
require $_SERVER["DOCUMENT_ROOT"] . '/tools/vendors/PHPMailer/src/Exception.php';
require $_SERVER["DOCUMENT_ROOT"] . '/tools/vendors/PHPMailer/src/SMTP.php';

$email = new PHPMailer( true );

echo file_get_contents( '.mailfile.config' );

try {
  //Server settings
  $email->SMTPDebug = SMTP::DEBUG_SERVER; //Enable verbose debug output
  $email->isSMTP();                                            //Send using SMTP
  $email->Host       = 'mail.hosting.reg.ru';                     //Set the SMTP server to send through
  $email->SMTPAuth   = true;                                   //Enable SMTP authentication
  $email->Username   = 'market@r-color.ru';                     //SMTP username
  $email->Password   = file_get_contents( '.mailfile.config' );                               //SMTP password
  $email->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
  $email->Port       = 465;                                    //TCP port to connect to; use 587 if you have
  $email->CharSet = 'UTF-8';
  
  //set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`


  //Recipients
  $email->setFrom('de-test@r-color.ru', 'DE TEST Mailer');
  $email->addAddress('test@r-color.ru', 'DE TEST recipient');     //Add a recipient
  // $mail->addReplyTo('info@example.com', 'Information');


  //Attachments
  // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
  // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name


  //Content
  $email->isHTML(true);                                  //Set email format to HTML
  $email->Subject = 'Here is the subject';
  $email->Body    = 'This is the HTML message body <b>in bold!</b>';
  $email->AltBody = 'This is the body in plain text for non-HTML mail clients';

  
  $email->send();
  echo 'Message has been sent';
} catch ( Exception $e ) {
  echo "Message could not be sent. Mailer Error: { $email->ErrorInfo }";
}
?>