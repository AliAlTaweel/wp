<?php

if ( ! defined( 'ABSPATH' ) ) exit();

/**
 * Remove the Options used by the plugin
 *
 */
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) exit();

cc_remove_tabby_settings();

function cc_remove_tabby_settings() {
	delete_option( 'cc_tabby_default_styles' );
	delete_option( 'cc_tabby_tab_title_element' );
	delete_option( 'cc_tabby_fa_icon_style' );
}