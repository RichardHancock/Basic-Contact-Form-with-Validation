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
//Settings
$privatekey = ""; //Change to your own key
$publickey = ""; //Change to your own key
$mailTo = ""; // Change to your own email address

// type of form input; isTextarea is a bool which handles a special case
// TODO: There must be a better way to do this.
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
		global $errorCount;
        if (strlen($input) > 0) {
            return true;
        }
        else
        {
			$errorCount++;
            return false;
        }
    }
	
	function isValidEmail($input)
	{
		global $errorCount;
		// This should accept most emails but might not accept rarer email address formats.
		if (preg_match('/^([A-Z0-9\.\-_]+)@([A-Z0-9\.\-_]+)?([\.]{1})([A-Z]{2,6})$/i', $input))
		{
			return true;
		}
		else
		{
			$errorCount++;
			return false;
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
    $resp = recaptcha_check_answer ($privatekey,
        $_SERVER["REMOTE_ADDR"],
        $_POST["recaptcha_challenge_field"],
        $_POST["recaptcha_response_field"]
	);

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

	//Error checking section
	$errorCount = 0;
	   
    $errors['Name'] = !hasContent($formData['Name']);
    $errors['Email'] = !hasContent($formData['Email']) || !isValidEmail($formData['Email']);
    $errors['Subject'] = !hasContent($formData['Subject']);
    $errors['Message'] = !hasContent($formData['Message']);

    // Final check and Sending the Email
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

		// Show a message based on if the email sent or not
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