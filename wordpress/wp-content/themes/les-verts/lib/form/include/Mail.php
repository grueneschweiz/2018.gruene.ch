<?php


namespace SUPT;


use Timber\Timber;

class Mail {
	/**
	 * Recipient email addresses as accepted by wp_mail()
	 *
	 * @var string|array
	 */
	private $to;

	/**
	 * Mail subject
	 *
	 * @var string
	 */
	private $subject;

	/**
	 * Mail body
	 *
	 * @var string
	 */
	private $body;

	/**
	 * Headers as accepted by wp_mail()
	 *
	 * @var array
	 */
	private $headers;

	/**
	 * Files to attach as accepted by wp_mail()
	 *
	 * @var null|string|array
	 */
	private $attachment;

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
	 */
	public function __construct(
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
		$data    = $this->prepare_data_for_email( $data, $post_meta_id );
		$body    = Timber::compile_string( $template, $data );
		$subject = Timber::compile_string( $subject, $data );

		// Assert nice quotes (call after compiling!)
		$body    = wptexturize( $body );
		$subject = wptexturize( $subject );

		$headers = array_merge(
			array(
				"From: $from",
				"Reply-To: $reply_to",
				'Content-Type: text/html; charset=UTF-8'
			),
			$headers
		);

		$this->to         = $to;
		$this->subject    = $subject;
		$this->body       = $body;
		$this->headers    = $headers;
		$this->attachment = $attachment;
	}

	/**
	 * Send out mail
	 *
	 * @return bool
	 */
	public function send() {
		return wp_mail(
			$this->to,
			$this->subject,
			$this->body,
			$this->headers,
			$this->attachment
		);
	}

	/**
	 * Flatten checkbox arrays, add submission url
	 *
	 * @param array $data
	 * @param int|null $post_meta_id
	 *
	 * @return array
	 */
	private function prepare_data_for_email( $data, $post_meta_id ) {
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

}
