<?php

/*
 * The "Generate PDF" link outputs to {secret_key}/wpdoc-pdf.  We're going to intercept
 * that request with template_redirect so that we can output the PDF.
 */

function wpdoc_pdf_redirect() {
	$secret_key = get_option( 'wpdoc_secret_key' );
	if ( $_SERVER['REQUEST_URI'] !== '/' . $secret_key . '/wpdoc-pdf' ) {
		return;
	}

	$pdf_settings_options = get_option( 'wpdoc_pdf_settings' );
	$developer_name = isset( $pdf_settings_options[ 'developer_name' ] ) ? $pdf_settings_options[ 'developer_name' ] : '';
	$client_name = isset( $pdf_settings_options[ 'client_name' ] ) ? $pdf_settings_options[ 'client_name' ] : '';
	$introduction = isset( $pdf_settings_options[ 'introduction' ] ) ? $pdf_settings_options[ 'introduction' ] : '';
	$plugin_descriptions = get_option( 'wpdoc_plugin_descriptions' );
	$cpt_descriptions = get_option( 'wpdoc_cpt_descriptions' );
	$page_template_descriptions = get_option( 'wpdoc_page_template_descriptions' );

	$site_url_with_protocol = get_bloginfo( 'url' );
	$site_url = str_replace( 'https://', '', $site_url_with_protocol );
	$site_url = str_replace( 'http://', '', $site_url_with_protocol );

	$filename = 'Documentation for ' . $site_url . '.pdf';

	$pdf_stylesheet_link = plugin_dir_url( __FILE__ ) . 'assets/css/wpdoc-pdf.css';

	ob_start(); ?>
	<head>
	<link rel="stylesheet" type="text/css" href="<?php echo $pdf_stylesheet_link; ?>">
	</head>
	<body>
		<?php if( $client_name || $developer_name || $introduction ) { ?>
			<div class="wpdoc-first-section-wrapper">

				<?php if( $client_name ) { ?>
					<h1>Website Documentation for <?php echo $client_name; ?></h1>
				<?php } else { ?>
					<h1>Website Documentation</h1>
				<?php } 

				if( $developer_name ) { ?>
					<small>Prepared by <?php echo $developer_name; ?></small>
				<?php }

				if( $introduction ) {
					echo apply_filters( 'the_content', $introduction );
				} ?>
			</div>
		<?php } // Endif ?>

		<?php if( isset( $plugin_descriptions ) && ! empty( $plugin_descriptions ) ) {
			if( is_array( $plugin_descriptions ) ) { // It should always be an array, but just in case... ?>
				<div class="wpdoc-section-wrapper">
					<h2>Plugin Descriptions</h2>
					<?php foreach( $plugin_descriptions as $plugin ) { ?>
						<strong><?php echo $plugin[ 'name' ]; ?></strong><br />
						<p class="plugin-description"><?php echo $plugin[ 'description' ]; ?></p>
					<?php } ?>
				</div> <?php
			}
		}

		if( isset( $cpt_descriptions ) && ! empty( $cpt_descriptions ) ) {
			if( is_array( $cpt_descriptions ) ) { // It should always be an array, but just in case... ?>
				<div class="wpdoc-section-wrapper">
					<h2>Custom Post Type Descriptions</h2>
					<?php foreach( $cpt_descriptions as $cpt ) { ?>
						<strong><?php echo $cpt[ 'name' ]; ?></strong><br />
						<p class="cpt-description"><?php echo $cpt[ 'description' ]; ?></p>
					<?php } ?>
				</div> <?php
			}
		}

		if( isset( $page_template_descriptions ) && ! empty( $page_template_descriptions ) ) {
			if( is_array( $page_template_descriptions ) ) { // It should always be an array, but just in case... ?>
				<div class="wpdoc-section-wrapper">
					<h2>Custom Post Type Descriptions</h2>
					<?php foreach( $page_template_descriptions as $page_template ) { ?>
						<strong><?php echo $page_template[ 'name' ]; ?></strong><br />
						<p class="page-template-description"><?php echo $page_template[ 'description' ]; ?></p>
					<?php } ?>
				</div> <?php
			}
		}

		 ?>
	</body><?php
	$contents = ob_get_clean();
	header('Content-Type: application/pdf', true, 200);
	// header('Content-Length: '.strlen( $dompdf ));
	header('Content-Disposition: inline; filename="' . $filename . '"');
	$dompdf = new DOMPDF();
	$dompdf->load_html($contents);
	$dompdf->set_paper('A4', 'portrait');
	$dompdf->render();
	$dompdf->stream( $filename );
	exit();
}

add_action( 'template_redirect', 'wpdoc_pdf_redirect' );