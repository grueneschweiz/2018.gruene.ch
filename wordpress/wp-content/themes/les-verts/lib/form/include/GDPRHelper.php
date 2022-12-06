<?php


namespace SUPT;


/**
 * Hosts all GDPR specific methods
 *
 * @package model
 */
class GDPRHelper {
	/**
	 * Return array containing the personal, from the person with the given email address, data stored by this plugin.
	 *
	 * @param string $email
	 *
	 * @return array
	 *
	 * @see https://developer.wordpress.org/plugins/privacy/adding-the-personal-data-exporter-to-your-plugin/
	 */
	public static function exporter( $email ) {
		$export_items = array();

		require_once __DIR__ . '/FormModel.php';
		$forms = FormModel::get_forms();

		foreach ( $forms as $form ) {
			$submissions = $form->get_submissions();

			foreach ( $submissions as $submission ) {
				if ( $email === $submission->meta_get_email() ) {
					$data = $submission->as_array_with_linked_data();

					$export_items[] = array(
						'group_id'    => FormType::MODEL_NAME,
						'group_label' => __( 'Forms', THEME_DOMAIN ),
						'item_id'     => $form->get_id(),
						'data'        => self::map_for_export( $data ),
					);
				}
			}
		}

		return array(
			'data' => $export_items,
			'done' => true,
		);
	}

	/**
	 * Map key value paired array data into two dimensional array with name and value keys
	 *
	 * @param array $array with the field names as key and the field data as value
	 *
	 * @return array
	 */
	private static function map_for_export( $array ) {
		$mapped = array();
		$labels = array_keys( $array );
		$values = array_values( $array );

		foreach ( $labels as $key => $label ) {
			$value = $values[ $key ];

			if ( is_array( $value ) ) {
				$value = array_map(
					static function ( $v ) {
						return (string) $v;
					}, $value );
				$value = implode( "; ", $value );
			}

			if ( ! is_string( $value ) ) {
				$value = (string) $value;
			}

			$mapped[] = array(
				'name'  => $label,
				'value' => $value,
			);
		}

		return $mapped;
	}

	/**
	 * Delete personal data, from the person with the given email address, stored by this plugin.
	 *
	 * @param string $email
	 *
	 * @return array
	 *
	 * @see https://developer.wordpress.org/plugins/privacy/adding-the-personal-data-eraser-to-your-plugin/
	 */
	public static function eraser( $email ) {
		require_once __DIR__ . '/FormModel.php';
		$forms = FormModel::get_forms();

		$removed = false;

		foreach ( $forms as $form ) {
			$submissions = $form->get_submissions();

			foreach ( $submissions as $submission ) {
				if ( $email === $submission->meta_get_email() ) {
					$removed = $submission->delete_including_linked_submissions();
				}
			}
		}

		return [
			'items_removed'  => $removed,
			'items_retained' => false,
			'messages'       => array(),
			'done'           => true,
		];
	}
}
