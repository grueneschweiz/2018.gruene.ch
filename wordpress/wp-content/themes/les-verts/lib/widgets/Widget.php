<?php
/**
 * Created by PhpStorm.
 * User: cyrillbolliger
 * Date: 10.08.18
 * Time: 12:41
 */

namespace SUPT;


abstract class Widget extends \WP_Widget {
	private $title_default = '';
	
	/**
	 * @param string $title_default
	 */
	public function set_title_default( $title_default ) {
		$this->title_default = $title_default;
	}
	
	/**
	 * Frontend output
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		echo 'This widget only works in combination with the theme"' . \THEME_DOMAIN . '". The output is generated with timber.';
		echo $args['after_widget'];
	}
	
	/**
	 * Backend form
	 *
	 * @param array $instance
	 *
	 * @return string|void
	 */
	public function form( $instance ) {
		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		} else {
			$title = $this->title_default;
		}
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
			       name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
			       value="<?php echo esc_attr( $title ); ?>"/>
		</p>
		<?php
	}
	
	/**
	 * Save form input
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance          = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		
		return $instance;
	}
}
