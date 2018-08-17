<?php
/**
 * Created by PhpStorm.
 * User: cyrillbolliger
 * Date: 14.08.18
 * Time: 15:28
 */

namespace SUPT;

class ACFPost extends \TimberPost {
	private $__excerpt;
	
	public function excerpt() {
		// bail early to ensure we'll never compute it twice
		if ( $this->__excerpt ) {
			return $this->__excerpt;
		}
		
		if ( ! empty( $this->acf_excerpt ) ) {
			$this->__excerpt = $this->acf_excerpt;
		}
		
		if ( ! empty( $this->lead ) ) {
			$this->__excerpt = $this->lead;
		}
		
		if ( empty( $this->__excerpt ) ) {
			$this->__excerpt = $this->generateExcerpt();
		}
		
		return $this->__excerpt;
	}
	
	private function generateExcerpt() {
		if ( empty( $this->content ) ) {
			return __( 'No content found.', THEME_DOMAIN );
		}
		foreach ( $this->content as $block ) {
			if ( 'text' === $block['acf_fc_layout'] ) {
				$text           = strip_shortcodes( $block['text'] );
				$text           = apply_filters( 'the_content', $text );
				$text           = str_replace( ']]>', ']]&gt;', $text );
				$excerpt_length = apply_filters( 'excerpt_length', 55 );
				$excerpt_more   = apply_filters( 'excerpt_more', ' ' . '[&hellip;]' );
				$text           = wp_trim_words( $text, $excerpt_length, $excerpt_more );
				
				return $text;
			}
		}
	}
}
