<!DOCTYPE html>
<html lang="en-US">
<head>
	<meta charset="UTF-8" />
	<link rel="stylesheet" type="text/css" href="contact.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<script src="contact.js"></script>
	<title>Contact Form</title>
</head>
<body>
	<form id="contact_form" action="send.php" method="POST">
		<noscript><input type="hidden" name="nojs" value="true"></noscript>
<?php
		if ( isset( $_GET[ "response" ] ) ) {
			if ( $_GET[ "response" ] == 1 ) {
				echo '<span id="contactResponse" class="success" style="display:block"><p>Your message was sent successfully!</p></span>';
			}
			else {
				$resp =  '<span id="contactResponse" class="error" style="display:block"><p>';
				if ( $_GET[ "response" ] == 2 ) {
					$resp .= 'Ensure that all required fields are complete and accurate.';
				}
				else {
					$resp .= 'An unexpected error occurred. Please try again later.';
				}
				$resp .= '</p></span>';
				echo $resp;
			}
		}
?>
		<span id="responseMessage"></span>
		<div class="row">
			<span id="nameError" class="error"></span>
			<label class="required" for="name">Name:</label><br />
			<input type="text" name="name" id="name" class="input" maxlength="100" required /><br />
		</div>
		<div class = "row hp">
			<label for="website">Do not put anything here:</label><br />
			<input type="text" name="url" id="url" class="input" maxlength="256" /><br />
		</div>
		<div class="row">
			<span id="emailError" class="error"></span>
			<label class="required"	for="email">Email:</label><br />
			<input type="text" name="email" id="email" class="input" maxlength="254" required /><br />
		</div>
		<div class="row">
			<span id="messageError" class="error"></span>
			<label class="required" for="message">Message:</label><br />
			<textarea name="message" id="message" class="input" rows="7" maxlength="3000" required></textarea><br />
		</div>
		<input type="submit" id="submit_button" value="Send" />
	</form>
</body>
</html>
