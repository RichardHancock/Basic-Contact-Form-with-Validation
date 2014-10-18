<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Contact Form Demo</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>
	<h1>Contact Form Demo Page</h1>

	<?php
		
		// type of form input; isTextarea is a bool which handles a special case
		function displayFormError($type, $isTextarea)
		{
			global $errors, $errorStrings, $formData;
			
			if($isTextarea) {
				if($errors[$type]) {
					$returnString = 'class="error" placeholder="'.$errorStrings[$type].'"></textarea>';
				} else {
					$returnString = 'class="formInput" placeholder="'.$type.'">'.$formData[$type].'</textarea>';
				}
			} else {
				if($errors[$type]) {
					$returnString = 'class="error" placeholder="'.$errorStrings[$type].'">';
				} else {
					$returnString = 'class="formInput" placeholder="'.$type.'" value="'.$formData[$type].'">';
				}
			}
			return $returnString;
		}
	
	
		if (isset($_POST["Submitted"]))
		{
			//Functions
			function hasContent($input)
			{
				if (strlen($input) <= 0) {
					return false;
				}
				else
				{
					return true;
				}
			}
			
		//Initialize Error bool array
		$errors = array(
			'Name' => false,
			'Email' => false,
			'Subject' => false,
			'Message' => false,
			'Captcha' => false,
		);

		//Initialize error message array
		$errorStrings = array(
			'Name' => "Please insert your name", 
			'Email' => "Please insert a valid email address",
			'Subject' => "Please insert a subject",
			'Message' => "Please insert a message",
			'Captcha' => "", //Gets generated later by the captcha php
		);
			
			//Recaptcha required code:
			require_once('recaptchalib.php');
			$privatekey = ""; //CHANGE THIS to your own key
			$resp = recaptcha_check_answer ($privatekey,
			                                $_SERVER["REMOTE_ADDR"],
			                                $_POST["recaptcha_challenge_field"],
			                                $_POST["recaptcha_response_field"]);

			if (!$resp->is_valid) {
			    // What happens when the CAPTCHA was entered incorrectly
			    //die ("The reCAPTCHA wasn't entered correctly. Go back and try it again." .
			         //"(reCAPTCHA said: " . $resp->error . ")");
				$errors['Captcha'] = true;
				
				//Bit lazy but couldn't find a reliable source stating the error codes, but this is good enough for users of the form
				if ($resp->error == "incorrect-captcha-sol") {
					$errorStrings['Captcha'] = "Incorrect captcha was entered";
				} else {
					$errorStrings['Captcha'] = "An unknown server error within captcha has occurred!";
					error_log("Server Captcha Error occured",0); //Log an event
				}
			}
			
			//Fetch Value from the forms POST data		
			$formData = array(
				'Name' => $_POST["Name"],
				'Email' => $_POST["Email"],
				'Subject' => $_POST["Subject"],
				'Message' => $_POST["Message"],
			);
			
			$mailTo = ""; // CHANGE THIS to your own email address

			//Error checking section
			//Section might be able to be improved like this: $errors['name'] = !hasContent($name), Would need to add $errorCount++ to hasContent function
			$errorCount = 0;
			if (!hasContent($formData['Name']))
			{
				$errorCount++;
				$errors['Name'] = true;
			}

			// This should check most emails but might not accept rarer email formats.
			if (!hasContent($formData['Email']) || !preg_match('/^([A-Z0-9\.\-_]+)@([A-Z0-9\.\-_]+)?([\.]{1})([A-Z]{2,6})$/i',$formData['Email'] ) ) 
			{
				$errorCount++;
				$errors['Email'] = true;
			}

			if (!hasContent($formData['Subject']))
			{
				$errorCount++;
				$errors['Subject'] = true;
			}

			if (!hasContent($formData['Message']))
			{
				$errorCount++;
				$errors['Message'] = true;
			}

			// Final check and Email Sender
			if ($errorCount == 0) {
				
				//Email Construction
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				$headers .= 'From: '.$formData['Email']."\r\n";
				$headers .= 'Reply-To: '.$formData['Email']."\r\n";

				$body = "<html><body><b>Name:</b>".$formData['Name']."<br>";
				$body .= "<b>Email:</b>".$formData['Email']."<br>";
				$body .= "<b>Message:</b>"."<br>";
				$body .= $formData['Message']."<br></body></html>";

				//CF is a tag you can set you mail program to auto allow
				$finalSubject = "CF: ".$formData['Subject'];

				$result = 0;
				if (mail($mailTo, $finalSubject, $body, $headers)) {
					$result = 1;
				} else {
					$result = 2;
				}

			}
		}
	?>

	<form action="demo.php" method="post">
		<label>
			Name: <input required type="text" name="Name" <?php echo(displayFormError('Name',false)); ?>
		</label><br>

		<label>
			Email: <input required type="email" name="Email" <?php echo(displayFormError('Email',false)); ?>
		</label><br>

		<label>
			Subject: <input required type="text" name="Subject" <?php echo(displayFormError('Subject',false)); ?>
		</label><br>
		
		<label>
			Message: <textarea required name="Message" <?php echo(displayFormError('Message',true)); ?>
		</label><br>

		<?php
			require_once('recaptchalib.php');
 			$publickey = ""; //CHANGE THIS to your own key
  			echo recaptcha_get_html($publickey);
  		?>

  		<input type="hidden" name="Submitted" value=1>
		<input type="submit" name="Submit" value="Submit"><br>
		<span class="formResult">
			<?php 
				if($errors['Captcha']) {
					echo($errorStrings['Captcha']);
				} elseif ($errorCount != 0) {
					echo("Please fix any mistakes and resubmit");
				} elseif ($result == 2) {
					echo("Internal Server Error: Email did not send");
				} elseif ($result == 1) {
					echo("Email was sent successfully!");
				}
			?>
		</span> 
	</form>
</body>
</html>