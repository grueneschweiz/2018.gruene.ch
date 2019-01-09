<?php

namespace SUPT;


class Submissions_Table extends WP_List_Table {
	const NUMB_COLUMNS_TO_DISPLAY = 7;
	const DELETE_ACTION = 'delete';
	const BULK_DELETE_ACTION = 'bulk-delete';
	
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
	 * Cache for get_forms()
	 *
	 * @var array
	 */
	private $forms;
	
	/**
	 * Class constructor
	 */
	public function __construct() {
		parent::__construct( [
			'singular' => __( 'Submission', THEME_DOMAIN ),
			'plural'   => __( 'Submissions', THEME_DOMAIN ),
			'ajax'     => false
		] );
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
		
		$this->columns['cb']     = '<input type="checkbox" />';
		$this->columns['_meta_'] = __( 'Timestamp', THEME_DOMAIN );
		
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
		if ( $this->form_fields ) {
			return $this->form_fields;
		}
		
		$this->form_fields = [];
		
		foreach ( get_field( 'form_fields', $this->get_form_id() ) as $field ) {
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
	 * Return the form id set in the GET param or the id of the newest form
	 *
	 * @return int
	 */
	private function get_form_id() {
		if ( $this->form_id ) {
			return $this->form_id;
		}
		
		$forms = $this->get_forms();
		
		if ( empty( $forms ) ) {
			wp_die( __( 'No forms available', THEME_DOMAIN ) );
		}
		
		if ( ! empty( $_GET['form_id'] ) ) {
			$form_id = (int) filter_var( $_GET['form_id'], FILTER_SANITIZE_NUMBER_INT );
			if ( in_array( $form_id, array_keys( $forms ) ) ) {
				$this->form_id = $form_id;
				
				return $this->form_id;
			} else {
				wp_die( __( 'Invalid form', THEME_DOMAIN ) );
			}
		}
		
		$newest_form   = array_pop( $forms );
		$this->form_id = $newest_form->ID;
		
		return $this->form_id;
	}
	
	/**
	 * Prepares the list of items for displaying.
	 */
	public function prepare_items() {
		$this->set_column_headers();
		
		$this->process_action();
		
		// todo: implement the sorting
		
		$data        = $this->get_post_meta_with_id( $this->get_form_id(), FormType::MODEL_NAME );
		$total_items = count( $data );
		$per_page    = $this->get_items_per_page( 'submissions_per_page' );
		
		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil( $total_items / $per_page )
		) );
		
		$current_page = $this->get_pagenum();
		
		$this->items = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );
	}
	
	/**
	 * Delete a submission
	 *
	 * @param int $id post meta id
	 */
	public function delete_item( $id ) {
		global $wpdb;
		
		$wpdb->delete(
			$wpdb->postmeta,
			[ 'meta_id' => $id ],
			[ '%d' ]
		);
	}
	
	/**
	 * Text displayed when no submissions are available
	 */
	public function no_items() {
		_e( 'Yet, there were no form submitted', THEME_DOMAIN );
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
			$timestamp   = date_i18n( $format, strtotime( $item['_meta_']['timestamp'] ) );
		} else {
			$timestamp = '';
		}
		
		$delete_nonce = wp_create_nonce( FormType::MODEL_NAME . '_delete_submission-' . $item['ID'] );
		
		$actions = [
			'view'              => sprintf(
				'<a href="?post_type=%s&page=%s&action=%s&item=%d">' . __( 'View', THEME_DOMAIN ) . '</a>',
				esc_attr( $_REQUEST['post_type'] ),
				esc_attr( $_REQUEST['page'] ),
				'view',
				absint( $item['ID'] )
			),
			'edit'              => sprintf(
				'<a href="?post_type=%s&page=%s&action=%s&item=%d">' . __( 'Edit', THEME_DOMAIN ) . '</a>',
				esc_attr( $_REQUEST['post_type'] ),
				esc_attr( $_REQUEST['page'] ),
				'view',
				absint( $item['ID'] )
			),
			self::DELETE_ACTION => sprintf(
				'<a href="?post_type=%s&page=%s&form_id=%d&action=%s&item=%d&_wpnonce=%s">' . __( 'Delete', THEME_DOMAIN ) . '</a>',
				esc_attr( $_REQUEST['post_type'] ),
				esc_attr( $_REQUEST['page'] ),
				$this->get_form_id(),
				self::DELETE_ACTION,
				absint( $item['ID'] ),
				$delete_nonce
			)
		];
		
		return $timestamp . $this->row_actions( $actions );
	}
	
	/**
	 * Return array with the forms indexed by the form id
	 *
	 * @return array
	 */
	private function get_forms() {
		if ( ! empty( $this->forms ) ) {
			return $this->forms;
		}
		
		$args = array(
			'posts_per_page' => - 1,
			'post_type'      => FormType::MODEL_NAME,
		);
		
		$this->forms = [];
		foreach ( get_posts( $args ) as $form ) {
			$this->forms[ $form->ID ] = $form;
		}
		
		return $this->forms;
	}
	
	/**
	 * Add the form selector
	 */
	function the_form_selector() {
		$forms   = $this->get_forms();
		$form_id = $this->get_form_id();
		
		echo '<form method="get">';
		echo '<input type="hidden" name="post_type" value="' . FormType::MODEL_NAME . '">';
		echo '<input type="hidden" name="page" value="submissions">';
		echo '<select name="form_id">';
		foreach ( $forms as $form ) {
			$selected = $form->ID === $form_id ? ' selected="selected"' : '';
			echo "<option value='{$form->ID}'{$selected}>{$form->post_title}</option>";
		}
		echo '</select>';
		
		echo '<input type="submit" class="button button-primary" value="' . __( 'Formular auswählen', THEME_DOMAIN ) . '">';
		echo '</form>';
	}
	
	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%s[]" value="%d" />',
			self::BULK_DELETE_ACTION,
			$item['ID']
		);
	}
	
	/**
	 * Retrieve metadata with id for the specified object.
	 *
	 * @param int $post_id ID of the object metadata is for
	 * @param string $meta_key Optional. Metadata key. If not specified, retrieve all metadata for
	 *                        the specified object.
	 *
	 * @return array
	 */
	private function get_post_meta_with_id( $post_id, $meta_key = '' ) {
		global $wpdb;
		$results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = %s",
			$post_id, $meta_key ) );
		
		if ( empty( $results ) ) {
			return [];
		}
		
		$data = [];
		foreach ( $results as $result ) {
			$id                = $result->meta_id;
			$data[ $id ]       = (array) maybe_unserialize( $result->meta_value );
			$data[ $id ]['ID'] = $id;
		}
		
		return $data;
	}
	
	/**
	 * Handle form actions
	 */
	public function process_action() {
		switch ($this->current_action()) {
			case self::DELETE_ACTION:
				$this->process_action_delete();
				return;
			case self::BULK_DELETE_ACTION:
				$this->process_action_bulk_delete();
				return;
			// todo: case edit, case view
			// note: we may have to exit for the cases edit and view
		}
	}
	
	/**
	 * Handle bulk delete action
	 */
	private function process_action_bulk_delete() {
		$nonce = esc_attr( $_REQUEST['_wpnonce'] );
		if ( ! wp_verify_nonce( $nonce, 'bulk-submissions' ) ) {
			wp_die( 'Invalid action.' );
		} else {
			foreach ( $_REQUEST[ self::BULK_DELETE_ACTION ] as $id ) {
				$this->delete_item( absint( $id ) );
			}
			
			return;
		}
	}
	
	/**
	 * Handle delete action
	 */
	private function process_action_delete() {
		$nonce = esc_attr( $_REQUEST['_wpnonce'] );
		$id    = absint( $_REQUEST['item'] );
		
		if ( ! wp_verify_nonce( $nonce, FormType::MODEL_NAME . '_delete_submission-' . $id ) ) {
			wp_die( 'Invalid action.' );
		} else {
			$this->delete_item( $id );
			
			return;
		}
	}
	
	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = [
			self::BULK_DELETE_ACTION => __( 'Delete', THEME_DOMAIN )
		];
		
		return $actions;
	}
}
