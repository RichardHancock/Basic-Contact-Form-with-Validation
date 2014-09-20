<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Contact Form Demo</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>
	<h1>Contact Form Demo Page</h1>

	<form action="validation.php" method="post">
		<label>
			Name: <input type="text" name="Name" 
			<?php 
				if($errors['name']) {
					echo('class="error" placeholder="'.$errorStrings["name"].'">');
				} else {
					echo('class="formInput" placeholder="Name">');
				}
			?>
		</label><br>

		<label>
			Email: <input type="email" name="Email" 
			<?php 
				if($errors['email']) {
					echo('class="error" placeholder="'.$errorStrings["email"].'">');
				} else {
					echo('class="formInput" placeholder="Email">');
				}
			?>
		</label><br>

		<label>
			Subject: <input type="text" name="Subject" 
			<?php 
				if($errors['subject']) {
					echo('class="error" placeholder="'.$errorStrings["subject"].'">');
				} else {
					echo('class="formInput" placeholder="Subject">');
				}
			?>
		</label><br>
		
		<label>
			Message: <textarea required name="Message" 
			<?php 
				if($errors['message']) {
					echo('class="error" placeholder="'.$errorStrings["message"].'">');
				} else {
					echo('class="formInput" placeholder="Message">');
				}
			?>
			</textarea>
		</label><br>

		<?php
			require_once('recaptchalib.php');
 			$publickey = "your_public_key";
  			echo recaptcha_get_html($publickey);
  		?>
  		<br>
		<input type="submit" name="Submit" value="Submit">
		<span class="formResult">
			<?php 
				if($errors['captcha']) {
					echo($errorStrings['captcha']);
				} elseif ($errorCount != 0) {
					echo("Please fix any mistakes and resubmit");
				} elseif (!$result) {
					echo("Internal Server Error: Email did not send");
				} elseif ($result) {
					echo("Email was sent successfully!");
				}
			?>
		</span> 
	</form>

	<?php
	if (isset($_POST["submit"]))
	{
		//Functions
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

		//Initialize Error bool array
		$errors = array(
			'name' => false,
			'email' => false,
			'subject' => false,
			'message' => false,
			'captcha' => false,
			 );

		//Initialize error message array
		$errorStrings = array(
			'name' => "Please insert your name", 
			'email' => "Please insert a valid email address",
			'subject' => "Please insert a subject",
			'message' => "Please insert a message",
			'captcha' => "", //Gets generated later by the captcha php
		);


		//Recaptcha required code:
		require_once('recaptchalib.php');
		$privatekey = "your_private_key"; //CHANGE THIS to your own key
		$resp = recaptcha_check_answer ($privatekey,
		                                $_SERVER["REMOTE_ADDR"],
		                                $_POST["recaptcha_challenge_field"],
		                                $_POST["recaptcha_response_field"]);

		if (!$resp->is_valid) {
		    // What happens when the CAPTCHA was entered incorrectly
		    //die ("The reCAPTCHA wasn't entered correctly. Go back and try it again." .
		         //"(reCAPTCHA said: " . $resp->error . ")");
			$errors['captcha'] = $resp->error;
		}

		//Fetch Value from the forms POST data
		$name = $_POST["Name"];
		$email = $_POST["Email"];
		$subject = $_POST["Subject"];
		$message = $_POST["Message"];

		$mailTo = "EMAILADDRESS"; // CHANGE THIS to your own email address

		
		//Error checking section
		//Section might be able to be improved like this: $errors['name'] = !hasContent($name);
		if (!hasContent($name))
		{
			$errors['name'] = true;
		}

		// This should check most emails but might not accept rarer email formats.
		if (!hasContent($email) || !preg_match('/^([A-Z0-9\.\-_]+)@([A-Z0-9\.\-_]+)?([\.]{1})([A-Z]{2,6})$/i',$email)) 
		{
			$errors['email'] = true;
		}

		if (!hasContent($subject)) 
		{
			$errors['subject'] = true;
		}

		if (!hasContent($message))
		{
			$errors['message'] = true;
		}


		$errorCount = 0;

		foreach ($errors as $key => $value) {
			
			if ($value) {
				$errorCount++;
			}

		}

		// Final check and Email Sender
		if ($errorCount == 0 && strtoupper($mailTo) != "EMAILADDRESS") {
			
			//Email Construction
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers .= 'From: '.$email."\r\n";
			$headers .= 'Reply-To: '.$email."\r\n";

			$body = "<html><body><b>Name:</b>".$name."<br>";
			$body .= "<b>Email:</b>".$email."<br>";
			$body .= "<b>Message:</b>"."<br>";
			$body .= $message."<br></body></html>";

			//CF is a tag you can set you mail program to auto allow
			$finalSubject = "CF: ".$subject;

			$result = mail($mailTo, $finalSubject, $body, $headers);

		}
	}
?>
</body>
</html>