<?php
/**
 * Send email with with the body rendered from a twig template string
 *
 * @param  string|array $to Array or comma-separated list of email addresses to send message.
 * @param  string $from Array or comma-separated list of email addresses to send message.
 * @param  string $subject Email subject
 * @param  string $template Twig template string
 * @param  array $data Optional. Data for the template.
 * @param  array $headers Optional. Additional headers.
 * @param  string|array $attachments Optional. Files to attach.
 *
 * @return bool Whether the email contents were sent successfully.
 */
function supt_form_send_email(
	$to,
	$from,
	$reply_to,
	$subject,
	$template,
	$data = array(),
	$headers = array(),
	$attachment = null
) {
	
	// Render the email from template
	$data = supt_prepare_data_for_email( $data );
	$body = Timber::compile_string( $template, $data );
	
	$headers = array_merge(
		array(
			"From: $from",
			"Reply-To: $reply_to",
			'Content-Type: text/html; charset=UTF-8'
		),
		$headers
	);
	
	$mail_sent = wp_mail( $to, $subject, $body, $headers, $attachment );
	
	return $mail_sent;
}

function supt_prepare_data_for_email( $data ) {
	
	foreach ( $data as $key => &$value ) {
		
		// flatten arrays
		if ( is_array( $value ) ) {
			$value = join( $value, ', ' );
		}
		
	}
	
	return $data;
}
