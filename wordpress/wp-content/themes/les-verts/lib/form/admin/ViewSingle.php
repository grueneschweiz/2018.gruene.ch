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
		echo '<table>';
		foreach ( $submission->column_keys() as $col_key ) {
			$item = $submission->{"get_pretty_$col_key"}();

			printf( '<tr><th>%s</th><td>%s</td></tr>',
				$item['label'], $item['value'] );
		}

		// todo: add meta data
		echo '</table></div>';
	}
}
