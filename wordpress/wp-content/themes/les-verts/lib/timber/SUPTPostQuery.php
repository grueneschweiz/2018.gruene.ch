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
	
	public function found_posts() {
		if (!$this->_found_posts) {
			$this->_found_posts = wp_count_posts()->publish;
		}
		
		return $this->_found_posts;
	}
}
