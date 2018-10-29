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
	
	public function excerpt( $length = 280 ) {
		// bail early to ensure we'll never compute it twice
		if ( $this->__excerpt ) {
			return $this->__excerpt;
		}
		
		$this->__excerpt = $this->limit_length( $this->fullExcerpt(), $length );
		
		return $this->__excerpt;
	}
	
	private function limit_length( $text, $limit ) {
		if ( strlen( $text ) > $limit ) {
			$trimmed = substr( $text, 0, strpos( $text, ' ', $limit ) );
			
			return $trimmed . ' [&hellip;]';
		}
		
		return $text;
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
}
