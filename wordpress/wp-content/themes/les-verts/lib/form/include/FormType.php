<?php

namespace SUPT;

use Exception;
use WP_Post;

require_once dirname( __DIR__, 2 ) . '/post-types/Model.php';

class FormType extends Model {

	const MODEL_NAME = 'theme_form';
	const COLUMN_FIELD_NAME = 'fields';
	const VIEW_ACTION = 'view';
	const EDIT_ACTION = 'edit';

	public static function register_type() {
		self::register_post_type();
		self::register_acf_fields();
	}

	public static function register_post_type() {
		register_post_type( self::MODEL_NAME,
			array(
				'labels'              => array(
					'name'               => _x( 'Forms', 'post type general name', THEME_DOMAIN ),
					'singular_name'      => _x( 'Form', 'post type singular name', THEME_DOMAIN ),
					'menu_name'          => _x( 'Forms', 'admin menu', THEME_DOMAIN ),
					'name_admin_bar'     => _x( 'Form', 'add new on admin bar', THEME_DOMAIN ),
					'add_new'            => _x( 'Add New', 'form', THEME_DOMAIN ),
					'add_new_item'       => __( 'Add New Form', THEME_DOMAIN ),
					'new_item'           => __( 'New Form', THEME_DOMAIN ),
					'edit_item'          => __( 'Edit Form', THEME_DOMAIN ),
					'view_item'          => __( 'View Form', THEME_DOMAIN ),
					'all_items'          => __( 'All Forms', THEME_DOMAIN ),
					'search_items'       => __( 'Find form', THEME_DOMAIN ),
					'parent_item_colon'  => __( 'Form:', THEME_DOMAIN ),
					'not_found'          => __( 'No form found', THEME_DOMAIN ),
					'not_found_in_trash' => __( 'No form in the trash', THEME_DOMAIN )
				),
				'menu_icon'           => 'dashicons-feedback', // See https://developer.wordpress.org/resource/dashicons
				'public'              => false,
				'show_ui'             => true,
				'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'show_in_nav_menus'   => false,
				'has_archive'         => false, // false = do not generate an archive (list) page
				'supports'            => array(
					'title',
					'revisions',
				)
			)
		);
	}

	public static function register_acf_fields() {
		require_once __DIR__ . '/../acf/form-local-settings.php';
		require_once __DIR__ . '/../acf/input.php';
		require_once __DIR__ . '/../acf/form-details.php';
		require_once __DIR__ . '/../acf/mail-template.php';

		self::maybe_remove_webling_field_settings();
	}

	/**
	 * Unless api key and url are defined, remove the Webling tab from the input
	 * field config if api key and url
	 */
	private static function maybe_remove_webling_field_settings() {
		$api_key = get_field( 'client_secret', 'option' );
		$api_url = get_field( 'api_url', 'option' );

		if ( ! $api_key || ! $api_url ) {
			// remove the webling tab
			add_filter( 'acf/load_field/key=field_5a869960c1cf2', function ( $fields ) {
				$fields_to_remove = [
					'field_5c0fac32bdbd6',
					'field_5c0fac61bdbd7',
					'field_5c0fc373bdf7b',
					'field_5c0fc5b4eece5',
					'field_5c0fc84a086db',
					'field_5d3f1c8af12df',
					'field_5d3f1c8af09ab',
					'field_5alk49sf14pd4',
				];

				foreach ( $fields['sub_fields'] as $key => $field ) {
					if ( in_array( $field['key'], $fields_to_remove, true ) ) {
						unset( $fields['sub_fields'][ $key ] );
					}
				}

				return $fields;
			} );

			// remove the crm specific field types (prefixed with 'crm_')
			add_filter( 'acf/load_field/key=field_59f33814cf0dc', function ( $field ) {
				$field['choices'] = array_filter( $field['choices'], static function ( $key ) {
					return false === strpos( $key, 'crm_' );
				}, ARRAY_FILTER_USE_KEY );

				return $field;
			} );
		}

		// hide the mailchimp field if Mailchimp is not configured
		add_filter( 'acf/load_field/key=field_59f33e699d496', function( $field ) {
			if ( ! MailchimpSaver::has_mailchimp_api_key() ) {
				return false;
			}

			return $field;
		});
	}

	public static function register_actions_filters() {
		add_action( 'admin_enqueue_scripts',
			array( __CLASS__, 'admin_enqueue_scripts' ) );

		add_filter( 'manage_edit-' . self::MODEL_NAME . '_columns',
			array( __CLASS__, 'register_columns_fields' ) );
		add_action( 'manage_' . self::MODEL_NAME . '_posts_custom_column',
			array( __CLASS__, 'populate_columns_fields' ), 10, 2 );
		add_action( 'post_row_actions',
			array( __CLASS__, 'alter_row_actions' ), 10, 2 );
		add_action( 'request',
			array( __CLASS__, 'order_by_title' ) );

		add_action( 'admin_menu',
			array( __CLASS__, 'menu_add_submissions_page' ) );

		add_filter( 'set-screen-option',
			array( __CLASS__, 'save_submissions_per_page_option' ), 10, 3 );

		add_filter( 'pll_get_post_types',
			array( __CLASS__, 'enable_polylang_support' ), 10, 2 );

		add_action( 'mtphr_post_duplicator_created',
			array( __CLASS__, 'duplicate_remove_submissions' ), 10, 2 );

		add_filter( 'wp_privacy_personal_data_exporters',
			array( __CLASS__, 'register_gdpr_exporter' ) );
		add_filter( 'wp_privacy_personal_data_erasers',
			array( __CLASS__, 'register_gdpr_eraser' ) );
	}

	/**
	 * Register exporter for personal data of this plugin
	 *
	 * @param array $exporters the registered exporter functions
	 *
	 * @return array with the exporter function for this plugin added
	 *
	 * @see https://developer.wordpress.org/plugins/privacy/adding-the-personal-data-exporter-to-your-plugin/
	 */
	public static function register_gdpr_exporter( array $exporters ): array {
		require_once __DIR__ . '/GDPRHelper.php';

		$exporters[ self::MODEL_NAME ] = array(
			'exporter_friendly_name' => __( 'Forms', THEME_DOMAIN ),
			'callback'               => array( GDPRHelper::class, 'exporter' ),
		);

		return $exporters;
	}

	/**
	 * Register eraser for personal data of this plugin
	 *
	 * @param array $erasers the registered eraser functions
	 *
	 * @return array with the eraser function for this plugin added
	 *
	 * @see https://developer.wordpress.org/plugins/privacy/adding-the-personal-data-eraser-to-your-plugin/
	 */
	public static function register_gdpr_eraser( array $erasers ): array {
		require_once __DIR__ . '/GDPRHelper.php';

		$erasers[ self::MODEL_NAME ] = array(
			'eraser_friendly_name' => __( 'Forms', THEME_DOMAIN ),
			'callback'             => array( GDPRHelper::class, 'eraser' ),
		);

		return $erasers;
	}

	/**
	 * Prevent duplicated submissions when using post-duplicator to clone form
	 *
	 * @param int $original_id
	 * @param int $duplicate_id
	 */
	public static function duplicate_remove_submissions( int $original_id, int $duplicate_id ) {
		try {
			$form = new FormModel( $duplicate_id );
			foreach ( $form->get_submissions() as $submission ) {
				$submission->delete();
			}
		} catch ( Exception $e ) {
			add_action( 'admin_notices', function () {
				$class   = 'notice notice-warning is-dismissible';
				$message = __( 'Unfortunately not only the form, but also the submissions were copied and we could not delete them. So make sure you delete the submissions linked with your newly cloned form manually.', THEME_DOMAIN );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			} );
		}
	}

	/**
	 * Make post type translatable with polylang
	 *
	 * @param array $post_types the array of post type names
	 * @param bool $is_settings true when displaying the list of custom post types in Polylang settings
	 *
	 * @return array
	 */
	public static function enable_polylang_support( array $post_types, bool $is_settings ): array {
		if ( $is_settings ) {
			// hides $t from the list of custom post types in Polylang settings
			unset( $post_types[ self::MODEL_NAME ] );
		} else {
			// enables language and translation management for $t
			$post_types[] = self::MODEL_NAME;
		}

		return $post_types;
	}

	/**
	 * Enqueue custom scripts & styles
	 */
	public static function admin_enqueue_scripts() {

		if ( self::MODEL_NAME !== get_post_type() ) {
			return;
		}

		$folder_uri = get_template_directory_uri() . '/lib/form';
		wp_enqueue_script( 'form_populate_placeholder_tags', "$folder_uri/scripts/input-populate-placeholder-tags.js",
			false, THEME_VERSION, true );
	}

	/**
	 * Add extra columns in edit table
	 *
	 * @param array $columns An array of column names.
	 *
	 * @return array
	 */
	public static function register_columns_fields( $columns ): array {
		return array_merge(
			array_slice( $columns, 0, 2 ),
			array( self::COLUMN_FIELD_NAME => __( 'Fields', THEME_DOMAIN ) ),
			array_slice( $columns, 2 )
		);
	}

	/**
	 * Populate extra columns with specific content, if available
	 *
	 * @param string $column_name The name of the column to display.
	 * @param int $post_id The current post ID.
	 */
	public static function populate_columns_fields( string $column_name, int $post_id ) {
		if ( $column_name === self::COLUMN_FIELD_NAME ) {
			$fields = get_field( 'form_fields', $post_id );

			if ( empty( $fields ) ) {
				return;
			}

			echo implode( '; ', array_map( function ( $f ) {
				$label = wp_trim_words( strip_tags( $f['form_input_label'] ), 4, '...' );

				return $label . ( $f['form_input_required'] ? '*' : '' );
			}, $fields ) );

		}
	}

	/**
	 * Remove the quickedit and the view row actions and
	 * add an action to view the submissions instead.
	 *
	 * @param array $actions
	 * @param WP_Post $post
	 *
	 * @return array
	 */
	public static function alter_row_actions( array $actions, WP_Post $post ): array {
		if ( $post->post_type === self::MODEL_NAME ) {
			if ( isset( $actions['inline hide-if-no-js'] ) ) {
				unset( $actions['inline hide-if-no-js'] );
			}

			if ( isset( $actions['view'] ) ) {
				unset( $actions['view'] );
			}

			$url = sprintf( "?post_type=%s&page=submissions&form_id=%d",
				self::MODEL_NAME,
				$post->ID
			);

			/** @noinspection HtmlUnknownTarget */
			$actions['submissions'] = sprintf( '<a href="%s" aria-label="%s">%s</a>',
				$url,
				sprintf( _x( 'View submissions of %s', 'form title', THEME_DOMAIN ), $post->post_title ),
				__( 'Submissions', THEME_DOMAIN )
			);
		}

		return $actions;
	}

	/**
	 * Set the default order to "Title ASC" if no order set
	 *
	 * @param array $query_vars The array of requested query variables.
	 *
	 * @return array
	 */
	public static function order_by_title( array $query_vars ): array {
		global $pagenow, $post_type;
		if ( $pagenow !== 'edit.php' || $post_type !== self::MODEL_NAME || ! is_admin() ) {
			return $query_vars;
		}

		if ( empty( filter_input( INPUT_GET, 'orderby' ) ) && empty( filter_input( INPUT_GET, 'order' ) ) ) {
			$query_vars['orderby'] = 'title';
			$query_vars['order']   = 'ASC';
		}

		return $query_vars;
	}

	public static function menu_add_submissions_page() {
		// add page
		$hook = add_submenu_page( 'edit.php?post_type=' . self::MODEL_NAME, __( 'Submissions', THEME_DOMAIN ),
			__( 'Submissions', THEME_DOMAIN ), 'edit_pages', 'submissions', array( __CLASS__, 'display_submissions_page' ) );

		// add screen option
		add_action( "load-$hook", function () {
			$args = [
				'label'   => 'Submissions per page',
				'default' => 20,
				'option'  => 'submissions_per_page'
			];
			add_screen_option( 'per_page', $args );
		} );
	}

	public static function save_submissions_per_page_option(
		/** @noinspection PhpUnusedParameterInspection */
		$status, $option, $value
	) {
		if ( 'submissions_per_page' === $option ) {
			return $value;
		}

		return null;
	}

	public static function display_submissions_page() {
		if ( isset( $_REQUEST['action'] ) && self::VIEW_ACTION === $_REQUEST['action'] ) {
			self::display_single_submission();

			return;
		}

		require_once __DIR__ . '/../helpers/class-wp-list-table.php';
		require_once 'SubmissionsTable.php';

		$submissions = new SubmissionsTable();
		$submissions->prepare_items();

		?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php _e( 'Form Submissions', THEME_DOMAIN ); ?></h1>
			<?php $submissions->the_form_selector() ?>
			<form method="get">
				<input type="hidden" name="post_type" value="<?php echo FormType::MODEL_NAME ?>">
				<input type="hidden" name="page" value="submissions">
				<?php if ( ! empty( $_REQUEST['form_id'] ) ) {
					echo '<input type="hidden" name="form_id" value="' . absint( $_REQUEST['form_id'] ) . '"">';
				} ?>
				<?php $submissions->display() ?>
			</form>
			<div style="margin-top: 3em;">
				<h3><?php echo __( 'Export Submissions', THEME_DOMAIN ) ?></h3>
				<p><?php echo sprintf(
						__( 'Exports the submission of the form "%s" and all its linked forms. Usually its best to select the first form in the chain before exporting.', THEME_DOMAIN ),
						$submissions->get_form_name()
					) ?></p>
				<?php $submissions->the_export_button() ?>
			</div>
		</div>
		<?php
	}

	public static function display_single_submission() {
		require_once __DIR__ . '/SubmissionModel.php';
		require_once dirname( __DIR__ ) . '/admin/ViewSingle.php';

		try {
			$id         = absint( $_REQUEST['item'] );
			$submission = new SubmissionModel( $id );
			ViewSingle::display( $submission );
		} catch ( Exception $e ) {
			/** @noinspection ForgottenDebugOutputInspection */
			wp_die( __( 'Ups, something went wrong.', THEME_DOMAIN ) );
		}
	}
}
