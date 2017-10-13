<?php

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    function sendEmail($email,$username){
        $url = 'www.google.com';
        $mail = new PHPMailer();
        $mail->CharSet =  "utf-8";
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->Username = "portalharga.ipb@gmail.com";
        $mail->Password = "portalharga1234";
        $mail->SMTPSecure = "ssl";  
        $mail->Host = "smtp.gmail.com";
        $mail->Port = "465";
     
        $mail->setFrom('portalharga.ipb@gmail.com', 'portal harga');
        $mail->AddAddress($email, $username);
     
        $mail->Subject  =  'using PHPMailer';
        $mail->IsHTML(true);
        $mail->Body    = "The new password is {$url}.
             <br>
             <br>
             ini baris ke tiga
             <br>
             <br>
             ini baris lanjut";
      
         if($mail->Send())
         {
            return "Message was Successfully Send :)";
         }
         else
         {
            return "Mail Error - >".$mail->ErrorInfo;
         }
    }

?>