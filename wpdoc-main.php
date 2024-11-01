<?php
/*
 * Plugin Name: WP Documentor
 * Plugin URI:	https://www.robertgillmer.com
 * Description: Ever start working on a site and wonder to yourself "Why is that plugin 
 * installed, what does it do?"  Yeah, me too.  This plugin enumerates the plugins
 * and custom post types on a site, with a description blank for each.  You can then output
 * the descriptions to a PDF to give to your clients, so they know what's installed and why.
 * Author:		Robert Gillmer
 * Author URI:	http://www.robertgillmer.com
 * Version:		1.1.0
 * Text Domain: wpdoc
 * Domain Path: languages
 */

// If this file is called directly, then die
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Activation functions
include_once( __DIR__ . '/wpdoc-activation.php' );

// Styles and scripts
include_once( __DIR__ . '/wpdoc-scripts.php' );

// Options page
include_once( __DIR__ . '/wpdoc-options-page-main.php' );

// DOM script
include_once( __DIR__ . '/assets/dompdf/dompdf_config.inc.php' );

// PDF redirect and DOM loader
include_once( __DIR__ . '/wpdoc-pdf-generator.php' );
