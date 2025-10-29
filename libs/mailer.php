<?php
// Lightweight PHPMailer bootstrap
require_once __DIR__ . '/../config.php';

// 1) Try Composer autoload first if available
$autoloadPaths = [
    // common composer autoload locations
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../../vendor/autoload.php',
];
foreach($autoloadPaths as $autoload){
    if(file_exists($autoload)){
        require_once $autoload;
        break;
    }
}

// 2) If Composer isn't available, try bundled PHPMailer in libs/PHPMailer/src
if(!class_exists('PHPMailer\\PHPMailer\\PHPMailer')){
    $phpmailerMain = __DIR__ . '/PHPMailer/src/PHPMailer.php';
    $phpmailerSMTP = __DIR__ . '/PHPMailer/src/SMTP.php';
    $phpmailerException = __DIR__ . '/PHPMailer/src/Exception.php';
    if(file_exists($phpmailerMain) && file_exists($phpmailerSMTP) && file_exists($phpmailerException)){
        require_once $phpmailerException;
        require_once $phpmailerSMTP;
        require_once $phpmailerMain;
    }
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function create_mailer(): ?PHPMailer {
    if(!class_exists('PHPMailer\\PHPMailer\\PHPMailer')){
        return null; // PHPMailer not available
    }
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USER;
    $mail->Password = SMTP_PASS;
    $mail->SMTPSecure = SMTP_SECURE; // 'tls' or 'ssl'
    $mail->Port = SMTP_PORT;
    $mail->CharSet = 'UTF-8';
    $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
    return $mail;
}

function send_mail($toEmail, $toName, $subject, $htmlBody, $textBody = ''){
    $mailer = create_mailer();
    if($mailer === null){
        return [ 'status' => 'failed', 'msg' => 'Mailer not available. Install via Composer (preferred) or copy PHPMailer to libs/PHPMailer.' ];
    }
    try{
        $mailer->clearAddresses();
        $mailer->addAddress($toEmail, $toName);
        $mailer->isHTML(true);
        $mailer->Subject = $subject;
        $mailer->Body = $htmlBody;
        $mailer->AltBody = $textBody ?: strip_tags($htmlBody);
        $mailer->send();
        return [ 'status' => 'success' ];
    }catch(Exception $e){
        return [ 'status' => 'failed', 'msg' => $e->getMessage() ];
    }
}
?>

