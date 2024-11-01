<?php

function wpdoc_activation() {
	add_option( 'wpdoc_plugin_descriptions', array(), '', false );
	add_option( 'wpdoc_cpt_descriptions', array(), '', false );
	add_option( 'wpdoc_page_template_descriptions', array(), '', false );
	add_option( 'wpdoc_pdf_settings', array(), '', false );
}

register_activation_hook( WPDOC_BASE_PATH . 'wp-documenter.php', 'wpdoc_activation' );