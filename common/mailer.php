<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './phpmailer/src/Exception.php';
require './phpmailer/src/PHPMailer.php';
require './phpmailer/src/SMTP.php';

function smtp_mailer($to, $subject, $msg)
{
    $log_file = './mail_log.txt';  // Define log file location
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'ssl';
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 465;
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    $mail->IsHTML(true);
    $mail->Username = "gm3buildershris@gmail.com";
    $mail->Password = "jadv iloo ufiy tjyi";
    $mail->SetFrom("gm3buildershris@gmail.com");
    $mail->Subject = $subject;
    $mail->Body = $msg;
    $mail->AddAddress($to);
    
    if ($mail->Send()) {
        // Log success message
        file_put_contents($log_file, "Mail sent successfully to $to\n", FILE_APPEND);
        return true;
    } else {
        
        // Log error message
        file_put_contents($log_file, "Failed to send mail: " . $mail->ErrorInfo . "\n", FILE_APPEND);
        return false;
    }
}