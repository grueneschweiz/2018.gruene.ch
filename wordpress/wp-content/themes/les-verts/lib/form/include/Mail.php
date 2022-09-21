<?php


namespace SUPT;


use Timber\Timber;
use Twig\Error\SyntaxError;

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
	 * Counter for the sending attempts
	 *
	 * @var int
	 */
	private $sending_attempts = 0;

	/**
	 * Send email with with the body rendered from a twig template string
	 *
	 * @param string|array $to Array or comma-separated list of email addresses to send message.
	 * @param string $from Array or comma-separated list of email addresses to send message.
	 * @param string|false $reply_to
	 * @param string $subject Email subject (can be a twig template string)
	 * @param string $template Twig template string
	 * @param array $data Optional. Data for the template.
	 * @param int|null $post_meta_id Id of the form submission.
	 * @param string $referer_url The url of the page, where the form was submitted.
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
		$referer_url = '',
		$headers = array(),
		$attachment = null
	) {

		// Render the email from template
		$data     = $this->prepare_data_for_email( $data, $post_meta_id, $referer_url );
		$template = $this->sanitize_twig_tags( $template );
		$body     = $this->compileBody( $template, $data );
		$subject  = $this->compileSubject( $subject, $data );

		// Assert nice quotes (call after compiling!)
		$body    = wptexturize( $body );
		$subject = wptexturize( $subject );

		$default_headers = array(
			"From: $from",
			'Content-Type: text/html; charset=UTF-8'
		);

		if ( $reply_to ) {
			$default_headers[] = "Reply-To: $reply_to";
		}

		$headers = array_merge(
			$default_headers,
			$headers
		);

		$this->to         = $to;
		$this->subject    = $subject;
		$this->body       = $body;
		$this->headers    = $headers;
		$this->attachment = $attachment;
	}

	/**
	 * Flatten checkbox arrays, add submission and request urls
	 *
	 * @param array $data
	 * @param int|null $post_meta_id
	 * @param string $referer_url
	 *
	 * @return array
	 */
	private function prepare_data_for_email( $data, $post_meta_id, $referer_url ): array {
		foreach ( $data as $key => $value ) {
			// flatten arrays
			if ( is_array( $value ) ) {
				$data[ $key ] = implode( ', ', $value );
			}
		}

		if ( $post_meta_id ) {
			$submission_url         = admin_url( 'edit.php?post_type=' . FormType::MODEL_NAME . '&page=submissions&action=view&item=' . $post_meta_id );
			$data['submission_url'] = "<a href='$submission_url'>" . _x( 'view complete submission online', 'form submission', THEME_DOMAIN ) . "</a>";
		}

		$data['referer_url'] = $referer_url;

		return $data;
	}

	/**
	 * Remove any html tags from inside twig expressions {{<removed>not removed<removed>}}.
	 *
	 * @param string $template
	 *
	 * @return string
	 */
	private function sanitize_twig_tags( string $template ): string {
		$tag_pattern = '/({{.*}})/';
		$parts       = preg_split( $tag_pattern, $template, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE );
		$return      = '';
		foreach ( $parts as $part ) {
			if ( preg_match( $tag_pattern, $part ) ) {
				$part = strip_tags( $part );
			}
			$return .= $part;
		}

		return $return;
	}

	private function compileBody( string $template, array $data ): string {
		try {
			$body = Timber::compile_string( $template, $data );
			if ( false === $body ) {
				return __( "ERROR: Failed to compile email template.", THEME_DOMAIN )
				       . "\n\n$template";
			}

			return $body;
		} /** @noinspection PhpRedundantCatchClauseInspection */
		catch ( SyntaxError $e ) {
			$error_msg = sprintf(
				__( 'ERROR: There is a syntax error in the email template on line %d.', THEME_DOMAIN ),
				$e->getTemplateLine()
			);
			$error_msg .= "\n";
			$error_msg .= __( "Often it's just a missing curly bracket. Make sure all your placeholders start with {{ and end with }}.", THEME_DOMAIN );
			$error_msg .= "\n";
			$error_msg .= __( "The placeholders can't be replaced due to this error.", THEME_DOMAIN );

			$template_lines        = explode( "\n", $template );
			$hinted_template_lines = [];
			foreach ( $template_lines as $idx => $line ) {
				$line_number                   = $idx + 1;
				$indicator                     = $e->getTemplateLine() === $line_number ? '-> ' : '   ';
				$hinted_template_lines[ $idx ] = "$indicator$line_number    $line";
			}

			return "$error_msg\n\n---" . __( 'Email template' ) . "---\n\n" . implode( "\n", $hinted_template_lines );
		}
	}

	private function compileSubject( string $template, array $data ): string {
		try {
			$subject = Timber::compile_string( $template, $data );
		} /** @noinspection PhpRedundantCatchClauseInspection */
		catch ( SyntaxError $e ) {
			$subject = $template;
		}

		if ( false === $subject ) {
			$subject = $template;
		}

		return html_entity_decode( $subject, ENT_QUOTES | ENT_HTML5 );
	}

	/**
	 * Send out mail
	 *
	 * @return bool
	 */
	public function send() {
		$this->sending_attempts ++;

		return wp_mail(
			$this->to,
			$this->subject,
			$this->body,
			$this->headers,
			$this->attachment
		);
	}

	/**
	 * @return int
	 */
	public function get_sending_attempts(): int {
		return $this->sending_attempts;
	}

	/**
	 * @return array|string
	 */
	public function get_to() {
		return $this->to;
	}

	public function get_subject(): string {
		return $this->subject;
	}

	public function get_headers(): array {
		return $this->headers;
	}

	public function get_body(): string {
		return $this->body;
	}
}
