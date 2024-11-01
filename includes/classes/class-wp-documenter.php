<?php

namespace WPDOC;

class WP_Documenter {
	
	// This space left intentionally blank
	public function __construct() {
	}
	
	public function init() {
		add_action( 'admin_enqueue_scripts', array( $this, 'register_assets' ) );
		add_filter( 'generate_rewrite_rules', array( $this, 'rewrite_rules_for_pdfs' ) );

		$this->load_dependencies();
		$this->instantiate_main();
		$this->instantiate_settings();
		$this->instantiate_plugins();
		$this->instantiate_cpts();
		$this->instantiate_templates();
	}

	private function load_dependencies() {
		include_once( WPDOC_BASE_PATH . 'includes/classes/class-wp-documenter-page-interface.php' );
		include_once( WPDOC_BASE_PATH . 'includes/classes/class-wp-documenter-main-page.php' );
		include_once( WPDOC_BASE_PATH . 'includes/classes/class-wp-documenter-settings-page.php' );
		include_once( WPDOC_BASE_PATH . 'includes/classes/class-wp-documenter-plugins-page.php' );
		include_once( WPDOC_BASE_PATH . 'includes/classes/class-wp-documenter-cpts-page.php' );
		include_once( WPDOC_BASE_PATH . 'includes/classes/class-wp-documenter-templates-page.php' );
	}

	private function instantiate_main() {
		$wpdoc_main = new \WPDOC\WP_Documenter_Main_Page();
	}
	
	private function instantiate_settings() {
		$wpdoc_settings = new \WPDOC\WP_Documenter_Settings_Page();
	}
	
	private function instantiate_plugins() {
		$wpdoc_plugins = new \WPDOC\WP_Documenter_Plugins_Page();
	}

	private function instantiate_cpts() {
		$wpdoc_cpts = new \WPDOC\WP_Documenter_CPTs_Page();
	}

	private function instantiate_templates() {
		$wpdoc_templates = new \WPDOC\WP_Documenter_Templates_Page();
	}

	public function register_assets() {
		wp_register_style( 'wpdoc-main-css', WPDOC_BASE_URL . 'includes/css/wpdoc-main.css' );
		wp_register_style( 'wpdoc-pdf-css', WPDOC_BASE_URL . 'includes/css/wpdoc-pdf.css' );
	}
}