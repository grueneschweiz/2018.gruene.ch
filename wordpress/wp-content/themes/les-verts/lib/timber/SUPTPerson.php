<?php
/**
 * Created by PhpStorm.
 * User: cyrillbolliger
 * Date: 14.08.18
 * Time: 15:28
 */

namespace SUPT;

use Timber\Post;

class SUPTPerson extends Post {
	private $__testimonials;
	
	public function quote( $taxonomy ) {
		$quote = $this->getTestimonialField( 'quote', $taxonomy );
		
		return $quote ? wptexturize( $quote ) : '';
	}
	
	private function getTestimonialField( $field, $taxonomy ) {
		foreach ( $this->getTestimonials() as $testimonial ) {
			if ( ! empty( array_intersect( $taxonomy, $testimonial['taxonomy'] ) ) ) {
				return $testimonial[ $field ];
			}
		}
		
		return false;
	}
	
	private function getTestimonials() {
		if ( ! $this->__testimonials ) {
			$this->__testimonials = get_field( 'testimonials' );
		}
		
		return $this->__testimonials;
	}
	
	public function role( $taxonomy ) {
		$role = $this->getTestimonialField( 'role', $taxonomy );
		
		return $role ? $role : '';
	}
}
