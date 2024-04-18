<?php 
include '/var/www/trinityapplab.in/html/PHPMailerAutoload.php';

class SendMailClass{
	
	public function sendMail($toMailId, $subject, $msg, $attachment){
		$status = false;
	    $message = $msg;
	    $mail = new PHPMailer;
	    $mail->isSMTP();                                      
	    $mail->Host = 'smtp.gmail.com';
	    $mail->SMTPAuth = true;
	    $mail->Username = 'communication@trinityapplab.co.in';
	    $mail->Password = 'communication@Trinity';   
	    $mail->Port = 587;
	    $mail->SMTPSecure = 'tls';
	    
	    // To mail's
	    $mail->addAddress($toMailId);
	    // $mail->addAddress("pushkar.tyagi@trinityapplab.co.in");
	    
	    $mail->setFrom("communication@trinityapplab.co.in","Trinity");
	    $mail->addAttachment($attachment);
	    $mail->isHTML(true);   

	    // CC mail's
	    // $mail->addCC('ankita.verma@trinityapplab.co.in');
    	// $mail->addCC('ayush.agarwal@trinityapplab.co.in');
    	// $mail->addCC('pushkar.tyagi@trinityapplab.co.in');
	    
	    // BCC mail's
	    // $mail->addBCC("jai.prakash@trinityapplab.co.in");

	    $mail->Subject = $subject;
	    $mail->Body = "$message<br>";
	    
	        
	    if(!$mail->send())
	    {
	        // echo 'Mailer Error: ' . $mail->ErrorInfo;
	        // echo"<br>Could not send";
	        $status = false;
	    }
	    else{
	        // echo "mail sent";
	        $status = true;
	    }
	    return $status;
	}
}
?>