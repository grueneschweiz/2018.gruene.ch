<?php

namespace SUPT;

use WP_REST_Request;

class ProgressHelper {
	private $form_id;
	private $offset;
	private $current_value;
	private $goal_value;
	private $legend;
	private $current_value_cache;
	private $goal_cache;

	/**
	 * @param string $datasource accepts 'manual' and 'form'
	 * @param int|null $form_id must be set if datasource is form. ignored otherwise
	 * @param int $offset number to add to the number of form submissions. ignored for datasource manual
	 * @param int $current_value progress to display. ignored for datasource 'form'
	 * @param string $goal_mode accepts 'manual' and 'adaptive'
	 * @param int $goal_value goal to display. ignored for goal_mode 'adaptive'
	 * @param string|null $legend legend template string
	 */
	private function __construct(
		string $datasource = 'manual',
		?int $form_id = null,
		int $offset = 0,
		int $current_value = 0,
		string $goal_mode = 'manual',
		int $goal_value = 0,
		?string $legend = null
	) {
		if ( 'form' === $datasource ) {
			$this->form_id = $form_id;
			$this->offset  = $offset;
		} else {
			$this->current_value = $current_value;
		}

		if ( 'manual' === $goal_mode ) {
			$this->goal_value = $goal_value;
		}

		$this->legend = $legend;
	}

	public static function handle_api_request( WP_REST_Request $request ): array {
		$progress = new self(
			'form',
			$request->get_param( 'id' ),
			$request->get_param( 'o' ),
			0,
			$request->get_param( 'gm' ),
			$request->get_param( 'g' ),
			urldecode( $request->get_param( 'l' ) )
		);

		return array(
			'current' => $progress->current(),
			'goal'    => $progress->goal(),
			'legend'  => $progress->legend(),
		);
	}

	public static function cache_submission_count( int $form_id ) {
		$transient = "theme_form-submission-count-form-$form_id";
		$count     = supt_theme_form_submission_count( $form_id );
		set_transient( $transient, $count, DAY_IN_SECONDS );
	}

	public function current() {
		if ( $this->current_value_cache ) {
			return $this->current_value_cache;
		}

		if ( ! $this->form_id ) {
			return $this->current_value;
		}

		$submissions = $this->get_cached_multilang_form_submission_count();

		if ( ! empty( $this->offset ) ) {
			$submissions += $this->offset;
		}

		/**
		 * Filters the number of form submissions (incl. offset) for the progress bar.
		 *
		 * @param int $submissions The displayed submission count
		 * @param int $form_id The form id
		 * @param int $offset The number that was added to the real submission count
		 *
		 * @since 0.28.0
		 *
		 */
		$this->current_value_cache = apply_filters(
			FormType::MODEL_NAME . '-submission-count',
			$submissions,
			$this->form_id,
			$this->offset
		);

		return $this->current_value_cache;
	}

	public function goal() {
		if ( $this->goal_cache ) {
			return $this->goal_cache;
		}

		if ( $this->goal_value ) {
			return $this->goal_value;
		}

		$current = $this->current();

		if ( $current < 10 ) {
			return 10;
		}

		// the goal is at around 110% of the current value
		$unrounded_goal = $current * 1.1;

		// the step size is basically the next round number (20 for 11,
		// 100 for 98, 6000 for 5123 etc.)
		$digits    = ceil( log10( $unrounded_goal ) );
		$step_size = 10 ** ( $digits - 1 );

		// if unrounded goal is below 40, 400, 4000 etc. halve the step size
		// so we have a goal of 35, 350, 3500 etc. for values of 31, 310, 3100
		// instead of 40, 400, 4000.
		if ( $unrounded_goal < 4 * $step_size ) {
			$step_size /= 2;
		}

		// increase the goal as soon as we reach 97% fulfillment
		$step_count = ceil( $current * 1.03 / $step_size );

		$this->goal_cache = $step_count * $step_size;

		return $this->goal_cache;
	}

	public function legend() {
		if ( ! $this->legend ) {
			return false;
		}

		$replacements = array(
			'{{current}}' => '<span class="a-progress__legend-value">' . $this->current() . '</span>',
			'{{goal}}'    => $this->goal()
		);

		$legend = strip_tags( $this->legend, '<p><br><wbr><i><em><strong><b>' );

		return str_replace( array_keys( $replacements ), array_values( $replacements ), $legend );
	}

	public static function from_array( $array ): ProgressHelper {
		$args = array_merge(
			array(
				'datasource' => 'manual',
				'form'       => null,
				'offset'     => 0,
				'current'    => 0,
				'goal'       => 'manual',
				'goal_value' => 0,
				'legend'     => null
			),
			$array
		);

		return new self(
			$args['datasource'],
			(int) $args['form'],
			(int) $args['offset'],
			(int) $args['current'],
			$args['goal'],
			(int) $args['goal_value'],
			$args['legend']
		);
	}

	public function current_percent() {
		return 100 * $this->current() / $this->goal();
	}

	private function get_cached_multilang_form_submission_count(): int {
		$submissions = self::get_cached_form_submission_count( (int) $this->form_id );

		// add submissions of same form in other languages
		if ( function_exists( 'pll_get_post_translations' ) ) {
			$post_ids = pll_get_post_translations( (int) $this->form_id );
			foreach ( $post_ids as $id ) {
				if ( $id === $this->form_id ) {
					continue;
				}

				$count = self::get_cached_form_submission_count( $id );
				if ( $count > 0 ) {
					$submissions += $count;
				}
			}
		}

		return $submissions;
	}

	private static function get_cached_form_submission_count( $form_id ): int {
		$transient = "theme_form-submission-count-form-$form_id";
		$count     = get_transient( $transient );

		if ( false === $count ) {
			self::cache_submission_count( $form_id );
			$count = get_transient( $transient );
		}

		if ( false === $count ) {
			$count = supt_theme_form_submission_count( $form_id );
		}

		return (int) $count;
	}
}
