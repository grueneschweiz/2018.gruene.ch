<?php
/**
 * Created by PhpStorm.
 * User: cyrillbolliger
 * Date: 10.08.18
 * Time: 11:58
 */

namespace SUPT;

use Timber\Timber;


/**
 * Class ContactWidget
 *
 * This widget is mainly a placeholder, acf is used for adding the fields
 *
 * @package SUPT
 */
class ContactWidget extends Widget {
	function __construct() {
		parent::__construct(
			'supt_contact_widget',
			__( 'Contact', \THEME_DOMAIN ),
			array(
				'description' => __( 'Presents your contact details', \THEME_DOMAIN )
			)
		);
		
		$this->set_title_default( __( 'Contact', \THEME_DOMAIN ) );
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
		
		echo '<div class="o-footer__contact">';
		echo $args['before_widget'];
		\Timber::render( 'molecules/m-footer-contact.twig', array(
			'title'        => $args['before_title'] . $instance['title'] . $args['after_title'],
			'address'      => get_field( 'address', 'widget_' . $id ),
			'email'        => get_field( 'email', 'widget_' . $id ),
			'social_media' => get_field( 'social_media', 'widget_' . $id ),
		) );
		echo $args['after_widget'];
		echo '</div>';
	}
}
