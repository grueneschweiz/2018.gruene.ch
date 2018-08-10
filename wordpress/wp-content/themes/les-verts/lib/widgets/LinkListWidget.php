<?php
/**
 * Created by PhpStorm.
 * User: cyrillbolliger
 * Date: 10.08.18
 * Time: 11:58
 */

namespace SUPT;


/**
 * Class LinkListWidget
 *
 * This widget is mainly a placeholder, acf is used for adding the fields
 *
 * @package SUPT
 */
class LinkListWidget extends Widget {
	function __construct() {
		parent::__construct(
			'supt_link_list_widget',
			__( 'Link List', \THEME_DOMAIN ),
			array(
				'description' => __( 'Add some links to the green family', \THEME_DOMAIN )
			)
		);
		
		$this->set_title_default( __( 'The green family', \THEME_DOMAIN ) );
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
		
		echo '<div class="o-footer__link-list">';
		echo $args['before_widget'];
		\Timber::render( 'atoms/a-footer-link-list.twig', array(
			'title'   => $args['before_title']  . $instance['title'] . $args['after_title'],
			'list' => get_field( 'list', 'widget_' . $id ),
		) );
		echo $args['after_widget'];
		echo '</div>';
	}
}
