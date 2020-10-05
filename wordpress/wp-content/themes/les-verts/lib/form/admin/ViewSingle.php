<?php

namespace SUPT;

use Exception;

class ViewSingle {
	/**
	 * @param SubmissionModel $submission
	 *
	 * @throws Exception
	 */
	public static function display( $submission ) {
		echo '<div class="wrap">';
		printf( '<h1 class="wp-heading-inline">%s</h1>', __( 'Submission', THEME_DOMAIN ) );
		self::print_submissions( $submission );
		echo '</div>';
	}

	/**
	 * @param SubmissionModel $submissions
	 *
	 * @throws Exception
	 */
	private static function print_submissions( $submissions ) {
		$submissions = self::get_submissions( [ $submissions ] );

		foreach ( $submissions as $submission ) {
			self::display_single_submission( $submission );
		}
	}

	/**
	 * @param SubmissionModel[] $submissions
	 *
	 * @return SubmissionModel[]
	 *
	 * @throws Exception
	 */
	private static function get_submissions( $submissions ) {
		$submissions = array_values( $submissions );

		$first = $submissions[0];
		$last  = $submissions[ count( $submissions ) - 1 ];

		$pred = $first->meta_get_predecessor();
		$desc = $last->meta_get_descendant();

		// base case
		if ( ! $pred && ! $desc ) {
			return $submissions;
		}

		if ( $pred ) {
			array_unshift( $submissions, $pred );
		}

		if ( $desc ) {
			$submissions[] = $desc;
		}

		return self::get_submissions( $submissions );
	}

	/**
	 * @param SubmissionModel $submission
	 *
	 * @throws Exception
	 */
	private static function display_single_submission( $submission ) {
		$form = $submission->meta_get_form();

		printf( '<div class="%s">', FormType::MODEL_NAME . '-submission' );
		printf( '<h2>%s</h2>', $form->get_title() );
		printf( '<table class="%s">', FormType::MODEL_NAME . '-submission-table widefat striped' );

		foreach ( $submission->column_keys() as $col_key ) {
			$item = $submission->{"get_pretty_$col_key"}();

			printf( '<tr><th scope="row"><strong>%s</strong></th><td>%s</td></tr>',
				$item['label'], $item['value'] );
		}

		$date_format = get_option( 'date_format' );
		$time_format = get_option( 'time_format' );
		$format      = $date_format . ' - ' . $time_format;
		$timestamp   = date_i18n( $format, strtotime( $submission->meta_get_timestamp() ) );

		printf( '<tr><th scope="row"><strong>%s</strong></th><td>%s</td></tr>',
			__( 'Timestamp', THEME_DOMAIN ), $timestamp );

		$referer = $submission->meta_get_referer();

		printf(
			'<tr><th scope="row"><strong>%s</strong></th><td><a href="%s" target="_blank">%s</a></td></tr>',
			__( 'Referer', THEME_DOMAIN ),
			$referer,
			$referer
		);

		echo '</table></div>';
	}
}
