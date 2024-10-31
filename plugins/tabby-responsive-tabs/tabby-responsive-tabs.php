<?php
/*
Plugin Name: Tabby Responsive Tabs
Plugin URI: https://cubecolour.co.uk/tabby-responsive-tabs
Description: Create responsive tabs inside your posts, pages or custom post types by adding simple shortcodes.
Author: cubecolour
Text Domain: tabby-responsive-tabs
Domain Path: /languages/
Version: 1.4.1
Requires at least: 4.9
Requires PHP: 5.6
Author URI: https://cubecolour.co.uk

	Tabby Responsive Tabs WordPress plugin Copyright 2013-2022 Michael Atkins

	Licenced under the GNU GPL:

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

	Original version of Responsive Tabs jQuery script by Pete Love:
	http://www.petelove.co.uk/responsiveTabs/
	http://codepen.io/petelove666/pen/zbLna
	MIT license: http://blog.codepen.io/legal/licensing/

	Permission is hereby granted, free of charge, to any person
	obtaining a copy of this software and associated documentation
	files (the "Software"), to deal in the Software without restriction,
	including without limitation the rights to use, copy, modify,
	merge, publish, distribute, sublicense, and/or sell copies of
	the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall
	be included in all copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
	EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
	OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
	NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
	HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
	WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
	DEALINGS IN THE SOFTWARE.
*/

if ( ! defined( 'ABSPATH' ) ) exit;

function cc_tabby_responsive_tabs_load_textdomain() {
	load_plugin_textdomain( 'tabby-responsive-tabs', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'cc_tabby_responsive_tabs_load_textdomain' );

/**
 * Define Constants
 *
 */
define( 'CC_TABBY_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CC_TABBY_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'CC_TABBY_PLUGIN_VERSION', '1.4.1' );

function cc_tabby_plugin_version(){
	return CC_TABBY_PLUGIN_VERSION;
}

/**
 * Add Links in Plugins Table
 *
 */
add_filter( 'plugin_row_meta', 'cc_tabby_meta_links', 10, 2 );
function cc_tabby_meta_links( $links, $file ) {

	$plugin = plugin_basename(__FILE__);

	//* create the links
	if ( $file == $plugin ) {

		$supporturl = 'https://wordpress.org/support/plugin/tabby-responsive-tabs';
		$donateurl = 'https://cubecolour.co.uk/wp';
		$reviewurl = 'https://wordpress.org/support/view/plugin-reviews/tabby-responsive-tabs?rate';
		$twitterurl = 'https://twitter.com/cubecolour';
		$customiserurl = 'https://cubecolour.co.uk/tabby-responsive-tabs-customiser';
		$adminurl = admin_url( 'options-general.php?page=tabby-settings' );

		$supporttxt = esc_attr__( 'Support', 'tabby-responsive-tabs' );
		$donatetxt = esc_attr__( 'Donate', 'tabby-responsive-tabs' );
		$reviewtxt = esc_attr__( 'Review', 'tabby-responsive-tabs' );
		$twittertxt = esc_attr__( 'Cubecolour on twitter', 'tabby-responsive-tabs' );
		$admintxt = esc_attr__( 'Settings', 'tabby-responsive-tabs' );

		$iconstyle = 'style="-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;"';

		return array_merge( $links, array(
			'<a href="' . $supporturl . '"> <span class="dashicons dashicons-lightbulb" ' . $iconstyle . 'title="' . $supporttxt . '"></span></a>',
			'<a href="' . $twitterurl . '"><span class="dashicons dashicons-twitter" ' . $iconstyle . 'title="' . $twittertxt . '"></span></a>',
			'<a href="' . $reviewurl . '"><span class="dashicons dashicons-star-filled"' . $iconstyle . 'title="' . $reviewtxt . '"></span></a>',
			'<a href="' . $donateurl . '"><span class="dashicons dashicons-heart"' . $iconstyle . 'title="' . $donatetxt . '"></span></a>',
			'<a href="' . $customiserurl . '"><span class="dashicons dashicons-admin-appearance" ' . $iconstyle . 'title="Tabby responsive tabs customiser"></span></a>',
			'<a href="' . $adminurl . '"><span class="dashicons dashicons-admin-generic" ' . $iconstyle . 'title="' . $admintxt . '"></span></a>',
		) );
	}

	return $links;
}

/**
 * Add the admin page if 'tabby responsive tabs customiser' is not active
 *
 */
if ( !function_exists( 'cc_remove_tabby_default_css' ) ) {
	require_once( CC_TABBY_PLUGIN_PATH . 'includes/admin.php' );
}

/**
 * Register & enqueue the stylesheet
 * To use custom styles, copy the content of the tabby.css into your child theme's stylesheet and edit the styles there
 * Prevent the default styles from loading by deselecting the "Include default tab by stylesheet" checkbox in the tabby settings page at settings/tabby
 * Alternatively use the tabby responsive tabs customiser plugin
 * available from from https:cubecolour.co.uk/tabby-responsive-tabs-customiser
 *
 */


//* Register script & styles
function cc_tabby_register_cssjs() {
	wp_register_style( 'tabby', CC_TABBY_PLUGIN_URL . 'css/tabby.css', '', CC_TABBY_PLUGIN_VERSION );
	wp_register_style( 'tabby-print', CC_TABBY_PLUGIN_URL . 'css/tabby-print.css', '', CC_TABBY_PLUGIN_VERSION, 'print' );
	wp_register_script( 'tabby', CC_TABBY_PLUGIN_URL . 'js/tabby.js', array('jquery'), CC_TABBY_PLUGIN_VERSION, true );

	//* inline script added at tabbyending
	$tabbytrigger = "jQuery(document).ready(function($) { RESPONSIVEUI.responsiveTabs(); })";
	wp_add_inline_script( 'tabby', $tabbytrigger );
}
add_action( 'wp_enqueue_scripts', 'cc_tabby_register_cssjs' );


/**
 * Sanitize html element
 *
 */
function cc_sanitize_html_element( $element ){

	$allowed = array( 'h1','h2','h3','h4','h5','h6','p' );

	if (in_array( $element, $allowed) ) {
		return $element;
	} else {
		return 'h2';
	}
}


/**
 * SHORTCODE FOR TABBY
 * use [tabby]
 */
function cc_shortcode_tabby( $atts, $content = null ) {

	//* initialise $firsttab flag so we can tell whether we are building the first tab

	global $reset_firsttab_flag;
	static $firsttab = TRUE;

	if ($GLOBALS["reset_firsttab_flag"] === TRUE) {
		$firsttab = TRUE;
		$GLOBALS["reset_firsttab_flag"] = FALSE;
	}

	$args =  shortcode_atts( array(
		'title'			=> '',
		'open'			=> '',
		'icon'			=> '',
		'ico'			=> '',
		'class'			=> '',
		'required'		=> FALSE,
	), $atts );


	$tabtarget = sanitize_title_with_dashes( remove_accents( wp_kses_decode_entities( $args['title'] ) ) );
	$tab_title_element = cc_sanitize_html_element( get_option( 'cc_tabby_tab_title_element', 'h2' ) );
	$class = sanitize_html_class( $args['class'] );

	//* initialise urltarget
	$urltarget = '';

	//* grab the value of the 'target' url parameter if there is one
	if ( isset ( $_REQUEST['target'] ) ) {
		$urltarget = sanitize_title_with_dashes( $_REQUEST['target'] );
	}

	//* Set Tab Panel Class - add active class if the open attribute is set or the target url parameter matches the dashed version of the tab title
	$tabcontentclass = "tabcontent";

	if ( $class != '' ) {
		$class = ' ' . $class;
		$tabcontentclass .= " " . $class . "-content";
	}

	if ( ( ( $args['open'] ) && ( $urltarget == '' ) ) || ( isset( $urltarget ) && ( $urltarget == $tabtarget ) ) ) {
		$tabcontentclass .= " responsive-tabs__panel--active";
	}

	//* Set the icon style for font awesome
	switch (  esc_html( get_option( 'cc_tabby_fa_icon_style', 'regular' ) ) ) {
		case 'solid':
			$faclass = 'fa-solid';
			break;
		case 'thin':
			$faclass = 'fa-thin';
			break;
		case 'light':
			$faclass = 'fa-light';
			break;
		case 'duotone':
			$faclass = 'fa-duotone';
			break;
		default: //* regular
			$faclass = 'fa';
	}

	//* Add span for icon if icon (font-awesome) or ico (non-autoprefixed) is present
	if ( $args['icon'] ) {
		$addtabicon = '<span class="' . $faclass . ' fa-' . sanitize_html_class( $args['icon'] ) . '"></span>';
	} elseif ( $args['ico'] ) {
		$addtabicon = '<span class="' . sanitize_html_class( $ico ) . '"></span>';
	} else {
		$addtabicon = '';
	}

	//* test whether this is the first tab in the group
	if ( $firsttab ) {

		//* Set flag so we know subsequent tabs are not the first in the tab group
		$firsttab = FALSE;

		//* Build output if we are making the first tab
		return '<div class="responsive-tabs">' . "\n" . '<' . $tab_title_element . ' class="tabtitle' . $class . '">' . $addtabicon . wp_kses( $args['title'], array( 'br' => array(), 'strong' => array(), 'em' => array(), 'i' => array() ) ) . '</' . $tab_title_element . '>' . "\n" . '<div class="' . sanitize_text_field( $tabcontentclass ) . '">' . "\n";
	}

	else {
		//* Build output if we are making a subsequent (non-first tab)
		return  "\n" . '</div><' . $tab_title_element . ' class="tabtitle' . $class . '">' . $addtabicon . wp_kses( $args['title'], array( 'br' => array(), 'strong' => array(), 'em' => array(), 'i' => array() ) ) . '</' . $tab_title_element . '>' . "\n" . '<div class="' . sanitize_text_field( $tabcontentclass ) . '">' . "\n";
	}
}
add_shortcode( 'tabby', 'cc_shortcode_tabby' );


/**
 * SHORTCODE TO BE USED AFTER FINAL TABBY TAB
 * use [tabbyending]
 *
 */
function cc_shortcode_tabbyending( $atts, $content = null ) {

	//* add screen styles, but only if the customiser or a custom styles plugin is not active
	if ( ( !function_exists( 'cc_remove_tabby_default_css' ) ) && ( !function_exists( 'cc_remove_tabby_default_style' ) ) && ( 1 == get_option( 'cc_tabby_default_styles' ) ) ) {
		wp_enqueue_style( 'tabby' );
	}

	//* Add print-only styles
	wp_enqueue_style( 'tabby-print' );

	//* Add script
	wp_enqueue_script( 'tabby' );

	//* action hook to add custom styles etc
	do_action( 'cc_tabby' );

	$GLOBALS["reset_firsttab_flag"] = TRUE;

	return '</div></div>';
}

add_shortcode( 'tabbyending', 'cc_shortcode_tabbyending' );