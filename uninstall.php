<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

delete_option( 'wpdoc_cpt_descriptions' );
delete_option( 'wpdoc_page_template_descriptions' );
delete_option( 'wpdoc_plugin_descriptions' );
delete_option( 'wpdoc_pdf_settings' );