<?php /** @noinspection AutoloadingIssuesInspection */

namespace SUPT;

use SUPT\Migrations\EventContent\Migrator;
use WP_Query;
use function get_field;
use function get_post;
use function get_post_thumbnail_id;
use function update_field;
use function wp_reset_postdata;
use function wp_update_post;

/**
 * Run immediately if called by the cli, else hook into the import_end action.
 *
 * The import_end hook is not called, if the import is done by cli. So we have
 * to call this class manually.
 */
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	Importer::import();
} else {
	add_action( 'import_end', array( '\SUPT\Importer', 'import' ) );
}

class Importer {
	private static $instance;
	private $post;

	/**
	 * The posts we have to review and the reasons
	 *
	 * @var array => [ # => ['type' => $shortcode_tag, 'shortcode' => $full_shortcode, 'post_id' => $post_id]]
	 */
	private $review = array();

	private function __construct() {
	}

	public static function import() {
		$instance = self::get_instance();
		$instance->update_posts_and_pages();
		$instance->update_events();
		$instance->notify();
	}

	private static function get_instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new Importer();
		}

		return self::$instance;
	}

	private function update_posts_and_pages() {
		$query = new WP_Query( array(
			'post_type'   => array( 'page', 'post' ),
			'post_status' => 'any',
			'nopaging'    => true
		) );

		$count = 0;

		while ( $query->have_posts() ) {
			$count ++;

			$query->the_post();
			$post       = get_post();
			$this->post = $post;

			self::cli_echo( "Processing post #$post->ID (\"$post->post_title\") (post_type: $post->post_type)" );
			self::cli_echo( "-- $count of {$query->post_count}" );

			$content = get_field( 'main_content', false );

			/**
			 * move body text
			 */
			if ( empty( $content['content'] ) ) {
				$content['content'] = array();
			}

			if ( trim( $post->post_content ) ) {
				$post_content = $this->process_shortcodes( $post->post_content );
				$post_content = $this->strip_block_editor_tags( $post_content );

				$idx                        = empty( $content['content'] ) ? 0 : count( $content );
				$content['content'][ $idx ] = array(
					'acf_fc_layout' => 'text',
					'text'          => $post_content
				);

				$post->post_content = '';
			}

			/**
			 * move excerpt
			 */
			if ( ! empty( $post->post_excerpt ) ) {
				update_field( 'teaser', $post->post_excerpt );
				$post->post_excerpt = '';
			}

			/**
			 * copy featured image
			 */
			$image_id = (int) get_post_thumbnail_id();
			if ( $image_id ) {
				$content['header_image'] = $image_id;
			}

			// save the new meta fields
			update_field( 'main_content', $content );

			// save the changes on the original post
			wp_update_post( $post );

			self::cli_echo( "-- Done.\n" );
		}

		wp_reset_postdata();
	}

	private static function cli_echo( string $message, string $method = 'log' ): void {
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			/** @noinspection PhpUndefinedClassInspection */
			WP_CLI::$method( $message );
		}
	}

	private function process_shortcodes( $content ) {
		$pattern        = get_shortcode_regex( array(
			'contact-form',
			'contact-field',
			'hide_n_show',
			'button',
			'progressbar',
			'donation_form',
			'politch'
		) );
		$has_shortcodes = preg_match_all( '$' . $pattern . '$s', $content, $matches );

		// bail early if we don't have any shortcodes to process
		if ( ! ( $has_shortcodes && array_key_exists( 2, $matches ) ) ) {
			return $content;
		}

		foreach ( $matches[2] as $idx => $tag ) {
			$full_shortcode = $matches[0][ $idx ];
			$attr_str       = $matches[3][ $idx ];
			$body           = $matches[5][ $idx ];

			switch ( $tag ) {
				case 'hide_n_show':
					$title = self::get_shortcode_attr( 'display', $attr_str );
					if ( $title ) {
						$replacement = "<h2>{$title}</h2>\n{$body}";
						$content     = str_replace( $full_shortcode, $replacement, $content );
					}
					break;

				case 'button':
					$color = self::get_shortcode_attr( 'color', $attr_str );
					$link  = self::get_shortcode_attr( 'link', $attr_str );

					if ( $link ) {
						$color_class = 'magenta' === $color ? 'secondary' : 'primary';
						$replacement = "<a href=\"{$link}\" class=\"a-button a-button--$color_class\">{$body}</a>";
						$content     = str_replace( $full_shortcode, $replacement, $content );
					}
					break;

				case 'donation_form':
					// do nothing, it still works
					break;

				case 'politch':
				case 'progressbar':
				case 'contact-form':
				case 'contact-field':
				default:
					$content = str_replace( $full_shortcode, '', $content );
					$this->note_for_review( $tag, $full_shortcode );

			}
		}

		return $content;
	}

	private static function get_shortcode_attr( $attr, $shortcode ) {
		$pattern  = $attr . '\s*=\s*([\'"]|&#8221;|&#8217;)(.*)\1';
		$has_attr = preg_match( "/$pattern/U", $shortcode, $match );

		if ( $has_attr && array_key_exists( 2, $match ) ) {
			return $match[2];
		}

		return false;
	}

	private function note_for_review( $tag, $shortcode ) {
		$post_id        = $this->post->ID;
		$this->review[] = array( 'type' => $tag, 'shortcode' => $shortcode, 'post_id' => $post_id );
	}

	private function strip_block_editor_tags( $content ) {
		// strip block editor's paragraph tags
		$p_regex = "/<!-- wp:paragraph -->\s*<p>\s*(.*?)\s*<\/p>\s*<!-- \/wp:paragraph -->/i";
		$content = preg_replace( $p_regex, '$1', $content );

		// remove any other tags except for the good old more tag
		$comment_regex = "/<!--(?!\s*more\s*).*?-->/";
		$content       = preg_replace( $comment_regex, '', $content );

		// remove multiple consecutive newline chars
		$newlines_regex = "/\n+/";
		$content        = preg_replace( $newlines_regex, "\n", $content );

		return $content;
	}

	private function update_events() {
		$query = new WP_Query( array(
			'post_type'   => array( 'tribe_events' ),
			'post_status' => 'any',
			'nopaging'    => true
		) );

		$count = 0;

		while ( $query->have_posts() ) {
			$count ++;

			$query->the_post();
			$post       = get_post();
			$this->post = $post;

			self::cli_echo( "Processing post #$post->ID (\"$post->post_title\") (post_type: $post->post_type)" );
			self::cli_echo( "-- $count of {$query->post_count}" );

			/**
			 * move body text
			 */
			$content = get_field( 'event_content', false );

			if ( empty( $content['content'] ) ) {
				$content['content'] = array();
			}

			if ( trim( $post->post_content ) ) {
				$post_content = $this->process_shortcodes( $post->post_content );
				$post_content = $this->strip_block_editor_tags( $post_content );

				$idx                        = empty( $content['content'] ) ? 0 : count( $content );
				$content['content'][ $idx ] = array(
					'acf_fc_layout' => 'text',
					'text'          => $post_content
				);

				$post->post_content = '';

				update_field( 'event_content', $content );
			}

			/**
			 * copy featured image
			 */
			$image_id = (int) get_post_thumbnail_id();
			if ( $image_id ) {
				update_field( 'image', $image_id );
			}

			// save the changes on the original post
			wp_update_post( $post );

			self::cli_echo( "-- Done.\n" );
		}

		wp_reset_postdata();

		/**
		 * migrate from old acf event format to the new one
		 */
		require_once dirname( __DIR__ ) . '/migrations/event-content.php';
		( new Migrator() )->migrate_all();
	}

	private function notify() {
		// bail early if everything went well
		if ( empty( $this->review ) ) {
			return;
		}

		// write log file
		$filename  = 'import_' . date( 'Y-m-d_H-i-s' ) . '.tsv';
		$file_path = trailingslashit( WP_CONTENT_DIR ) . $filename;
		$file_url  = trailingslashit( WP_CONTENT_URL ) . $filename;

		$data = "Shortcode type\tFull shortcode\tPost title\tEdit link\n";
		foreach ( $this->review as $issue ) {
			$edit_link = get_edit_post_link( $issue['post_id'], '' );
			$post      = get_post( $issue['post_id'] );
			$line      = "{$issue['type']}\t{$issue['shortcode']}\t{$post->post_title}\t{$edit_link}";
			$data      .= $line . "\n";
		}

		file_put_contents( $file_path, $data, LOCK_EX );

		// inform user about log file
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			self::cli_echo( "There were some shortcodes we couldn't process automatically.", 'warning' );
			self::cli_echo( "Have a look at the list of posts to review manually: $file_path" );
		} else {
			$class   = 'notice notice-warning';
			$message = sprintf(
				__( "<strong>WARNING:</strong> There were some shortcodes we couldn't process automatically. %sDownload the list%s of posts to review manually.", 'THEME_DOMAIN' ),
				"<a href='$file_url' target='_blank'>",
				'</a>'
			);

			printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
		}
	}
}
