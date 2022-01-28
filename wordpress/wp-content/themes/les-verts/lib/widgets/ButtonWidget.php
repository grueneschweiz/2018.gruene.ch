<?php
/**
 * Created by PhpStorm.
 * User: cyrillbolliger
 * Date: 10.08.18
 * Time: 11:58
 */

namespace SUPT;


use const THEME_DOMAIN;

/**
 * Class ButtonWidget
 *
 * This widget is mainly a placeholder, acf is used for adding the fields
 *
 * @package SUPT
 */
class ButtonWidget extends Widget {
	function __construct() {
		parent::__construct(
			'supt_button_widget',
			__( 'Button', THEME_DOMAIN ),
			array(
				'description' => __( 'Show your main calls to action', THEME_DOMAIN )
			)
		);

		$this->set_title_default( __( 'Get active', THEME_DOMAIN ) );
	}

	/**
	 * Frontend output
	 *
	 * @param array $args
	 * @param array $instance
	 *
	 * @uses Timber
	 * @uses The https://www.advancedcustomfields.com/ Plugin
	 */
	public function widget( $args, $instance ) {
		if ( ! class_exists( '\Timber' ) ) {
			parent::widget( $args, $instance );

			return;
		}

		$id = $args['widget_id'];

		echo '<div class="o-footer__cta">';
		echo $args['before_widget'];
		/** @noinspection PhpFullyQualifiedNameUsageInspection */
		\Timber::render( 'molecules/m-footer-cta.twig', array(
			'title'   => $args['before_title'] . '<span class="m-footer-cta__title">' . $instance['title'] . '</span>' . $args['after_title'],
			'buttons' => get_field( 'buttons', 'widget_' . $id ),
		) );
		echo $args['after_widget'];
		echo '</div>';
	}
}
