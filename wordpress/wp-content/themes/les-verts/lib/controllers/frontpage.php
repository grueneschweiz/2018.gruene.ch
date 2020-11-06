<?php


namespace SUPT;

use Timber\Timber;
use WP_Query;
use function get_posts;

class Frontpage_controller {
	public static function register() {
		add_filter( 'timber_context', array( __CLASS__, 'add_to_context' ) );
	}

	public static function add_to_context( $context ) {
		if ( is_front_page() || 'single_front.php' === get_page_template_slug() ) {
			$context['post']                 = new ACFPost();
			$context['latest_press_release'] = self::get_latest_press_release( $context );
			$context['latest_posts']         = self::get_latest_posts( $context );
			$context['events']               = self::get_events( $context );
			$context['no_sanuk']             = ! file_exists( WP_CONTENT_DIR . '/sanuk/font.ttf' );
		}

		return $context;
	}

	/**
	 * Adds a post 'latest_press_release' to the context. Uses the category id from
	 * the media block (front page only) as well as the exclude categories.
	 *
	 * @param $context
	 */
	private static function get_latest_press_release( &$context ) {
		if ( empty( $context['post']->custom['content_blocks'] ) ) {
			return;
		}

		foreach ( $context['post']->custom['content_blocks'] as $id => $type ) {
			if ( 'media' == $type ) {
				// get post category
				$id     = (int) $id;
				$cat_id = $context['post']->custom["content_blocks_{$id}_category"];

				// get latest post query
				$args = array(
					'posts_per_page' => 1,
					'cat'            => $cat_id,
					'post_status'    => 'publish', // prevent 'private' if logged in
					'meta_query'     => array(
						array(
							'key'   => 'settings_show_on_front_page',
							'value' => '1',
						)
					),
				);

				// get the latest post
				$press_post = Timber::get_posts( $args );

				if ( $press_post ) {
					return $press_post[0];
				}

				// the latest post is limited to one, so we're done now
				return;
			}
		}
	}

	/**
	 * Get the content of the post / page block if set to automatic post selection.
	 *
	 * Asserts, that no post is twice on the front page (unless added twice manually).
	 *
	 * @param $context
	 *
	 * @return array the latest posts
	 */
	private static function get_latest_posts( $context ) {
		if ( empty( $context['post']->custom['content_blocks'] ) ) {
			return array();
		}

		$configs = [];
		foreach ( $context['post']->custom['content_blocks'] as $id => $type ) {
			if ( $type !== 'single' && $type !== 'double' ) {
				continue;
			}

			$configs = array_merge( $configs, self::get_post_config_selectors( $id, $type ) );
		}

		$loaded_posts = [];
		if ( $context['latest_press_release'] ) {
			$loaded_posts[] = $context['latest_press_release']->id;
		}

		$latest_posts = [];
		foreach ( $configs as $config ) {
			$data = $context['post'];

			if ( 'manual' === $data->{$config['selection']} ) {
				$loaded_posts[] = (int) $data->{$config['post']};
			}
		}

		foreach ( $configs as $config ) {
			if ( 'latest' === $data->{$config['selection']} ) {
				$cat_id   = $data->{$config['category']};
				$post_ids = get_posts(
					array(
						'numberposts'  => 1,
						'category'     => $cat_id,
						'exclude'      => $loaded_posts,
						'fields'       => 'ids',
						'has_password' => false,
						'post_status'  => 'publish',
						'orderby'      => 'date',
						'order'        => 'DESC',
					)
				);

				if ( $post_ids ) {
					$loaded_posts = array_merge(
						$loaded_posts,
						$post_ids
					);

					$latest_posts[ $config['id'][0] ][ $config['id'][1] ] = $post_ids[0];
				}
			}
		}

		return $latest_posts;
	}

	private static function get_post_config_selectors( $id, $type ) {
		if ( 'single' === $type ) {
			return array(
				array(
					'selection' => "content_blocks_{$id}_post_selection",
					'post'      => "content_blocks_{$id}_post",
					'category'  => "content_blocks_{$id}_category",
					'id'        => array( $id, 0 ),
				)
			);
		} else {
			return array(
				array(
					'selection' => "content_blocks_{$id}_post_1_post_selection",
					'post'      => "content_blocks_{$id}_post_1_post",
					'category'  => "content_blocks_{$id}_post_1_category",
					'id'        => array( $id, 0 ),
				),
				array(
					'selection' => "content_blocks_{$id}_post_2_post_selection",
					'post'      => "content_blocks_{$id}_post_2_post",
					'category'  => "content_blocks_{$id}_post_2_category",
					'id'        => array( $id, 1 ),
				),
			);
		}
	}

	/**
	 * Adds the event posts to the context (only if the events block is used)
	 *
	 * @param $context
	 *
	 * @return WP_Query
	 */
	private static function get_events( &$context ) {
		$context['venues'] = null;

		if ( empty( $context['post']->custom['content_blocks'] ) ) {
			return;
		}

		foreach ( $context['post']->custom['content_blocks'] as $id => $type ) {
			if ( 'events' == $type ) {

				$post_per_page = (int) $context['post']->{"content_blocks_{$id}_max_num"};

				// get upcoming and running events
				$args = array(
					'post_type'      => 'tribe_events',
					'posts_per_page' => $post_per_page,
					'post_status'    => 'publish', // prevent 'private' if logged in
					'orderby'        => 'meta_value_datetime',
					'meta_query'     => array(
						array(
							'key'     => '_EventEndDate',
							'compare' => '>',
							'value'   => date( 'Y-m-d H:i:s' ),
							'type'    => 'DATETIME'
						)
					),
				);

				$events = Timber::get_posts( $args );

				// the latest post is limited to one, so we're done now
				return $events;
			}
		}
	}
}


