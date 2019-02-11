<?php

namespace SUPT;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

require_once 'Get_Post_Meta_With_Id.php';

class Submission_Export {
	use Get_Post_Meta_With_Id;

	const DIRECTION_BOTH = 0;
	const DIRECTION_PREDECESSOR = 1;
	const DIRECTION_DESCENDANT = 2;

	const HEADER_META_KEY = - 1;

	/**
	 * @var int
	 */
	private $base_form_id;

	/**
	 * @var array
	 */
	private $forms;

	/**
	 * The submission data including its predecessors and descendants
	 *
	 * @var array   [$base_submission_id => [$form_id => $form_data]]
	 */
	private $submissions;

	/**
	 * The header data for the export file
	 *
	 * @var array   [$form_id => [$field_slug => $field_label]]
	 */
	private $headers = [];

	/**
	 * The export data
	 *
	 * @var array
	 */
	private $data = [];

	/**
	 * Submission_Export constructor.
	 *
	 * @param int $form_id of the base form to export data from
	 */
	public function __construct( $form_id ) {
		$this->base_form_id = $form_id;
	}

	/**
	 * Export all data
	 */
	public function run() {
		$this->getSubmissions();
		$this->getHeaders();
		$this->getData();

		try {
			$this->writeToExcel();
		} catch ( \PhpOffice\PhpSpreadsheet\Exception $e ) {
			wp_die( $e->getMessage() . "\n" . $e->getTraceAsString(), 'Export failed', true );
		}
	}

	/**
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
	 */
	private function writeToExcel() {
		$spreadsheet = new Spreadsheet();

		// Set document properties
		$site_name = get_bloginfo( 'name' );
		$base_form = reset( $this->forms );
		$spreadsheet->getProperties()->setCreator( $site_name )
		            ->setLastModifiedBy( $site_name )
		            ->setTitle( __( 'Website export', THEME_DOMAIN ) )
		            ->setSubject( $base_form->blog_name );

		// prepare sheet;
		$sheet = $spreadsheet->getActiveSheet()
		                     ->setTitle( __( 'Export', THEME_DOMAIN ) );

		// add the headers
		$column = 1;
		foreach ( $this->headers as $form_id => $header ) {
			$content = $form_id === self::HEADER_META_KEY ? '' : $this->forms[ $form_id ]->post_title;

			// form name
			$merge_first = $sheet->getCellByColumnAndRow( $column, 1 )->getCoordinate();
			$merge_last  = $sheet->getCellByColumnAndRow( $column + count( $header ) - 1, 1 )->getCoordinate();
			$sheet->mergeCells( "$merge_first:$merge_last" );
			$sheet->setCellValueByColumnAndRow( $column, 1, $content );

			// field name
			$coord = $sheet->getCellByColumnAndRow( $column, 2 )->getCoordinate();
			$sheet->fromArray( $header, '', $coord );

			$column += count( $header );
		}

		// add the data
		$sheet->fromArray( $this->data, '', 'A3' );

		$filename = 'export_' . date( 'Ymd' ) . '-' . $base_form->post_name . '.xlsx';

		// Redirect output to a client’s web browser (Xlsx)
		header( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
		header( 'Content-Disposition: attachment;filename="' . $filename . '"' );
		header( 'Cache-Control: max-age=0' );
		// If you're serving to IE 9, then the following may be needed
		header( 'Cache-Control: max-age=1' );

		// If you're serving to IE over SSL, then the following may be needed
		header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' ); // Date in the past
		header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' ); // always modified
		header( 'Cache-Control: cache, must-revalidate' ); // HTTP/1.1
		header( 'Pragma: public' ); // HTTP/1.0

		$writer = IOFactory::createWriter( $spreadsheet, 'Xlsx' );
		$writer->save( 'php://output' );
	}

	private function getData() {
		$headers = $this->headers;

		unset( $headers[ self::HEADER_META_KEY ] );

		// add the field data
		foreach ( $this->submissions as $id => $submission ) {
			$row['id'] = $id;

			foreach ( $headers as $form_id => $fields ) {
				if ( array_key_exists( $form_id, $submission ) && empty( $row['timestamp'] ) ) {
					$row['timestamp'] = $submission[ $form_id ]['_meta_']['timestamp'];
				}

				foreach ( $fields as $slug => $unused ) {
					if ( empty( $submission[ $form_id ][ $slug ] ) ) {
						$field = '';
					} elseif ( is_array( $submission[ $form_id ][ $slug ] ) ) {
						$field = implode( ', ', $submission[ $form_id ][ $slug ] );
					} else {
						$field = $submission[ $form_id ][ $slug ];
					}

					$row[ $form_id . '-' . $slug ] = $field;
				}
			}

			$this->data[] = $row;
		}
	}

	private function getHeaders() {
		$this->headers[ self::HEADER_META_KEY ]['id']        = __( 'ID' );
		$this->headers[ self::HEADER_META_KEY ]['timestamp'] = __( 'Timestamp', THEME_DOMAIN );

		foreach ( $this->forms as $form_id => $form ) {
			// the headers of the current form
			foreach ( get_field( 'form_fields', $form_id ) as $field ) {
				$this->headers[ $form_id ][ $field['slug'] ] = wp_trim_words( $field['form_input_label'], 4, '...' );
			}

			// add the headers of old forms
			foreach ( $this->submissions as $submission ) {
				if ( ! array_key_exists( $form_id, $submission ) ) {
					continue;
				}

				$fields = array_keys( $submission[ $form_id ] );
				foreach ( $fields as $field_slug ) {
					if ( '_meta_' === $field_slug || 'ID' === $field_slug ) {
						continue;
					}

					if ( ! array_key_exists( $field_slug, $this->headers[ $form_id ] ) ) {
						$label     = __( 'old: ' . $field_slug );
						$max_label = strlen( $label ) > 50 ? substr( $label, 0, 50 ) . '...' : $label;

						$this->headers[ $form_id ][ $field_slug ] = $max_label;
					}
				}
			}
		}
	}

	private function getSubmissions() {
		$base_submissions = $this->get_post_meta_with_id( $this->base_form_id, FormType::MODEL_NAME );

		foreach ( $base_submissions as $submission ) {
			$this->processSubmission( $submission );
		}
	}

	private function processSubmission( $submission, $base_id = false, $direction = self::DIRECTION_BOTH ) {
		// add form id
		$form_id                 = $submission['_meta_']['form_id'];
		$this->forms[ $form_id ] = $this->getForm( $form_id );

		// add submission
		$id = $base_id ? $base_id : $submission['ID'];

		if ( empty( $this->submissions[ $id ] ) ) {
			$this->submissions[ $id ] = array();
		}

		if ( array_key_exists( $form_id, $this->submissions[ $id ] ) ) {
			return; // form config creates loop. break here to prevent endless recursion.
		}

		$this->submissions[ $id ][ $form_id ] = $submission;

		$predecessor_id = $submission['_meta_']['predecessor_id'];
		$descendant_id  = $submission['_meta_']['descendant_id'];

		if ( $predecessor_id > 0 && $direction !== self::DIRECTION_DESCENDANT ) {
			$this->getRelatedSubmissions( $submission['ID'], $predecessor_id, self::DIRECTION_PREDECESSOR );
		}

		if ( $descendant_id > 0 && $direction !== self::DIRECTION_PREDECESSOR ) {
			$this->getRelatedSubmissions( $submission['ID'], $descendant_id, self::DIRECTION_DESCENDANT );
		}
	}

	private function getRelatedSubmissions( $base_id, $related_id, $direction ) {
		global $wpdb;

		$data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->postmeta WHERE meta_id = %d", $related_id ) );

		if ( empty( $data ) ) {
			return;
		}

		$submission = $this->unserialize_and_add_id( $data );

		$this->processSubmission( $submission, $base_id, $direction );
	}

	private function getForm( $id ) {
		return get_post( $id );
	}
}
