<?php /** @noinspection AutoloadingIssuesInspection */

namespace SUPT\Migrations\EventContent;

use WP_Query;

/**
 * Migrate event body text from the ACF description field to the much more versatile event_content
 */
add_action( 'wp', [ new Migrator(), 'migrate_all' ] );

class Migrator {

	private $old_content;
	private $new_content;

	public function migrate_all(): void {
		$query = new WP_Query( array(
			'post_type'                    => 'tribe_events',
			'post_status'                  => 'any',
			'posts_per_page'               => - 1,
			'nopaging'                     => true,
			'tribe_suppress_query_filters' => true,
			'lang'                         => '', // deactivates the Polylang filter
		) );

		while ( $query->have_posts() ) {
			$query->the_post();
			$this->migrate_current();
		}

		wp_reset_postdata();
	}

	private function migrate_current(): void {
		$old_content = $this->get_old_content();

		if ( ! $old_content ) {
			return;
		}

		$new_content = $this->get_new_content();

		if ( ! $this->is_already_migrated() ) {
			$new_content['content'][ count( $new_content ) ] = array(
				'acf_fc_layout' => 'text',
				'text'          => $old_content
			);

			update_field( 'event_content', $new_content );
		}

		$this->delete_old_content();
		unset( $this->old_content, $this->new_content );
	}

	/**
	 * @return false|string
	 */
	private function get_old_content() {
		if ( ! isset( $this->old_content ) ) {
			$old_content = get_field( 'description' );

			if ( empty( $old_content ) || ! is_string( $old_content ) ) {
				$this->old_content = false;
			} else {
				$old_content       = trim( $old_content );
				$this->old_content = empty( $old_content ) ? false : $old_content;
			}
		}

		return $this->old_content;
	}

	private function get_new_content() {
		if ( ! isset( $this->new_content ) ) {
			$new_content = get_field( 'event_content' );

			if ( empty( $new_content['content'] ) ) {
				$new_content['content'] = array();
			}

			$this->new_content = $new_content;
		}

		return $this->new_content;
	}

	private function is_already_migrated(): bool {
		$new_content = $this->get_new_content();

		return count( $new_content['content'] ) > 0;
	}

	private function delete_old_content(): void {
		if ( ! delete_field( 'description' ) ) {
			$id    = get_post()->ID;
			$field = acf_maybe_get_field( 'description', $id, false );
			acf_delete_value( $id, $field );
		}
	}
}
