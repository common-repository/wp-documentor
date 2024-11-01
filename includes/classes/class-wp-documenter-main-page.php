<?php

namespace WPDOC;

class WP_Documenter_Main_Page {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'menu_page' ), 11 );
		add_action( 'admin_menu', array( $this, 'remove_menu_page' ), 20 );
	}

	public function menu_page() {
		add_menu_page(
			'',							// Page title, won't be used in this case due to the redirect
			'WP Documenter',			// Menu title
			'manage_options',			// Cap needed
			'wp-documenter',			// Menu slug
			'',							// Callback function.  Empty because the first submenu is handling the output.
			'dashicons-book',			// Icon
			83							// Position
		);
	}

	/*
	 * So when you create an admin page through add_menu_page(), then create submenus using
	 * add_submenu_page(), the original menu page adds itself as the first submenu in the
	 * group.  We don't want that in this case, so we have to remove the submenu page from
	 * itself.
	 */

	public function remove_menu_page() {
		remove_submenu_page( 'wp-documenter', 'wp-documenter' );
	}
}