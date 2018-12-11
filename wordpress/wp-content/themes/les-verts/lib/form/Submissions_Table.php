<?php

namespace SUPT;


class Submissions_Table extends WP_List_Table {
	const POSTS_PER_PAGE = 50;
	const NUMB_COLUMNS_TO_DISPLAY = 5;
	
	/**
	 * The id of the form, whose submissions shall be displayed
	 *
	 * @var int
	 */
	private $form_id;
	
	/**
	 * Cache for get_columns()
	 *
	 * @var array
	 */
	private $columns;
	
	/**
	 * Cache for get_form_fields()
	 *
	 * @var array
	 */
	private $form_fields;
	
	/**
	 * @param int $form_id
	 */
	public function set_form_id( $form_id ) {
		$this->form_id = $form_id;
	}
	
	/**
	 * Return a chached associative array with the column slug as key and the
	 * name as value.
	 *
	 * @return array
	 */
	public function get_columns() {
		if ( $this->columns ) {
			return $this->columns;
		}
		
		$this->columns = [ '_meta_' => __( 'Timestamp', THEME_DOMAIN ) ];
		foreach ( $this->get_form_fields() as $key => $field ) {
			$this->columns[ $key ] = $field['form_input_label'];
		}
		
		return $this->columns;
	}
	
	/**
	 * Return the cached fields of the current form in an associative array
	 * that contains the field slug as key.
	 *
	 * @return array
	 */
	private function get_form_fields() {
		if ( empty( $this->form_id ) ) {
			wp_die( 'The form id must be set, before prepare_items is called.' );
		}
		
		if ( $this->form_fields ) {
			return $this->form_fields;
		}
		
		$this->form_fields = [];
		
		foreach ( get_field( 'form_fields', $this->form_id ) as $field ) {
			$key                       = supt_slugify( $field['form_input_label'] );
			$this->form_fields[ $key ] = $field;
		}
		
		return $this->form_fields;
	}
	
	/**
	 * Return a list of sortable columns.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = [];
		foreach ( $this->get_columns() as $column_slug => $column_value ) {
			$sortable_columns[ $column_slug ] = $column_slug;
		}
		
		return $sortable_columns;
	}
	
	/**
	 * Prepares the list of items for displaying.
	 */
	public function prepare_items() {
		if ( empty( $this->form_id ) ) {
			wp_die( 'The form id must be set, before prepare_items is called.' );
		}
		
		$this->set_column_headers();
		
		$data        = get_post_meta( $this->form_id, FormType::MODEL_NAME );
		$total_items = count( $data );
		
		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => self::POSTS_PER_PAGE,
			'total_pages' => ceil( $total_items / self::POSTS_PER_PAGE )
		) );
		
		$current_page = $this->get_pagenum();
		
		$this->items = array_slice( $data, ( ( $current_page - 1 ) * self::POSTS_PER_PAGE ), self::POSTS_PER_PAGE );
	}
	
	/**
	 * Define the table header columns
	 */
	private function set_column_headers() {
		$columns = $this->get_columns();
		$hidden  = [];
		$count   = count( $columns );
		
		if ( $count > self::NUMB_COLUMNS_TO_DISPLAY ) {
			$columns = array_slice( $columns, 0, self::NUMB_COLUMNS_TO_DISPLAY, true );
			$hidden  = array_slice( $this->get_columns(), self::NUMB_COLUMNS_TO_DISPLAY, null, true );
		}
		
		$sortable = $this->get_sortable_columns();
		$first    = array_keys( $columns )[0];
		
		$this->_column_headers = array( $columns, $hidden, $sortable, $first );
	}
	
	/**
	 * Prepare output of the cell
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return string
	 */
	protected function column_default( $item, $column_name ) {
		switch ( $this->get_form_fields()[ $column_name ]['form_input_type'] ) {
			case 'confirmation':
				return empty( $item[ $column_name ] ) ? '' : 'X';
			case 'checkbox':
				$values = empty( $item[ $column_name ] ) ? [] : $item[ $column_name ];
				
				return implode( ', ', $values );
			default:
				return empty( $item[ $column_name ] ) ? '' : $item[ $column_name ];
		}
	}
	
	/**
	 * Return the localized timestamp
	 *
	 * @param $item
	 *
	 * @return string
	 */
	protected function column__meta_( $item ) {
		if ( ! empty( $item['_meta_']['timestamp'] ) ) {
			$date_format = get_option( 'date_format' );
			$time_format = get_option( 'time_format' );
			$format      = $date_format . ' - ' . $time_format;
			
			return date_i18n( $format, strtotime( $item['_meta_']['timestamp'] ) );
		}
	}
}
