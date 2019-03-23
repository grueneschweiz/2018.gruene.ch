<?php

namespace SUPT;

class SubmissionsTable extends WP_List_Table {
	const NUMB_COLUMNS_TO_DISPLAY = 7;
	const DELETE_ACTION = 'delete';
	const BULK_DELETE_ACTION = 'bulk-delete';
	const EXPORT_ACTION = 'export';

	/**
	 * The id of the form, whose submissions shall be displayed
	 *
	 * @var int
	 */
	private $form_id;

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
	 * Return ssociative array with the column slug as key and the
	 * name as value.
	 *
	 * @return array
	 */
	public function get_columns() {
		$columns['cb']     = '<input type="checkbox" />';
		$columns['_meta_'] = __( 'Timestamp', THEME_DOMAIN );

		return array_merge( $columns, $this->get_form_columns() );
	}

	/**
	 * Return columns of the current form
	 *
	 * @return array
	 */
	private function get_form_columns() {
		$form = $this->get_current_form();

		$columns = [];
		foreach ( $form->get_columns() as $key => $label ) {
			$max_label       = wp_trim_words( strip_tags( $label ), 4, '...' );
			$columns[ $key ] = $max_label;
		}

		return $columns;
	}

	/**
	 * Return fields of the current form
	 *
	 * @return array
	 */
	private
	function get_form_fields() {
		$form = $this->get_current_form();

		return $form->get_fields();
	}

	/**
	 * Return a list of sortable columns.
	 *
	 * @return array
	 */
	public
	function get_sortable_columns() {
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
	private
	function get_form_id() {
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

		$newest_form   = reset( $forms );
		$this->form_id = $newest_form->get_id();

		return $this->form_id;
	}

	/**
	 * Prepares the list of items for displaying.
	 */
	public
	function prepare_items() {
		$this->set_column_headers();

		$this->process_action();

		// todo: implement the sorting

		$submissions = $this->get_current_form()->get_submissions();
		$total_items = count( $submissions );
		$per_page    = $this->get_items_per_page( 'submissions_per_page' );

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil( $total_items / $per_page )
		) );

		$current_page = $this->get_pagenum();

		$this->items = array_slice( $submissions, ( ( $current_page - 1 ) * $per_page ), $per_page );
	}

	/**
	 * Delete a submission
	 *
	 * @param int $id post meta id
	 */
	public
	function delete_item(
		$id
	) {
		require_once __DIR__ . '/SubmissionModel.php';

		try {
			$submission = new SubmissionModel( $id );
			$submission->delete();
		} catch ( \Exception $e ) {
			wp_die( __( 'Could not remove form submission. Was it already deleted?', THEME_DOMAIN ) );
		}
	}

	/**
	 * Text displayed when no submissions are available
	 */
	public
	function no_items() {
		_e( 'Yet, there were no forms submitted.', THEME_DOMAIN );
	}

	/**
	 * Define the table header columns
	 */
	private
	function set_column_headers() {
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
	 * @param SubmissionModel $item
	 * @param string $column_name
	 *
	 * @return string
	 */
	protected
	function column_default(
		$item, $column_name
	) {
		$value = $item->{'get_' . $column_name}();

		switch ( $this->get_form_fields()[ $column_name ]['form_input_type'] ) {
			case 'confirmation':
				return empty( $value ) ? '' : 'X';
			case 'checkbox':
				$values = empty( $value ) ? [] : $value;

				return implode( ', ', $values );
			default:
				return empty( $value ) ? '' : $value;
		}
	}

	/**
	 * Return the localized timestamp
	 *
	 * @param SubmissionModel $item
	 *
	 * @return string
	 */
	protected
	function column__meta_(
		$item
	) {
		if ( ! empty( $item->meta_get_timestamp() ) ) {
			$date_format = get_option( 'date_format' );
			$time_format = get_option( 'time_format' );
			$format      = $date_format . ' - ' . $time_format;
			$timestamp   = date_i18n( $format, strtotime( $item->meta_get_timestamp() ) );
		} else {
			$timestamp = '';
		}

		$delete_nonce = wp_create_nonce( FormType::MODEL_NAME . '_delete_submission-' . $item->meta_get_id() );

		$actions = [
			FormType::VIEW_ACTION => sprintf(
				'<a href="?post_type=%s&page=%s&action=%s&item=%d">' . __( 'View', THEME_DOMAIN ) . '</a>',
				esc_attr( $_REQUEST['post_type'] ),
				esc_attr( $_REQUEST['page'] ),
				FormType::VIEW_ACTION,
				$item->meta_get_id()
			),
			FormType::EDIT_ACTION => sprintf(
				'<a href="?post_type=%s&page=%s&action=%s&item=%d">' . __( 'Edit', THEME_DOMAIN ) . '</a>',
				esc_attr( $_REQUEST['post_type'] ),
				esc_attr( $_REQUEST['page'] ),
				FormType::EDIT_ACTION,
				$item->meta_get_id()
			),
			self::DELETE_ACTION   => sprintf(
				'<a href="?post_type=%s&page=%s&form_id=%d&action=%s&item=%d&_wpnonce=%s">' . __( 'Delete', THEME_DOMAIN ) . '</a>',
				esc_attr( $_REQUEST['post_type'] ),
				esc_attr( $_REQUEST['page'] ),
				$this->get_form_id(),
				self::DELETE_ACTION,
				$item->meta_get_id(),
				$delete_nonce
			)
		];

		return $timestamp . $this->row_actions( $actions );
	}

	/**
	 * Return array with the forms indexed by the form id
	 *
	 * @return FormModel[]
	 */
	private
	function get_forms() {
		if ( ! empty( $this->forms ) ) {
			return $this->forms;
		}

		require_once __DIR__ . '/FormModel.php';

		$args = array(
			'posts_per_page' => - 1,
			'post_type'      => FormType::MODEL_NAME,
		);

		$this->forms = [];
		foreach ( get_posts( $args ) as $form ) {
			try {
				$this->forms[ $form->ID ] = new FormModel( $form->ID, $form );
			} catch ( \Exception $e ) {
				wp_die( $e->getMessage() );
			}
		}

		return $this->forms;
	}

	/**
	 * Add the form selector
	 */
	function the_form_selector() {
		$forms = $this->get_forms();

		// sort by form title
		uasort( $forms, function ( $form1, $form2 ) {
			/** @var $form1 FormModel */
			/** @var $form2 FormModel */
			return strcasecmp( $form1->get_title(), $form2->get_title() );
		} );

		$form_id = $this->get_form_id();

		echo '<form method="get">';
		echo '<input type="hidden" name="post_type" value="' . FormType::MODEL_NAME . '">';
		echo '<input type="hidden" name="page" value="submissions">';
		echo '<select name="form_id">';
		foreach ( $forms as $form ) {
			$selected = $form->get_id() === $form_id ? 'selected="selected"' : '';
			echo "<option value='{$form->get_id()}' {$selected}>{$form->get_title()}</option>";
		}
		echo '</select>';

		echo '<input type="submit" class="button button-primary" value="' . __( 'Select form', THEME_DOMAIN ) . '">';
		echo '</form>';
	}

	/**
	 * Echo the markup for the export button
	 */
	function the_export_button() {
		$nonce = wp_create_nonce( FormType::MODEL_NAME . '_export-' . $this->get_form_id() );
		$url   = sprintf( "?post_type=%s&page=%s&action=%s&form_id=%d&_wpnonce=%s&noheader=true",
			esc_attr( $_REQUEST['post_type'] ),
			esc_attr( $_REQUEST['page'] ),
			self::EXPORT_ACTION,
			$this->get_form_id(),
			$nonce
		);

		echo "<a href='$url' class='button action'>" . __( 'Export .xlsx', THEME_DOMAIN ) . "</a>";
	}

	/**
	 * Return the title of the current form
	 *
	 * @return string
	 */
	function get_form_name() {
		return $this->get_current_form()->get_title();
	}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param SubmissionModel $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%s[]" value="%d" />',
			self::BULK_DELETE_ACTION,
			$item->meta_get_id()
		);
	}

	/**
	 * Handle form actions
	 */
	public function process_action() {
		switch ( $this->current_action() ) {
			case self::DELETE_ACTION:
				$this->process_action_delete();

				return;
			case self::BULK_DELETE_ACTION:
				$this->process_action_bulk_delete();

				return;
			case self::EXPORT_ACTION:
				$this->process_action_export();

				return;
		}
	}

	/**
	 * Handle bulk delete action
	 */
	private function process_action_bulk_delete() {
		$nonce = esc_attr( $_REQUEST['_wpnonce'] );

		if ( ! wp_verify_nonce( $nonce, 'bulk-submissions' ) ) {
			wp_die( 'Invalid action.' );
		}

		if ( ! current_user_can( 'delete_private_posts' ) ) {
			wp_die( 'Insufficient privileges.' );
		}

		foreach ( $_REQUEST[ self::BULK_DELETE_ACTION ] as $id ) {
			$this->delete_item( absint( $id ) );
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
		}

		if ( ! current_user_can( 'delete_private_posts' ) ) {
			wp_die( 'Insufficient privileges.' );
		}

		$this->delete_item( $id );
	}

	/**
	 * Handle export action
	 */
	private function process_action_export() {
		$nonce = esc_attr( $_REQUEST['_wpnonce'] );
		$id    = absint( $_REQUEST['form_id'] );

		if ( ! wp_verify_nonce( $nonce, FormType::MODEL_NAME . '_export-' . $id ) ) {
			wp_die( 'Invalid action.' );
		}

		if ( ! current_user_can( 'read_private_posts' ) ) {
			wp_die( 'Insufficient privileges.' );
		}

		require_once __DIR__ . '/SubmissionExport.php';
		$exporter = new SubmissionExport( $this->get_form_id() );
		$exporter->run();
		exit;
	}

	private function get_current_form() {
		$forms = $this->get_forms();

		return $forms[ $this->get_form_id() ];
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