<?php

namespace WPDOC;

class WP_Documenter_Settings_Page implements \WPDOC\WP_Documenter_Page_Interface {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'menu_page' ), 12 );
		add_action( 'admin_init', array( $this, 'settings_init' ) );
	}

	public function menu_page() { 
		add_submenu_page(
			'wp-documenter',								// Slug of the parent
			'Print Settings',								// Page title (tab name)
			'Print Settings',								// Menu title (WP sidebar menu)
			'manage_options',								// Cap needed
			'wp-documenter-pdf-settings',					// Page slug
			array( $this ,'form_output' )					// Callback function which outputs the form
		);
	}

	public function settings_init() {
		register_setting(
			'wpdocPDFSettingsPage', 						// Option group
			'wpdoc_pdf_settings',							// Option name
			array( $this, 'sanitize_input' )				// Sanitization callback
		);
	
		add_settings_section(
			'wpdoc_pdf_settings_fields',					// Section ID
			'',												// Section title. Not needed, handled by wpdoc_add_pdf_settings_submenu()
			'',												// Section callback. Not needed, handled by wpdoc_pdf_settings_form_output()
			'wpdocPDFSettingsPage'							// Option group
		);
	
		add_settings_field(
			'wpdoc_pdf_settings_dev_name',					// Field ID
			'What is your name or company name?',			// Field title
			array( $this, 'dev_name_render' ),				// Callback function to generate field's HTML
			'wpdocPDFSettingsPage',							// Option group which this field should appear in
			'wpdoc_pdf_settings_fields'						// ID of the section this should be in
		);
	
		add_settings_field(
			'wpdoc_pdf_settings_user_name',					// Field ID
			'What is your client\'s name or company name?',	// Field title
			array( $this, 'user_name_render' ), 			// Callback function to generate field's HTML
			'wpdocPDFSettingsPage',							// Option group which this field should appear in
			'wpdoc_pdf_settings_fields'						// ID of the section this should be in
		);
	
		add_settings_field(
			'wpdoc_pdf_settings_description',				// Field ID
			'Do you have an introduction you\'d like to display at the top of the PDF, before the documentation?', // Field title
			array( $this, 'introduction_render' ),			// Callback function to generate field's HTML
			'wpdocPDFSettingsPage',							// Option group which this field should appear in
			'wpdoc_pdf_settings_fields'						// ID of the section this should be in
		);
	}

	public function dev_name_render() {
		$options = get_option( 'wpdoc_pdf_settings' );
		if( isset( $options[ 'developer_name' ] ) ) {
			$value = $options[ 'developer_name' ];
		} else {
			$value = '';
		} ?>
		<input type='text' name='wpdoc_pdf_settings[developer_name]' value='<?php echo $value; ?>'>
		<?php
	}

	public function user_name_render() {
		$options = get_option( 'wpdoc_pdf_settings' );
		if( isset( $options[ 'client_name' ] ) ) {
			$value = $options[ 'client_name' ];
		} else {
			$value = '';
		} ?>
		<input type='text' name='wpdoc_pdf_settings[client_name]' value='<?php echo $value; ?>'>
		<?php
	}

	public function introduction_render() {
		$options = get_option( 'wpdoc_pdf_settings' );
		if( isset( $options[ 'introduction' ] ) ) {
			$value = $options[ 'introduction' ];
		} else {
			$value = '';
		} ?>
		<textarea cols='40' rows='5' type='text' name='wpdoc_pdf_settings[introduction]'><?php echo $value; ?></textarea>
		<?php
	}

	public function sanitize_input( $input ) {
		$options = get_option( 'wpdoc_pdf_settings' );

		foreach( $input as $k => $v ) {
			// We don't need to clutter up the option table with blank entries
			if( $v === '' ) {
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
			 * put some nasty code in the textarea name field.
			 */
	
			$cleaned_k = wp_kses_post( $k );
			$cleaned_v = wp_kses_post( $v );
	
			$newinput[ $cleaned_k ] = $cleaned_v;
		}
	
		return $newinput;
	}

	public function form_output() {
		wp_enqueue_style( 'wpdoc-main-css' );
		wp_enqueue_style( 'wpdoc-pdf-css' );
		wp_enqueue_script( 'json-form' );

		settings_errors();
		?>
		<form class="wp-documenter-panel" action='options.php' method='post'>
			<h1>WP Documenter - PDF Settings</h1>
			<p>You can set the defaults for your PDF below.</p>
	
			<?php
			// Set some stuff up.
			settings_fields( 'wpdocPDFSettingsPage' );			// Hidden fields, nonces, and other such mystic sorcery
			do_settings_sections( 'wpdocPDFSettingsPage' );		// Do all the functions added to this options page.
			submit_button(); 									// Does what it says on the tin
			?>
		</form>
		<br /><br />
		<p>Click here to generate a PDF of your documentation.  Please note - be sure to save any changes on this page before clicking this button.</p>
		<?php
			/*
			 * Javascript/AJAX/DomPDF won't save a PDF on button-click because security.  We're going to open
			 * a new tab, do a security check to make sure nonces match, then display the PDF there for the user
			 * to download manually.  To do this, I'm going to build a nonce into the link so that we can
			 * $_GET it from the new tab.
			 */

			$pdf_nonce = wp_create_nonce( 'wpdoc-key' )?>
			<a class="button-primary" href="/wpdoc-pdf?wpdoc-key=<?php echo esc_attr( $pdf_nonce ); ?>" target="_blank">Generate PDF</a>
		<?php
	}
}