<?php

namespace Services;
use Config\Phpmailer\PHPMailer;

class Email {

    public static function send($emails = [], $site = '', $subject = '', $message = '', $file = [])
    {
		$email = getEnvelopment('CONFIG_EMAIL');

		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->SMTPAuth   = true;
		$mail->SMTPSecure = "ssl";
		$mail->Host       = $email['SMTP_HOST'];
		$mail->Username   = $email['SMTP_USERNAME'];
		$mail->Password   = $email['SMTP_PASSWORD'];
		$mail->Port       = $email['SMTP_PORT'];
		$mail->CharSet 	= 'UTF-8';
		$mail->SMTPDebug  = 0;

		foreach($_FILES as $value) {
			$mail->AddAttachment($value['tmp_name'], $value['name']);
		}

		$mail->From 	= $email['SMTP_FROM'];
		$mail->FromName = $site;
		$mail->Subject 	= $subject;

		foreach ($emails as &$value) {
			$mail->AddAddress($value);
		}

		$mail->Body = $message;
		$mail->IsHTML(true);
		return $mail->Send();
    }
}