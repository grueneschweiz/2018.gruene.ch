<?php

namespace SUPT;

class Progress_controller {

	private $form_id;
	private $offset;
	private $current_value;
	private $goal_value;
	private $legend;
	private $current_value_cache;
	private $goal_cache;

	public function __construct( $array ) {
		if ( 'form' === $array['datasource'] ) {
			$this->form_id = $array['form'];
			$this->offset  = $array['offset'];
		} else {
			$this->current_value = $array['current'];
		}

		if ( 'manual' === $array['goal'] ) {
			$this->goal_value = (int) $array['goal_value'];
		}

		$this->legend = $array['legend'];
	}

	public static function register() {
		add_filter( 'get_twig', function ( $twig ) {
			$twig->addFunction( new \Twig\TwigFunction( 'Progress_Bar', function ( $array ) {
					return new self( $array );
				} )
			);

			return $twig;
		} );
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

	public function current() {
		if ( $this->current_value_cache ) {
			return $this->current_value_cache;
		}

		if ( ! $this->form_id ) {
			return $this->current_value;
		}

		// this is expensive, so we'll cache it in $this->current
		$submissions = supt_theme_form_submission_count( (int) $this->form_id );

		if ( ! empty( $this->offset ) ) {
			$submissions += $this->offset;
		}

		$this->current_value_cache = $submissions;

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
		$digits         = ceil( log10( $unrounded_goal ) );
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

	public function current_percent() {
		return 100 * $this->current() / $this->goal();
	}
}
