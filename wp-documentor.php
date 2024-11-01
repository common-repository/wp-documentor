<?php
/*
 * Plugin Name: WP Documenter
 * Plugin URI:	https://gillmer.us
 * Description: Ever start working on a site and wonder to yourself "Why is that plugin installed, what does it do?"  Yeah, me too.  This plugin enumerates the plugins and custom post types on a site, with a description blank for each.  You can then output the descriptions to a PDF to give to your clients so that they know what's installed and why.
 * Author:		Robert Gillmer
 * Author URI:	https://gillmer.us
 * Version:		2.0.0
 * Text Domain: wpdoc
 * Domain Path: languages
 */

// If this file is called directly, then die
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WPDOC_BASE_PATH', plugin_dir_path( __FILE__ ) );
define( 'WPDOC_BASE_URL', plugin_dir_url( __FILE__ ) );

function wpdoc_bootstrap() {
	include_once( WPDOC_BASE_PATH . 'includes/classes/class-wp-documenter.php' );

	$wp_documenter = new \WPDOC\WP_Documenter();
	$wp_documenter->init();
}

add_action( 'admin_menu', 'wpdoc_bootstrap' );

// Activation functions
include_once( WPDOC_BASE_PATH . 'includes/wpdoc-activation.php' );

function load_custom_pdf_template( $original_template ) {
	if( ! current_user_can( 'manage_options' ) ) {
		return $original_template;
	}

	if( ! isset( $_GET[ 'wpdoc-key' ] ) || empty( $_GET[ 'wpdoc-key' ] ) ) {
		return $original_template;
	}
	
	if( ! wp_verify_nonce( $_GET[ 'wpdoc-key' ], 'wpdoc-key' ) ) {
		return $original_template;
	}	

	return WPDOC_BASE_PATH . '/includes/wp-documenter-pdf-source.php';
}

add_filter( 'template_include', 'load_custom_pdf_template' );