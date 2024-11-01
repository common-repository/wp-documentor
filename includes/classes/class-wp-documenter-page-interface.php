<?php

namespace WPDOC;

interface WP_Documenter_Page_Interface {
	public function menu_page();
	public function settings_init();
	public function sanitize_input( $input );
	public function form_output();
}