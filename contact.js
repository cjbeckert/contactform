$( function() {

	// Hide any sent/failed messages if text field is selected
	$( "#name" ).focus( function() {
		$( "#responseMessage" ).fadeOut();
		$( "#nameError" ).fadeOut();
	} );

	$( "#email" ).focus( function() {
		$( "#responseMessage" ).fadeOut();
		$( "#emailError" ).fadeOut();
	} );

	$( "#message" ).focus( function() {
		$( "#responseMessage" ).fadeOut();
		$( "#messageError" ).fadeOut();
	} );

	// Handle form submit
	$( "#contact_form" ).submit( function( event ) {

		// Block default form action, we want AJAX to handle things
		event.preventDefault();

		// Basic user input error checking
		// Input is also checked (and sanitized) on the server-side PHP
		var errors = 0;

		// Ensure that the name field is not empty
		if ( !$.empty_field_validation( $( "#name" ).val() ) ) {
			errors = 1;
			$( "#nameError" ).html( "<p>Name cannot be blank!</p>" );
			$( "#nameError" ).css( "display","block" );
		}

		// Ensure email address is of a proper format
		if ( !$.email_validation( $( "#email" ).val() ) ) {
			errors = 1;
			$( "#emailError" ).html( "<p>Invalid email address format!</p>" );
			$( "#emailError" ).css( "display","block" );
		}

		// Ensure that the message field is not empty
		if ( !$.empty_field_validation( $( "#message" ).val() ) ) {
			errors = 1;
			$( "#messageError" ).html( "<p>Message cannot be blank!</p>" );
			$( "#messageError" ).css( "display","block" );
		}

		// Input looks ok - let's proceed
		if ( !errors ) {

			// Disable submit button to prevent spamming
			$( "#submit_button" ).attr( { "disabled" : "true", "value" : "Sending..." } );

			// Serialize the form data
			var formData = $( "#contact_form" ).serialize();

			// Submit the form via AJAX
			$.ajax( {
				type: "POST",
				url: $( "#contact_form" ).attr( "action" ),
				data: formData,
				dataType: "json",
				success: function( response ) {

					// Make sure the server didn't catch any bad stuff
					var errors = false;
					if ( response.nameError ) {
						errors = true;
						$( "#nameError" ).html( "<p>" + response.nameError + "</p>" );
						$( "#nameError" ).css( "display","block" );
					}

					if ( response.emailError ) {
						errors = true;
						$( "#emailError" ).html( "<p>" + response.emailError + "</p>" );
						$( "#emailError" ).css( "display","block" );
					}

					if ( response.messageError ) {
						errors = true;
						$( "#messageError" ).html( "<p>" + response.messageError + "</p>" );
						$( "#messageError" ).css( "display","block" );
					}

					// If no errors display success message
					if ( !errors ) {
						$( "#responseMessage" ).removeClass( "error" );
						$( "#responseMessage" ).addClass( "success" );
						$( "#responseMessage" ).html( "<p>Your message was sent successfully!</p>" );
                	                        $( "#responseMessage" ).css( "display","block" );
						$.reset_form();
					}
				},
				error: function() {
					$( "#responseMessage" ).removeClass( "success" );
					$( "#responseMessage" ).addClass( "error" );
					$( "#responseMessage" ).html( "<p>An unexpected error occurred. Please try again later.</p>" );
					$( "#responseMessage" ).css( "display","block" );
					$.reset_form();
				}
			} );
		}
	} );

});

$.reset_form = function() {
	$( "#name" ).val( "" );
	$( "#email" ).val( "" );
	$( "#message" ).val( "" );
	$( "#submit_button" ).attr( { "value" : "Send" } );
	$( "#submit_button" ).removeAttr( "disabled" );
}

$.empty_field_validation = function( field_value ) {
	if ( field_value.trim() == '' ) return false;
	return true;
}

$.email_validation = function( email ) {
	// No need to check length > 254 here. Field maxlength "should" prevent that. If user skirts it somehow, server
	// will catch and throw error
	var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9])+$/;
	return regex.test( email );
}
