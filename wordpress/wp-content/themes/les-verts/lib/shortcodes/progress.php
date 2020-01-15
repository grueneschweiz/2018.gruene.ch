<?php

namespace SUPT;

class Progress_shortcode {

	public static function register() {
		add_shortcode( 'progressbar', [self::class, 'process']);
	}

	public static function process($atts) {
		$atts = shortcode_atts( array(
			'datasource'    => null,
			'goal'          => 'adaptive',
			'legend'        => false,
			'form'          => null,
			'offset'        => null,
			'current_value' => null,
			'current'       => null,
			'goal_value'    => null,
		), $atts, 'progressbar' );

		if ( ! isset ( $atts['datasource'] ) ) {
			return self::show_error();
		}

		if ( 'form' === $atts['datasource'] ) {
			if ( ! isset( $atts['form'] ) ) {
				return self::show_error();
			}

			if ( ! isset( $atts['offset'] ) ) {
				$atts['offset'] = 0;
			}
		} else {
			if ( isset( $atts['current_value'] ) ) {
				$atts['current'] = $atts['current_value'];
			}
			if ( ! isset( $atts['current'] ) ) {
				return self::show_error();
			}
		}

		if ( 'adaptive' !== $atts['goal'] ) {
			$atts['goal_value'] = (int) $atts['goal'];
			$atts['goal']       = 'manual';
		}

		// This time we use Timber::compile since shortcodes should return the code
		return \Timber::compile( 'atoms/a-progress.twig', [ 'block' => $atts ] );
	}

	public static function show_error() {
		if ( ! current_user_can( 'edit_post', get_the_ID() ) ) {
			return '';
		}

		$title       = __( 'Invalid shortcode', THEME_DOMAIN );
		$examples    = __( 'Valid examples:', THEME_DOMAIN );
		$from_form   = __( 'Data from form', THEME_DOMAIN );
		$from_manual = __( 'Data manually entered', THEME_DOMAIN );
		$goal_auto   = __( 'automatic goal', THEME_DOMAIN );
		$goal_manual = __( 'manual goal', THEME_DOMAIN );
		$with_legend = __( 'with legend', THEME_DOMAIN );
		$legend      = __( '{{current}} have signed. Can we reach {{goal}}?', THEME_DOMAIN );

		return <<<END
<div style="color: red">
	<h3>$title</h3>
	<p>$examples</p>
	<ul>
		<li>$from_form, $goal_auto: <code><span>&#91;</span>progressbar datasource="form" form="{{form_id}}" offset="123" goal="adaptive"&#93;</code></li>
		<li>$from_form, $goal_manual: <code><span>&#91;</span>progressbar datasource="form" form="{{form_id}}" offset="123" goal="150"&#93;</code></li>
		<li>$from_form, $goal_manual, $with_legend: <code><span>&#91;</span>progressbar datasource="form" form="{{form_id}}" offset="123" goal="150" legend="$legend"&#93;</code></li>
		<li>$from_manual, $goal_auto: <code><span>&#91;</span>progressbar datasource="manual" current_value="94" goal="adaptive"&#93;</code></li>
		<li>$from_manual, $goal_manual: <code><span>&#91;</span>progressbar datasource="manual" current_value="94" goal="150"&#93;</code></li>
		<li>$from_manual, $goal_manual, $with_legend: <code><span>&#91;</span>progressbar datasource="manual" current_value="94" goal="150" legend="$legend"&#93;</code></li>
</ul>
</div>
END;
	}

}
