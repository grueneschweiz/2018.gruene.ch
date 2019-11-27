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

		// todo: fix me (test with 101)

		$digits         = ceil( log10( $current ) );
		$unrounded_goal = $current * 1.2;

		$norming_divisor = 10 ** ( $digits - 1 );
		$normed_goal     = $unrounded_goal / $norming_divisor;

		// allow X5 values below 4X
		// 35 is a valid goal, 45 gets 50. 350 is valid, 450 gets 500
		$rounding_factor = $unrounded_goal / $normed_goal < 4 ? 2 : 1;

		$this->goal_cache = round( $normed_goal * $rounding_factor ) * $norming_divisor / $rounding_factor;

		return $this->goal_cache;
	}

	public function current_percent() {
		return 100 * $this->current() / $this->goal();
	}
}
