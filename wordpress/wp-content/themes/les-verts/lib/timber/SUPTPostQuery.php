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
	private $_archive_url;
	private $_archive_description;
	private $_archive_title;
	private $_categories = array();
	private $_tags = array();
	
	public function __construct( $query = false, $post_class = '\Timber\Post' ) {
		parent::__construct( $query, $post_class );
	}
	
	/**
	 * Return the total number of posts
	 *
	 * @return int
	 */
	public function found_posts() {
		if ( ! $this->_found_posts ) {
			if ( $this->get_query() ) {
				// works before the query was executed
				// this is used for the link lists more button
				$query = new \WP_Query( $this->get_query() );
			} else {
				// works after the query was executed
				// this is used on the archives page
				global $wp_query;
				$query = $wp_query;
			}
			
			$this->_found_posts = $query->found_posts;
		}
		
		return $this->_found_posts;
	}
	
	/**
	 * Return an accurate title according to the query
	 *
	 * @return string
	 */
	public function archive_title() {
		if ( ! $this->_archive_title ) {
			$tags = count( $this->get_tags() );
			$cats = count( $this->get_categories() );
			
			if ( ( $tags && $cats ) || $cats > 1 || $tags > 1 ) {
				$this->_archive_title = __( 'Filtered posts', THEME_DOMAIN );
			} else if ( is_day() ) {
				$this->_archive_title = sprintf(
					__( 'Archive: %s', THEME_DOMAIN ),
					get_the_date()
				);
			} else if ( is_month() ) {
				$this->_archive_title = sprintf(
					__( 'Archive: %s', THEME_DOMAIN ),
					get_the_date( 'F Y' )
				);
			} else if ( is_year() ) {
				$this->_archive_title = sprintf(
					__( 'Archive: %s', THEME_DOMAIN ),
					get_the_date( 'Y' )
				);
			} else if ( is_tag() ) {
				$this->_archive_title = single_tag_title( '', false );
			} else if ( is_category() ) {
				$this->_archive_title = single_cat_title( '', false );
			} else if ( is_post_type_archive() ) {
				$this->_archive_title = post_type_archive_title( '', false );
			} else {
				$this->_archive_title = __( 'Archive', THEME_DOMAIN );
			}
		}
		
		return $this->_archive_title;
	}
	
	/**
	 * Return cached tags as term objects if this archive is queried by tags
	 *
	 * @return mixed
	 */
	private function get_tags() {
		if ( ! $this->_tags ) {
			global $wp_query;
			
			if ( ! empty( $wp_query->query['tag'] ) ) {
				$slugs = $this->parse_slugs( $wp_query->query['tag'] );
				foreach ( $slugs as $slug ) {
					$this->_tags[] = get_term_by( 'slug', $slug, 'post_tag' );
				}
			} else if ( is_tag() ) {
				$this->_tags[] = $wp_query->get_queried_object();
			}
		}
		
		return $this->_tags;
	}
	
	/**
	 * Helper function to get ab array of slugs out of the given string
	 *
	 * @param string $string
	 *
	 * @return array
	 */
	private function parse_slugs( $string ) {
		if ( strpos( $string, '+' ) ) {
			$slugs = explode( '+', $string );
		} elseif ( strpos( $string, ',' ) ) {
			$slugs = explode( ',', $string );
		} else {
			$slugs = [ $string ];
		}
		
		return $slugs;
	}
	
	/**
	 * Return cached categories as term objects if this archive is queried by categories
	 *
	 * @return mixed
	 */
	private function get_categories() {
		if ( ! $this->_categories ) {
			global $wp_query;
			
			if ( ! empty( $wp_query->query['category_name'] ) ) {
				$slugs = $this->parse_slugs( $wp_query->query['category_name'] );
				foreach ( $slugs as $slug ) {
					$this->_categories[] = get_term_by( 'slug', $slug, 'category' );
				}
			} else if ( is_category() ) {
				$this->_categories[] = $wp_query->get_queried_object();
			}
		}
		
		return $this->_categories;
	}
	
	/**
	 * Get cached archive description
	 *
	 * @return string
	 */
	public function archive_description() {
		if ( ! $this->_archive_description ) {
			$tags = $this->get_tags();
			$cats = $this->get_categories();
			
			if ( count($cats) > 1 ) {
				$names = array();
				foreach ( $cats() as $category ) {
					$names[] = $category->name;
				}
				$cat_string = implode( ', ', $names );
			}
			
			if ( count($tags) > 1 ) {
				$names = array();
				foreach ( $tags as $tag ) {
					$names[] = $tag->name;
				}
				$tag_string = implode( ', ', $names );
			}
			
			if ( ! empty( $cat_string ) && ! empty( $tag_string ) ) {
				$this->_archive_description = sprintf(
					__( 'Here you will find any content categorized under %s and tagged with %s.' ),
					$cat_string,
					$tag_string
				);
			} else if ( ! empty( $cat_string ) ) {
				$this->_archive_description = sprintf(
					__( 'Here you will find any content categorized under %s.' ),
					$cat_string
				);
			} else if ( ! empty( $tag_string ) ) {
				$this->_archive_description = sprintf(
					__( 'Here you will find any content tagged with %s.' ),
					$tag_string
				);
			} else if ( is_tag() ) {
				$this->_archive_description = tag_description();
			} else if ( is_category() ) {
				$this->_archive_description = category_description();
			}
		}
		
		return $this->_archive_description;
	}
	
	/**
	 * Return cached url to an archive with the same category and tag settings.
	 *
	 * @return string
	 */
	public function archive_url() {
		if ( ! $this->_archive_url ) {
			$this->_archive_url = $this->build_archive_url();
		}
		
		return $this->_archive_url;
	}
	
	/**
	 * Get the url that leads to the currently listed posts.
	 *
	 * NOTE: At the moment only categories and tags are supported query vars.
	 *
	 * @return string
	 */
	private function build_archive_url() {
		$query = $this->get_query();
		
		$criteria = array();
		$criteria = array_merge( $criteria, $this->category_url_array( $query ) );
		$criteria = array_merge( $criteria, $this->tag_url_array( $query ) );
		
		$base = get_site_url();
		
		return add_query_arg( $criteria, $base );
	}
	
	/**
	 * Get the query vars from $query ready to be used as a url query where not
	 * all query var arguments are supported.
	 *
	 * @link https://codex.wordpress.org/Class_Reference/WP_Query#Category_Parameters
	 * @link https://codex.wordpress.org/WordPress_Query_Vars
	 *
	 * @param $query
	 *
	 * @return array|\WP_Error
	 */
	private function category_url_array( $query ) {
		if ( ! empty( $query['cat'] ) ) {
			return [ 'cat' => $query['cat'] ];
		}
		
		if ( ! empty( $query['category_name'] ) ) {
			return [ 'category_name' => $query['category_name'] ];
		}
		
		if ( ! empty( $query['category__and'] ) ) {
			$category_slugs = [];
			foreach ( $query['category__and'] as $cat_id ) {
				$category         = get_category( $cat_id );
				$category_slugs[] = $category->slug;
			}
			
			return [ 'category_name' => urlencode( implode( '+', $category_slugs ) ) ];
		}
		
		if ( ! empty( $query['category__in'] ) ) {
			return [ 'cat' => urlencode( implode( ',', $query['category__in'] ) ) ];
		}
		
		if ( ! empty( $query['category__not_in'] ) ) {
			$not_cat_ids = array_map( function ( $id ) {
				return $id * - 1;
			}, $query['category__not_in'] );
			
			return [ 'cat' => urlencode( implode( ',', $not_cat_ids ) ) ];
		}
		
		return array();
	}
	
	/**
	 * Get the query vars from $query ready to be used as a url query where not
	 * all query var arguments are supported.
	 *
	 * @link https://codex.wordpress.org/Class_Reference/WP_Query#Tag_Parameters
	 * @link https://codex.wordpress.org/WordPress_Query_Vars
	 *
	 * @param $query
	 *
	 * @return array|\WP_Error
	 */
	private function tag_url_array( $query ) {
		if ( ! empty( $query['tag'] ) ) {
			return [ 'tag' => $query['tag'] ];
		}
		
		if ( ! empty( $query['tag_id'] ) ) {
			$term = get_term( $query['tag_id'] );
			
			return [ 'tag' => $term->slug ];
		}
		
		if ( ! empty( $query['tag__and'] ) ) {
			$tag_slugs = [];
			foreach ( $query['tag__and'] as $tag_id ) {
				$term        = get_term( $tag_id );
				$tag_slugs[] = $term->slug;
			}
			
			return [ 'tag' => urlencode( implode( '+', $tag_slugs ) ) ];
		}
		
		if ( ! empty( $query['tag_slug__and'] ) ) {
			return [ 'tag' => urlencode( implode( '+', $query['tag_slug__and'] ) ) ];
		}
		
		if ( ! empty( $query['tag__in'] ) ) {
			$tag_slugs = [];
			foreach ( $query['tag__in'] as $tag_id ) {
				$term        = get_term( $tag_id );
				$tag_slugs[] = $term->slug;
			}
			
			return [ 'tag' => urlencode( implode( ',', $tag_slugs ) ) ];
		}
		
		if ( ! empty( $query['tag_slug__in'] ) ) {
			return [ 'tag' => urlencode( implode( ',', $query['tag_slug__in'] ) ) ];
		}
		
		if ( ! empty( $query['tag__not_in'] ) ) {
			return new \WP_Error( 'tag__not_in__unsupported',
				__( 'The tag not in function is not supported.', THEME_DOMAIN ) );
		}
		
		return array();
	}
}
