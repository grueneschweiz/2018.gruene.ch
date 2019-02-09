<?php

namespace SUPT;


trait Get_Post_Meta_With_Id {
	/**
	 * Retrieve metadata with id for the specified object.
	 *
	 * @param int $post_id ID of the object metadata is for
	 * @param string $meta_key Optional. Metadata key. If not specified, retrieve all metadata for
	 *                        the specified object.
	 *
	 * @return array
	 */
	private function get_post_meta_with_id( $post_id, $meta_key = '' ) {
		global $wpdb;
		$results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = %s",
			$post_id, $meta_key ) );

		if ( empty( $results ) ) {
			return [];
		}

		$data = [];
		foreach ( $results as $result ) {
			$id          = $result->meta_id;
			$data[ $id ] = $this->unserialize_and_add_id( $result );
		}

		return $data;
	}

	private function unserialize_and_add_id( $result ) {
		$item       = (array) maybe_unserialize( $result->meta_value );
		$item['ID'] = $result->meta_id;

		return $item;
	}
}
