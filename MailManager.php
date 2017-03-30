<?php
error_reporting(E_ALL); 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
set_time_limit(727379969);
require_once __DIR__.'/lib/swift_required.php';

class MailManager
{
	public function __construct(){
		// Create the Transport
		$this->transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, 'ssl')
		  ->setUsername(FROM_EMAIL)
		  ->setPassword(EMAIL_PASSWORD);
	}

	public function sendEmail($to, $subject, $message){
		$mailer = Swift_Mailer::newInstance($this->transport);
		// Create a message
		$message = Swift_Message::newInstance('Wonderful Subject')
		  ->setFrom(array(FROM_EMAIL => FROM_EMAIL_TITLE))
		  ->setTo(array($to))
		  ->setSubject($subject)
		  ->setContentType("text/html")
		  ->setBody($message)
		  ;

		// Send the message
		$result = $mailer->send($message);
	}
}
