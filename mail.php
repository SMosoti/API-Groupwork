<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


require  'C:/Apache24/htdocs/pro/Plugins/PHPMailer/vendor/autoload.php';



$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      
    $mail->isSMTP();                                            
    $mail->Host       = 'smtp.gmail.com';                     
    $mail->SMTPAuth   = true;                                   
    $mail->Username   = 'sherly.mosoti@strathmore.edu';                     
    $mail->Password   = 'oksi juad idbr ytoi';                               
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            
    $mail->Port       = 465;                                    

    //Recipients
    $mail->setFrom('sherly.mosoti@strathmore.edu', 'Sherly Mosoti');
    $mail->addAddress('birungi.jannie@strathmore.edu', 'Jannie Birungi');     
   // $mail->addAddress('ellen@example.com');              
    //$mail->addReplyTo('info@example.com', 'Information');
    //$mail->addCC('cc@example.com');
   // $mail->addBCC('bcc@example.com');

    //Attachments
    //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'Confirmation of Attendance';
    
    $mail->Body    = ' Dear Jannie,<br> 
    We are pleased to confirm your reservation for  Riverside Residences from  21st of October 2025 to 30th October 2025 ...</b>';
   // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    echo 'Message sent successfully';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}