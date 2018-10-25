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
	private $__fullExcerpt;
	
	public function excerpt() {
		// bail early to ensure we'll never compute it twice
		if ( $this->__excerpt ) {
			return $this->__excerpt;
		}
		
		$this->__excerpt = $this->trim_words( $this->fullExcerpt() );
		
		return $this->__excerpt;
	}
	
	public function fullExcerpt() {
		// bail early to ensure we'll never compute it twice
		if ( $this->__fullExcerpt ) {
			return $this->__fullExcerpt;
		}
		
		if ( ! empty( $this->teaser ) ) {
			$tmp = $this->teaser;
		}
		
		if ( ! empty( $this->excerpt ) ) {
			$tmp = $this->excerpt;
		}
		
		if ( empty( $tmp ) ) {
			$tmp = $this->generateExcerpt();
		}
		
		$this->__fullExcerpt = $tmp;
		
		return $this->__fullExcerpt;
	}
	
	private function generateExcerpt() {
		if ( empty( $this->content ) ) {
			return '';
		}
		foreach ( $this->content as $block ) {
			if ( 'text' === $block['acf_fc_layout'] ) {
				$text = strip_shortcodes( $block['text'] );
				$text = apply_filters( 'the_content', $text );
				$text = str_replace( ']]>', ']]&gt;', $text );
				
				return $text;
			}
		}
	}
	
	private function trim_words( $text ) {
		$excerpt_length = apply_filters( 'excerpt_length', 55 );
		$excerpt_more   = apply_filters( 'excerpt_more', ' ' . '[&hellip;]' );
		return wp_trim_words( $text, $excerpt_length, $excerpt_more );
	}
}
