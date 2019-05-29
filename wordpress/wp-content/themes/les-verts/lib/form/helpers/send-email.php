<?php

use SUPT\FormType;

/**
 * Send email with with the body rendered from a twig template string
 *
 * @param string|array $to Array or comma-separated list of email addresses to send message.
 * @param string $from Array or comma-separated list of email addresses to send message.
 * @param string $reply_to
 * @param string $subject Email subject (can be a twig template string)
 * @param string $template Twig template string
 * @param array $data Optional. Data for the template.
 * @param int|null $post_meta_id Id of the form submission.
 * @param array $headers Optional. Additional headers.
 * @param string|array $attachment Optional. Files to attach.
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
	$post_meta_id = null,
	$headers = array(),
	$attachment = null
) {

	// Render the email from template
	$data    = supt_prepare_data_for_email( $data, $post_meta_id );
	$body    = Timber::compile_string( $template, $data );
	$subject = Timber::compile_string( $subject, $data );

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

/**
 * Flatten checkbox arrays, add submission url
 *
 * @param $data
 * @param $post_meta_id
 *
 * @return mixed
 */
function supt_prepare_data_for_email( $data, $post_meta_id ) {
	foreach ( $data as $key => &$value ) {
		// flatten arrays
		if ( is_array( $value ) ) {
			$value = join( $value, ', ' );
		}
	}

	if ( $post_meta_id ) {
		$url                    = admin_url( 'edit.php?post_type=' . FormType::MODEL_NAME . '&page=submissions&action=view&item=' . $post_meta_id );
		$data['submission_url'] = "<a href='$url'>" . _x( 'view complete submission online', 'form submission', THEME_DOMAIN ) . "</a>";
	}

	return $data;
}
