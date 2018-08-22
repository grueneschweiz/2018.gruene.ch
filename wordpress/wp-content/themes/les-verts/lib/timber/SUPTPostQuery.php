<?php
/**
 * Created by PhpStorm.
 * User: cyrillbolliger
 * Date: 22.08.18
 * Time: 11:28
 */

namespace SUPT;


class SUPTPostQuery extends \Timber\PostQuery {
	private $_found_posts = 0;
	
	public function __construct( $query = false, $post_class = '\Timber\Post' ) {
		// this does all the magic
		add_filter('found_posts', function($found_posts){
			$this->_found_posts = $found_posts;
			return $found_posts;
		});
		
		parent::__construct($query, $post_class);
	}
	
	public function found_posts() {
		return $this->_found_posts;
	}
}
