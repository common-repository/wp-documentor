<?php

namespace WPDOC;

class WP_Documenter_Plugins_Page implements \WPDOC\WP_Documenter_Page_Interface {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'menu_page' ), 12 );
		add_action( 'admin_init', array( $this, 'settings_init' ) );
	}

	public function menu_page() { 
		add_submenu_page(
			'wp-documenter',						// Slug of the parent
			'Plugin Descriptions',					// Page title (tab name)
			'Plugin Descriptions',					// Menu title (WP sidebar menu)
			'manage_options',						// Cap needed
			'wp-documenter-plugins',				// Page slug
			array( $this ,'form_output' )			// Callback function which outputs the form
		);
	}

	public function settings_init() {
		register_setting(
			'wpdocPluginsPage', 					// Option group
			'wpdoc_plugin_descriptions',			// Option name
			array( $this, 'sanitize_input' )		// Sanitization callback
		);
	
		add_settings_section(
			'wpdoc_plugin_page_fields',				// Section ID
			'',										// Section title. Not needed, handled by wpdoc_add_plugins_submenu()
			'',										// Section callback. Not needed, handled by wpdoc_plugins_form_output()
			'wpdocPluginsPage'						// Option group
		);
	
		add_settings_field(
			'wpdoc_plugin_description_output',		// Field ID
			'', 									// Field title
			array( $this, 'description_render' ),	// Callback function to generate field's HTML
			'wpdocPluginsPage',						// Option group which this field should appear in
			'wpdoc_plugin_page_fields'				// ID of the section this should be in
		);
	}

	public function description_render() {
		$options = get_option( 'wpdoc_plugin_descriptions' );
		$all_installed_plugins = get_plugins();
		$active_plugins = get_option( 'active_plugins' );
	
		foreach( $all_installed_plugins as $slug => $plugin_data ) {
			$plugin_name = $plugin_data[ 'Name' ];
			$status = '';
	
			if( ! in_array( $slug, $active_plugins ) ) {
				$status = ' <span class="wp-documenter-notice">(Inactive)</span>';
			}
	
			if( is_multisite() ) {
				if( is_plugin_active_for_network( $slug ) ) {
					$status = ' (Network Activated)';
				}
			}
	
			if( isset( $options[ $slug ][ 'description' ] ) ) {
				$value = $options[ $slug ][ 'description' ];
			} else {
				$value = '';
			}
			?>
	
			<label><?php echo $plugin_name; echo $status; ?></label>
			<textarea cols='40' rows='5' name='wpdoc_plugin_descriptions[<?php echo $slug; ?>]'><?php echo $value; ?></textarea>
			<br />
		<?php }
	}

	public function sanitize_input( $input ) {
		$all_installed_plugins = get_plugins();
		$all_installed_plugins_keys = array_keys( $all_installed_plugins );
	
		foreach( $input as $k => $v ) {
			// We don't need to clutter up the option table with blank entries
			if( $v === '' ) {
				continue;
			}
	
			/*
			 * If $k isn't in $all_installed_plugins_keys, someone is being a naughty
			 * monkey and tampering with the form through dev tools.  This
			 * is probably malicious and will cause you to have a bad day, so
			 * let's discard this entry.
			 */
	
			if( ! in_array( $k, $all_installed_plugins_keys ) ) {
				continue;
			}
	
			/*
			 * Bobby Tables might be a good kid, but he taught me to sanitize my 
			 * database inputs.
			 *
			 * @link https://xkcd.com/327/
			 */
	
			/*
			 * Sanitize the slug name in case someone opened dev tools and
			 * put some nasty code in the textarea name field. The check above
			 * should have weeded these out, but just in case.
			 */
	
			$cleaned_k = wp_kses_post( $k );
			$cleaned_v = wp_kses_post( $v );
	
			$newinput[ $cleaned_k ][ 'name' ] = $all_installed_plugins[ $cleaned_k ][ 'Name' ];
			$newinput[ $cleaned_k ][ 'description' ] = $cleaned_v;
		}
	
		return $newinput;
	}

	public function form_output() {
		wp_enqueue_style( 'wpdoc-main-css' );
		wp_enqueue_style( 'wpdoc-pdf-css' );

		settings_errors(); ?>
		<form class="wp-documenter-panel wp-documenter-panel-no-labels" action='options.php' method='post'>
			<h1>WP Documenter - Plugins</h1>
			<p>You can add descriptions for each plugin installed on this site.  Plugins which are inactive are noted below.</p>
			<p>Anything left blank will not output on the PDF.</p>
	
			<?php
			// Set some stuff up.
			settings_fields( 'wpdocPluginsPage' );		// Hidden fields, nonces, and other such mystic sorcery
			do_settings_sections( 'wpdocPluginsPage' );	// Do all the functions added to this options page.
			submit_button(); 							// Does what it says on the tin
			?>
		</form>
		<?php
	}
}