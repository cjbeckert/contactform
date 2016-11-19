<?php
	/*
		Simple AJAX Contact Form v1.0

		Author: CJ Beckert - cjbeckert.com

		This is a simple PHP contact form that uses JQuery/AJAX to send data between the browser and server script.
		Note that this contact form will still work if Javascript is disabled on the client - It just won't be as pretty.

		How to use:
		This is a simple drop-in contact form. Just update the info in the CHANGE ME section below, and
		ensure that the relative links between the files are ok if you restructure.
		
		Dependencies:
		-PHP (duh)
		-PHP mbstring module because we're working with UTF-8 encoded input - e.g., yum install php-mbstring
		-If using SELinux, ensure sending of mail from httpd is allowed - e.g., setsebool -P httpd_can_sendmail 1
		-Make sure the mail relay specified in your php.ini is configured properly and check that your firewall isn't blocking
		 the necessary port(s)
	*/


	/*
		CHANGE ME
		Update these settings prior to utilizing this contact form
	*/
	
	// Specify the email address where messages are to be sent
	$send_to = "you@yourdomain.com";

	// What subject would you like to have on emails sent
	// NOTE: Make sure to comply with RFC 2047 when specifying subject. Google for more info.
	$subject = "New message from YourDomain.com Contact Form";

	// OPTIONAL: Add additional email headers.
	// In the example below, a fancy no-reply address is used in lieu of the server's default sending address.
	// NOTE: Make sure your server's IP is ok to send emails from any domain specified below, or you will likely hit spam or bounce.
	$headers = "From: no-reply <no-reply@yourdomain.com>" . "\r\n";

	/*
		LET'S GET TO WORK!
	*/

	// Only process POST requests
	if ( $_SERVER[ "REQUEST_METHOD" ] == "POST" ) {

		// Array of data to be returned to client
		$json = array(
			"nameError" => false,
			"emailError" => false,
			"messageError" => false,
			"sendSuccess" => false
		);

		// Make sure name is valid
		if ( isset( $_POST[ "name" ] ) ) {
			try {
				$name = validateText( $_POST[ "name" ], 100, true );
			}
			catch ( Exception $e ) {
				$json[ "nameError" ] = "Name" . $e->getMessage();
			}
		}
		else {
			$json[ "nameError" ] = "Name cannot be blank!";
		}

		// Make sure email is valid
		if ( isset( $_POST[ "email" ] ) ) {
			$email = filter_var( trim( $_POST[ "email" ] ), FILTER_SANITIZE_EMAIL );

			// RFC 2821 limits address lengths to 254 characters. The following filter ensures this, along with format checks.
			// In case you were wondering why I set a 254 max length in the html file..
			if ( !filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
				$json[ "emailError" ] = "Invalid email address!";
			}
		}
		else {
			$json[ "emailError" ] = "Email cannot be blank!";
		}

		// Make sure message is valid
		if ( isset( $_POST[ "message" ] ) ) {
			try {
				$message = validateText( $_POST[ "message" ], 3000, false );
			}
			catch ( Exception $e ) {
				$json[ "messageError" ] = "Message" . $e->getMessage();
			}
		}
		else {
			$json[ "messageError" ] = "Message cannot be blank!";
		}

		// If inputs are good lets proceed with preparing/sending the email
		if ( !$json[ "nameError" ] && !$json[ "emailError" ] && !$json[ "messageError" ] ) {	
			// Check the honey pot
			// There's a url field present in the form that doesn't display on browsers (display: none)
			// The purpose behind this is to stop automated spam-bots from spamming our form
			// These bots will likely not know that the url field should be left blank
			if ( isset( $_POST[ "url" ] ) && $_POST[ "url" ] != "" ) {
				
				// We want the user to think that the message sent successfully
				$json[ "sendSuccess" ] = true;
				if ( isset( $_POST[ "nojs" ] ) ) {
					echo "Message sent successfully!";
				}
			}
			else {
				// We're all good to send an email
				// Collect additional user info to send in our email
				$ua = $_SERVER[ "HTTP_USER_AGENT" ];
				$ip = $_SERVER[ "REMOTE_ADDR" ] . " - " . gethostbyaddr( $_SERVER[ "REMOTE_ADDR" ] );

				// Build the email content
				$email_content = "Name: $name\n";
				$email_content .= "Email: $email\n";
				$email_content .= "Message:\n$message\n\n";
				$email_content .= "User Agent: $ua\n";
				$email_content .= "User IP: $ip";
				
				// Send the message
				if ( mail( $send_to, $subject, $email_content, $headers ) ) {
					$json[ "sendSuccess" ] = true;

					// Display a message for our non-JS friends
					if ( isset( $_POST[ "nojs" ] ) ) {
						echo "Message sent successfully!";
					}
				}
				else {
					if ( isset( $_POST[ "nojs" ] ) ) {
						echo "An unexpected error occurred. Please try again.";
					}
				}
			}
		}
		else {
			// Print errors to client screen if not utilizing AJAX
			if ( isset( $_POST[ "nojs" ] ) ) {
				$errorResponse = "";
				if ( $json[ "nameError" ] ) {
					$errorResponse .= $json[ "nameError" ] . "\n";
				}
				if ( $json[ "emailError" ] ) {
					$errorResponse .= $json[ "emailError" ] . "\n";
				}
				if ( $json[ "messageError" ] ) {
					$errorResponse .= $json[ "messageError" ];
				}
				echo $errorResponse;
			}
		}
		
		// Return our array to the client to let them know how things went		
		if ( !isset( $_POST[ "nojs" ] ) ) {
			header( "Content-Type: application/json" );
			echo json_encode( $json );
		}
	}
	else {
	
		// Didn't receive a POST request so throw a 403
		http_response_code(403);
	
	}
	
	/*
		Helpful functions
	*/
	// Ensure text field is not empty, remove naughty characters, and enforce
	// character length limit
	function validateText( $text, $maxLength, $removeLineBreaks ) {

		$text = strip_tags( trim( $text ) );

		if ( $removeLineBreaks ) {
			$text = str_replace( array( "\r","\n" ),array( " "," " ), $text );
		}
		else {
			// Line breaks are cool, but we don't want more than 2 in a row
			$text = preg_replace( '~(\R{2})\R+~', '$1', $text );
		}

		if ( mb_strlen( $text ) > $maxLength ) {
			throw new Exception( " maximum length is $maxLength characters!" );
		}
		elseif ( $text == "" ) {
			throw new Exception( " cannot be blank!" );
		}
		
		return $text;
	
	}
	
?>
