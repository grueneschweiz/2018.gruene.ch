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
	private $__twitterName;
	
	public function excerpt( $length = 280 ) {
		// bail early to ensure we'll never compute it twice
		if ( $this->__excerpt ) {
			return $this->__excerpt;
		}
		
		$this->__excerpt = $this->limit_length( $this->full_excerpt(), $length );
		
		return $this->__excerpt;
	}
	
	private function limit_length( $text, $limit ) {
		if ( strlen( $text ) > $limit ) {
			$trimmed = substr( $text, 0, strpos( $text, ' ', $limit ) );
			
			return $trimmed . ' [&hellip;]';
		}
		
		return $text;
	}
	
	public function full_excerpt() {
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
			$tmp = $this->generate_excerpt();
		}
		
		$this->__fullExcerpt = wptexturize( $tmp );
		
		return $this->__fullExcerpt;
	}
	
	private function generate_excerpt() {
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
		
		return '';
	}
	
	public function twitter_name() {
		// bail early to ensure we'll never compute it twice
		if ( null !== $this->__twitterName ) {
			return $this->__twitterName;
		}
		
		if ( class_exists( '\WPSEO_Options' ) ) {
			$twitter_name        = apply_filters( 'wpseo_twitter_site', \WPSEO_Options::get( 'twitter_site' ) );
			$twitter_name        = trim( $twitter_name, "@\t\n\r\0\x0B" ); // remove @ (if there is one) and trim
			$this->__twitterName = $twitter_name;
		} else {
			$this->__twitterName = '';
		}
		
		return $this->__twitterName;
	}
}
