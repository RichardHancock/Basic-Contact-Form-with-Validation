<?php
	if (isset($_POST["submit"]))
	{
		//Recapcha required code:
		require_once('recaptchalib.php');
		$privatekey = "your_private_key";
		$resp = recaptcha_check_answer ($privatekey,
		                                $_SERVER["REMOTE_ADDR"],
		                                $_POST["recaptcha_challenge_field"],
		                                $_POST["recaptcha_response_field"]);

		if (!$resp->is_valid) {
		    // What happens when the CAPTCHA was entered incorrectly
		    die ("The reCAPTCHA wasn't entered correctly. Go back and try it again." .
		         "(reCAPTCHA said: " . $resp->error . ")");
		} else {
		    // Your code here to handle a successful verification

		}




		$name = $_POST["Name"];
		$email = $_POST["Email"];
		$subject = $_POST["Subject"];
		$message = $_POST["Message"];

		$mailTo = "DEBUG";

		function hasContent($input)
		{
			if ($input.len <= 0) {
				return false;
			}
			else
			{
				return true;
			}
		}


		if (!hasContent($name))
		{
			//Error
		}

		// This should check most emails but might not accept rarer email formats.
		if (!hasContent($email) || !preg_match('/^([A-Z0-9\.\-_]+)@([A-Z0-9\.\-_]+)?([\.]{1})([A-Z]{2,6})$/i',$email)) 
		{
			// Error
		}

		if (!hasContent($subject)) 
		{
			//Error
		}

		if (!hasContent($message))
		{
			//Error
		}


		//Email Construction
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: '.$email."\r\n";
		$headers .= 'Reply-To: '.$email."\r\n";

		$body = "<html><body><b>Name:</b>".$name."<br>";
		$body .= "<b>Email:</b>".$email."<br>";
		$body .= "<b>Message:</b>"."<br>";
		$body .= $message."<br></body></html>";

		//CF154 is a tag you can set you mail program to auto allow
		$finalSubject = "CF154: ".

		if (!strtoupper($mailTo) == "DEBUG")
		{
			mail($mailTo, $finalSubject, $body, $headers);
		}
	}
?>