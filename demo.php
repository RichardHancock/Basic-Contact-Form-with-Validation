<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Contact Form Demo</title>
</head>
<body>
	<h1>Contact Form Demo Page</h1>
	<form action="validation.php" method="post">
		<label>
			Name: <input type="text" name="Name" placeholder="e.g. Bob">
		</label>
		<span class="Error">Please insert your name!</span>

		<label>
			Email: <input type="email" name="Email" placeholder="e.g. example@examp.le">
		</label>
		<span class="Error">Please insert a valid Email Address!</span>

		<label>
			Subject: <input type="text" name="Subject" placeholder="Your Subject Here">
		</label>
		<span class="Error">Please insert a subject line!</span>
		
		<label>
			Message: <textarea required name="Message" placeholder="Your Message Here."></textarea>
		</label>
		<span class="Error">Please insert a message!</span>

		<?php
			require_once('recaptchalib.php');
 			$publickey = "your_public_key";
  			echo recaptcha_get_html($publickey);
  		?>
		<input type="submit" name="Submit" value="Submit">
	</form>
</body>
</html>