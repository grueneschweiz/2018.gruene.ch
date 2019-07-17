<?php

namespace SUPT;

add_action( 'import_end', array( '\SUPT\Importer', 'import' ) );

class Importer {
	private $post;

	/**
	 * The posts we have to review and the reasons
	 *
	 * @var array => [ # => ['type' => $shortcode_tag, 'shortcode' => $full_shortcode, 'post_id' => $post_id]]
	 */
	private $review = array();

	public static function import() {
		$instance = new Importer();
		$instance->update_posts_and_pages();
		$instance->update_events();
		$instance->notify();
	}

	private function __construct() {
	}

	private function update_events() {
		$query = new \WP_Query( array(
			'post_type'   => array( 'tribe_events' ),
			'post_status' => 'any',
			'nopaging'    => true
		) );

		while ( $query->have_posts() ) {
			$query->the_post();
			$post       = \get_post();
			$this->post = $post;

			/**
			 * move body text
			 */
			if ( empty( get_field( 'description' ) ) ) {
				$post_content = $this->process_shortcodes( $post->post_content );
				update_field( 'description', $post_content );
				$post->post_content = '';
			}

			/**
			 * copy featured image
			 */
			$image_id = (int) \get_post_thumbnail_id();
			if ( $image_id ) {
				update_field( 'image', $image_id );
			}

			// save the changes on the original post
			\wp_update_post( $post );
		}

		\wp_reset_postdata();
	}

	private function update_posts_and_pages() {
		$query = new \WP_Query( array(
			'post_type'   => array( 'page', 'post' ),
			'post_status' => 'any',
			'nopaging'    => true
		) );

		while ( $query->have_posts() ) {
			$query->the_post();
			$post       = \get_post();
			$this->post = $post;
			$content    = \get_field( 'main_content', false );

			/**
			 * move body text
			 */
			if ( empty( $content['content'] ) ) {
				$content['content'] = array();
			}

			if ( trim( $post->post_content ) ) {
				$post_content = $this->process_shortcodes( $post->post_content );

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
				\update_field( 'teaser', $post->post_excerpt );
				$post->post_excerpt = '';
			}

			/**
			 * copy featured image
			 */
			$image_id = (int) \get_post_thumbnail_id();
			if ( $image_id ) {
				$content['header_image'] = $image_id;
			}

			// save the new meta fields
			\update_field( 'main_content', $content );

			// save the changes on the original post
			\wp_update_post( $post );
		}

		\wp_reset_postdata();
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

	private function note_for_review( $tag, $shortcode ) {
		$post_id        = $this->post->ID;
		$this->review[] = array( 'type' => $tag, 'shortcode' => $shortcode, 'post_id' => $post_id );
	}

	private static function get_shortcode_attr( $attr, $shortcode ) {
		$pattern  = $attr . '\s*=\s*([\'"]|&#8221;|&#8217;)(.*)\1';
		$has_attr = preg_match( "/$pattern/U", $shortcode, $match );

		if ( $has_attr && array_key_exists( 2, $match ) ) {
			return $match[2];
		}

		return false;
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
		$class   = 'notice notice-warning';
		$message = sprintf(
			__( "<strong>WARNING:</strong> There were some shortcodes we couldn't process automatically. %sDownload the list%s of posts to review manually.", 'THEME_DOMAIN' ),
			"<a href='$file_url' target='_blank'>",
			'</a>'
		);

		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
	}
}
