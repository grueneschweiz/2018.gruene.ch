<?php

/**
 * Add menu meta box
 *
 * @param object $object The meta box object
 *
 * @link https://developer.wordpress.org/reference/functions/add_meta_box/
 */
add_filter( 'nav_menu_meta_box_object', function ( $object ) {
	if ( ! is_admin() || ! defined('TRIBE_EVENTS_FILE')) {
		return $object;
	}
	
	add_meta_box( 'supt-agenda-menu-meta-box', __( 'Automatic Agenda' ), 'supt_agenda_menu_meta_box', 'nav-menus', 'side',
		'default' );
	
	return $object;
} );

/**
 * Displays a metabox for an author menu item.
 *
 * @global int|string $nav_menu_selected_id (id, name or slug) of the currently-selected menu
 */
function supt_agenda_menu_meta_box() {
	global $_nav_menu_placeholder, $nav_menu_selected_id;
	$_nav_menu_placeholder = 0 > $_nav_menu_placeholder ? $_nav_menu_placeholder - 1 : - 1;
	?>
	<div id="agendamenu" class="posttypediv">
		<div id="tabs-panel-agendamenu" class="tabs-panel tabs-panel-active">
			<ul id="agendamenu-checklist" class="categorychecklist form-no-clear">
				<li>
					<label class="menu-item-title">
						<input type="checkbox" class="menu-item-checkbox"
						       name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-object-id]"
						       value="-1"> <?php esc_html_e( 'Automatic Agenda', THEME_DOMAIN ); ?>
					</label>
					<input type="hidden" class="menu-item-type"
					       name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-type]" value="custom">
					<input type="hidden" class="menu-item-title"
					       name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-title]"
					       value="<?php esc_html_e( 'Agenda', THEME_DOMAIN ); ?>">
					<input type="hidden" class="menu-item-url"
					       name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-url]" value="#supt_agenda">
				</li>
			</ul>
		</div>
		<p class="button-controls">
					<span class="add-to-menu">
						<input type="submit" <?php disabled( $nav_menu_selected_id, 0 ); ?>
						       class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to Menu' ); ?>"
						       name="add-post-type-menu-item" id="submit-agendamenu">
						<span class="spinner"> </span>
					</span>
		</p>
	</div>
	<?php
}
