<?php

namespace SUPT;

use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class SubmissionExport {
	const DIRECTION_BOTH = 0;
	const DIRECTION_PREDECESSOR = 1;
	const DIRECTION_DESCENDANT = 2;

	const HEADER_META_KEY = - 1;

	/**
	 * @var FormModel
	 */
	private $base_form;

	/**
	 * @var FormModel[]
	 */
	private $forms;

	/**
	 * The submission data including its predecessors and descendants
	 *
	 * @var SubmissionModel[][]  [$base_submission_id => [$form_id => $form_data]]
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
		include_once __DIR__ . '/FormModel.php';

		try {
			$this->base_form = new FormModel( $form_id );
		} catch ( Exception $e ) {
			wp_die( $e->getMessage() );
		}
	}

	/**
	 * Export all data
	 */
	public function run() {
		$this->getSubmissions();
		$this->getHeaders();
		$this->getData();

		// update submission table to use model
		// create view and edit

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
			->setSubject( $base_form->get_title() );

		// prepare sheet;
		$sheet = $spreadsheet->getActiveSheet()
			->setTitle( __( 'Export', THEME_DOMAIN ) );

		// add the headers
		$column = 1;
		foreach ( $this->headers as $form_id => $header ) {
			$content = $form_id === self::HEADER_META_KEY ? '' : $this->forms[ $form_id ]->get_title();

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

		$filename = 'export_' . date( 'Ymd' ) . '-' . sanitize_title( $base_form->get_title() ) . '.xlsx';

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
				if ( ! array_key_exists( $form_id, $submission ) ) {
					continue;
				}

				$row['timestamp'] = $submission[ $form_id ]->meta_get_timestamp();
				$row['referer']   = $submission[ $form_id ]->meta_get_referer();

				foreach ( $fields as $slug => $unused ) {
					$field = $submission[ $form_id ]->{'get_' . $slug}();

					if ( empty( $field ) ) {
						$field = '';
					} elseif ( is_array( $field ) ) {
						$field = implode( ', ', $field );
					}

					$row[ $form_id . '-' . $slug ] = $field;
				}
			}

			$this->data[] = $row;
			unset( $row );
		}
	}

	private function getHeaders() {
		$this->headers[ self::HEADER_META_KEY ]['id']        = __( 'ID' );
		$this->headers[ self::HEADER_META_KEY ]['timestamp'] = __( 'Timestamp', THEME_DOMAIN );
		$this->headers[ self::HEADER_META_KEY ]['referer']   = __( 'Referer', THEME_DOMAIN );

		foreach ( $this->forms as $form_id => $form ) {
			// the headers of the current form
			foreach ( $form->get_columns() as $slug => $text ) {
				$this->headers[ $form_id ][ $slug ] = wp_trim_words( strip_tags( $text ), 4, '...' );
			}

			// add the headers of old forms
			foreach ( $this->submissions as $submission ) {
				if ( ! array_key_exists( $form_id, $submission ) ) {
					continue;
				}

				$fields = $submission[ $form_id ]->column_keys();
				foreach ( $fields as $field_slug ) {
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
		include_once __DIR__ . '/SubmissionModel.php';

		$base_submissions = $this->base_form->get_submissions();

		if ( empty( $base_submissions ) ) {
			wp_die( __( 'Yet, there were no forms submitted, so there is nothing to export.' ) );
		}

		foreach ( $base_submissions as $submission ) {
			$this->processSubmission( $submission );
		}
	}

	/**
	 * @param SubmissionModel $submission
	 * @param bool $base_id
	 * @param int $direction
	 */
	private function processSubmission( $submission, $base_id = false, $direction = self::DIRECTION_BOTH ) {
		// add form id
		try {
			$form                           = $submission->meta_get_form();
			$this->forms[ $form->get_id() ] = $form;
		} catch ( Exception $e ) {
			wp_die( $e->getMessage() );

			return;
		}

		// add submission
		$id = $base_id ? $base_id : $submission->meta_get_id();

		$this->submissions[ $id ][ $form->get_id() ] = $submission;

		if ( $direction !== self::DIRECTION_DESCENDANT ) {
			$predecessor = $submission->meta_get_predecessor();
			if ( $predecessor ) {
				$this->processSubmission( $predecessor, $submission->meta_get_id(), self::DIRECTION_PREDECESSOR );
			}
		}

		if ( $direction !== self::DIRECTION_PREDECESSOR ) {
			$descendant = $submission->meta_get_descendant();

			if ( $descendant ) {
				$this->processSubmission( $descendant, $submission->meta_get_id(), self::DIRECTION_DESCENDANT );
			}
		}
	}
}
