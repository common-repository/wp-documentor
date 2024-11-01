<?php

namespace WPDOC;

class WP_Documenter_CPTs_Page implements \WPDOC\WP_Documenter_Page_Interface {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'menu_page' ), 12 );
		add_action( 'admin_init', array( $this, 'settings_init' ) );
	}

	public function menu_page() { 
		add_submenu_page(
			'wp-documenter',						// Slug of the parent
			'Custom Post Type Descriptions',		// Page title (tab name)
			'Custom Post Type Descriptions',		// Menu title (WP sidebar menu)
			'manage_options',						// Cap needed
			'wp-documenter-cpts',					// Page slug
			array( $this ,'form_output' )			// Callback function which outputs the form
		);
	}

	public function settings_init() {
		register_setting(
			'wpdocCPTsPage', 						// Option group
			'wpdoc_cpt_descriptions',				// Option name
			array( $this, 'sanitize_input' )		// Sanitization callback
		);
	
		add_settings_section(
			'wpdoc_cpt_page_fields',				// Section ID
			'',										// Section title. Not needed, handled by wpdoc_add_cpts_submenu()
			'',										// Section callback. Not needed, handled by wpdoc_cpts_form_output()
			'wpdocCPTsPage'							// Option group
		);
	
		add_settings_field(
			'wpdoc_cpt_description_output',			// Field ID
			'',										// Field title
			array( $this, 'description_render' ), 	// Callback function to generate field's HTML
			'wpdocCPTsPage',						// Option group which this field should appear in
			'wpdoc_cpt_page_fields'					// ID of the section this should be in
		);
	}

	public function description_render() {
		$options = get_option( 'wpdoc_cpt_descriptions' );
		$built_in_cpts_objects = get_post_types(
			array(
				'_builtin'	=> true,
			),
			'objects'
		);
		$built_in_cpts_slugs = array_keys( $built_in_cpts_objects );
	
		$all_public_cpts = get_post_types(
			array(
				'public'	=> true,
			),
			'objects'
		);
	
		foreach( $all_public_cpts as $slug => $cpt_data ) {
			$label = $cpt_data->label;
			$status = '';
	
			if( in_array( $slug, $built_in_cpts_slugs ) ) {
				$status = ' <span class="wp-documenter-notice">(Built-In)</span>';
			}
	
			if( isset( $options[ $slug ][ 'description' ] ) ) {
				$value = $options[ $slug ][ 'description' ];
			} else {
				$value = '';
			}
			?>
	
			<label><?php echo $label; echo $status; ?></label>
			<textarea cols='40' rows='5' name='wpdoc_cpt_descriptions[<?php echo $slug; ?>]'><?php echo $value; ?></textarea>
			<br />
		<?php }
	}

	public function sanitize_input( $input ) {
		$all_public_cpts = get_post_types(
			array(
				'public'	=> true,
			),
			'objects'
		);
		$all_public_cpts_keys = array_keys( $all_public_cpts );
	
		foreach( $input as $k => $v ) {
			// We don't need to clutter up the option table with blank entries
			if( $v === '' ) {
				continue;
			}
	
			/*
			 * If $k isn't in $all_public_cpts_keys, someone is being a naughty
			 * monkey and tampering with the form through dev tools.  This
			 * is probably malicious and will cause you to have a bad day, so
			 * let's discard this entry.
			 */
	
			if( ! in_array( $k, $all_public_cpts_keys ) ) {
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
	
			$newinput[ $cleaned_k ][ 'name' ] = $all_public_cpts[ $cleaned_k ]->label;
			$newinput[ $cleaned_k ][ 'description' ] = $cleaned_v;
		}
	
		return $newinput;
	}

	public function form_output() {
		wp_enqueue_style( 'wpdoc-main-css' );
		wp_enqueue_style( 'wpdoc-pdf-css' );

		settings_errors(); ?>
		<form class="wp-documenter-panel wp-documenter-panel-no-labels"  action='options.php' method='post'>
			<h1>WP Documenter - Custom Post Types</h1>
			<p>You can add descriptions for the custom post types which have been created for this site.  You can also add descriptions for the built-in WordPress post types Posts, Pages, and Media.</p>
			<p>Anything left blank will not output on the PDF.</p>
	
			<?php
			// Set some stuff up.
			settings_fields( 'wpdocCPTsPage' );			// Hidden fields, nonces, and other such mystic sorcery
			do_settings_sections( 'wpdocCPTsPage' );	// Do all the functions added to this options page.
			submit_button(); 							// Does what it says on the tin
			?>
		</form>
		<?php
	}
}