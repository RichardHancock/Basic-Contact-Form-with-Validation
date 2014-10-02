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
			$privatekey = ""; //CHANGE THIS to your own key
			$resp = recaptcha_check_answer ($privatekey,
			                                $_SERVER["REMOTE_ADDR"],
			                                $_POST["recaptcha_challenge_field"],
			                                $_POST["recaptcha_response_field"]);

			if (!$resp->is_valid) {
			    // What happens when the CAPTCHA was entered incorrectly
			    //die ("The reCAPTCHA wasn't entered correctly. Go back and try it again." .
			         //"(reCAPTCHA said: " . $resp->error . ")");
				$errors['captcha'] = true;
				if ($resp->error == "incorrect-captcha-sol") 
				{
					$errorStrings['captcha'] = "Incorrect captcha was entered";
				} else {
					$errorStrings['captcha'] = "An unknown error within captcha has occurred";
				}
			}

			//Fetch Value from the forms POST data
			$name = $_POST["Name"];
			$email = $_POST["Email"];
			$subject = $_POST["Subject"];
			$message = $_POST["Message"];

			$mailTo = ""; // CHANGE THIS to your own email address

			//Error checking section
			//Section might be able to be improved like this: $errors['name'] = !hasContent($name);
			if (hasContent($name) == false)
			{
				$errors['name'] = true;
			}

			// This should check most emails but might not accept rarer email formats.
			if (hasContent($email) == false || !preg_match('/^([A-Z0-9\.\-_]+)@([A-Z0-9\.\-_]+)?([\.]{1})([A-Z]{2,6})$/i',$email)) 
			{
				$errors['email'] = true;
			}

			if (hasContent($subject) == false) 
			{
				$errors['subject'] = true;
			}

			if (hasContent($message) == false)
			{
				$errors['message'] = true;
			}


			$errorCount = 0;

			foreach ($errors as $key => $value) {
				
				if ($value == true) {
					$errorCount++;
				}
			
			}

			// Final check and Email Sender
			if ($errorCount == 0) {
				
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
			Name: <input required type="text" name="Name" 
			<?php 
				if($errors['name']) {
					echo('class="error" placeholder="'.$errorStrings["name"].'">');
				} else {
					echo('class="formInput" placeholder="Name" value="'.$name.'">');
				}
			?>
		</label><br>

		<label>
			Email: <input required type="email" name="Email" 
			<?php 
				if($errors['email']) {
					echo('class="error" placeholder="'.$errorStrings["email"].'">');
				} else {
					echo('class="formInput" placeholder="Email" value="'.$email.'">');
				}
			?>
		</label><br>

		<label>
			Subject: <input required type="text" name="Subject" 
			<?php 
				if($errors['subject']) {
					echo('class="error" placeholder="'.$errorStrings["subject"].'">');
				} else {
					echo('class="formInput" placeholder="Subject" value="'.$subject.'">');
				}
			?>
		</label><br>
		
		<label>
			Message: <textarea required name="Message" 
			<?php 
				if($errors['message']) {
					echo('class="error" placeholder="'.$errorStrings["message"].'"></textarea>');
				} else {
					echo('class="formInput" placeholder="Message">'.$message.'</textarea>');
				}
			?>
			
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
				if($errors['captcha']) {
					echo($errorStrings['captcha']);
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