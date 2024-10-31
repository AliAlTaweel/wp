<?php

if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Register and enqueue the stylesheet
 *
 */
function cc_tabby_rt_admin_css() {
	wp_register_style( 'cc-tabby-admin-css', CC_TABBY_PLUGIN_URL . 'css/admin.css', '', CC_TABBY_PLUGIN_VERSION );
	wp_enqueue_style( 'cc-tabby-admin-css' );
}
add_action( 'admin_enqueue_scripts', 'cc_tabby_rt_admin_css' );


/**
 * Add Options sub-page
 *
 */
if ( is_admin() ){
	//* add menu & call register settings function
	add_action( 'admin_menu' , 'cc_tabby_menu' );
	add_action( 'admin_init', 'cc_register_tabby_settings' );
}

function cc_tabby_menu() {
	//* create new sub-menu under the Settings top level menu
	$page = add_submenu_page( 'options-general.php', 'Tabby', 'Tabby', 'manage_options', 'tabby-settings', 'cc_tabby_settings_page' );
}


/**
 * Create the Settings
 *
 */
function cc_register_tabby_settings() {

	//* Assign default values
	add_option( 'cc_tabby_default_styles', 1 );
	add_option( 'cc_tabby_tab_title_element', 'h2' );
	add_option( 'cc_tabby_fa_icon_style', 'regular' );

	//* register the settings
	register_setting( 'tabby-settings', 'cc_tabby_default_styles', 'bool' );
	register_setting( 'tabby-settings', 'cc_tabby_tab_title_element' );
	register_setting( 'tabby-settings', 'cc_tabby_fa_icon_style' );
}

function cc_tabby_settings_page() {

	if ( !current_user_can( 'manage_options' ) )	{
		wp_die( esc_attr_e( 'You do not have sufficient permissions to access this page.', 'tabby-responsive-tabs' ) );
	}

	?> <div class="wrap" id="tabby">
		<h2>Tabby responsive tabs</h2>
		<?php /* translators: Developer name: cubecolour */ ?>
		<p><?php printf( __( 'by %1$s', 'tabby-responsive-tabs' ),'<a href="https://cubecolour.co.uk/wp" class="cubecolour">cubecolour</a>' ) ?></p>


<ul class="tabbyoptions">

	<li class="card">
		<h2><?php esc_attr_e( 'Default stylesheet', 'tabby-responsive-tabs' ) ?></h2>
		<p><?php esc_attr_e( 'If you are using a custom set of tabby styles in your child theme or elsewhere, uncheck the checkbox below to remove the default tabby stylesheet.', 'tabby-responsive-tabs' ) ?></p>
		<?php /* translators: Link to the official font awesome plugin on WordPress.org */ ?>
		<p><?php printf( __( 'If you are using the official %1$sfont awesome plugin%2$s to add icon support, you can choose the icon style to be used in tab titles. Only regular and solid icons are available in font awesome free.', 'tabby-responsive-tabs' ),'<a href="https://wordpress.org/plugins/font-awesome/">','</a>' ) ?></p>
		<form method="post" action="options.php">

			<?php settings_fields( 'tabby-settings' ); ?>
			<?php do_settings_sections( 'tabby-settings' ); ?>

			<?php $tab_title_element = esc_html( get_option( 'cc_tabby_tab_title_element', 'h2' ) ); ?>
			<?php $fa_icon_style = esc_html( get_option( 'cc_tabby_fa_icon_style', 'regular' ) ); ?>

			<table class="form-table">
			<tr>
				<td><label for="cc_tabby_default_styles"><?php esc_attr_e( 'Include the default tabby stylesheet:', 'tabby-responsive-tabs' ) ?></label></td>
				<td><input type="checkbox" name="cc_tabby_default_styles" value=1 <?php checked( 1, get_option('cc_tabby_default_styles'), 1 ); ?> /></td>
			</tr>
			<tr>
				<td><label for="cc_tabby_tab_title_element"><?php esc_attr_e( 'Tab title element (default: H2)', 'tabby-responsive-tabs' ) ?></label></td>
				<td><select name="cc_tabby_tab_title_element" id="cc_tabby_tab_title_element">
					<option value="h1"<?php selected( $tab_title_element, 'h1' ); ?>>h1</option>
					<option value="h2"<?php selected( $tab_title_element, 'h2' ); ?>>h2</option>
					<option value="h3"<?php selected( $tab_title_element, 'h3' ); ?>>h3</option>
					<option value="h4"<?php selected( $tab_title_element, 'h4' ); ?>>h4</option>
					<option value="h5"<?php selected( $tab_title_element, 'h5' ); ?>>h5</option>
					<option value="h6"<?php selected( $tab_title_element, 'h6' ); ?>>h6</option>
					<option value="p"<?php selected( $tab_title_element, 'p' ); ?>>p</option>
				</select></td>
			</tr>

			<tr>
				<td><label for="cc_tabby_fa_icon_style"><?php esc_attr_e( 'Font Awesome icon style', 'tabby-responsive-tabs' ) ?></label></td>
				<td><select name="cc_tabby_fa_icon_style" id="cc_tabby_fa_icon_style">
					<option value="regular"<?php selected( $fa_icon_style, 'regular' ); ?>>regular</option>
					<option value="solid"<?php selected( $fa_icon_style, 'solid' ); ?>>solid</option>
					<option value="light"<?php selected( $fa_icon_style, 'light' ); ?>>light</option>
					<option value="thin"<?php selected( $fa_icon_style, 'thin' ); ?>>thin</option>
					<option value="duotone"<?php selected( $fa_icon_style, 'duotone' ); ?>>duotone</option>
				</select></td>
			</tr>
			</table>

			<?php submit_button( $text="Save Changes", $type='primary' ); ?><!-- MAKE TRANSLATABLE -->
		</form>
		<div class="after-settings">
		<p><?php esc_attr_e( 'To customise how your tabs display, either:', 'tabby-responsive-tabs' ); ?></p>
			<ol>
				<?php $tabbycsslink = '<a href="' . CC_TABBY_PLUGIN_URL . 'css/tabby.css" target="_blank">'; ?>
				<?php /* translators: Link to the tabby stylesheet */ ?>
				<li><?php printf( __( 'Uncheck the option above, copy the css rules from the %1$stabby stylesheet%2$s into either your child theme\'s stylesheet or the custom css section of the WordPress customizer, and make any required edits to that copy of the css.', 'tabby-responsive-tabs' ), $tabbycsslink, '</a>' ); ?></li>
				<li><?php esc_attr_e( 'Alternatively the optional \'Tabby responsive tabs customiser\' add-on can be used to customise the tab design without any code editing.', 'tabby-responsive-tabs' ); ?></li>
			</ol>
			<div class="contribute">
				<h2><?php esc_attr_e( 'Thank you!', 'tabby-responsive-tabs' ); ?></h2>
				<?php /* translators: Link to the developer website, link to the reviews page */ ?>
				<p><?php printf( __( 'If this plugin or has provided you with some value, or it has saved you some development time, please consider purchasing an add-on, or making a contribution to %1$sthe developer%2$s, and writing a brief %3$sreview%2$s of the plugin.', 'tabby-responsive-tabs' ),'<a href="https://cubecolour.co.uk/wp/">', '</a>', '<a href="https://en-gb.wordpress.org/plugins/tabby-responsive-tabs/#reviews/">' ); ?></p>
			</div>
		</div>
	</li>

	<li class="card usage">
		<h2><?php esc_attr_e( 'Plugin usage', 'tabby-responsive-tabs' ); ?></h2>
				<?php /* translators: [tabby],[tabbyending] */ ?>
		<p><?php printf( __( 'There are two shortcodes which should both be used, %1$s and %2$s. These are added in the visual or text editor, or within a shortcode block in the block editor.', 'tabby-responsive-tabs' ),'<code>[tabby]</code>','<code>[tabbyending]</code>' ); ?></p>
		<p><?php esc_attr_e( 'The title parameter sets the text on the tab, the content is added between the shortcodes, and the [tabbyending] shortcode is used once per tabgroup and must be placed after the last tab\'s content.', 'tabby-responsive-tabs' ); ?></p>
		<p><?php esc_attr_e( 'It is recommended to leave a blank line between each shortcode and block of content.', 'tabby-responsive-tabs' ); ?></p>
				<?php /* translators: <pre> tags */ ?>
		<p><?php printf( __( 'If you copy & paste the following shortcodes into your visual editor, be sure to delete any %1$s tags surrounding the content.', 'tabby-responsive-tabs' ),'<code>&lt;pre&gt;</code>' ); ?></p>

<pre>

[tabby title="First Tab"]

<?php esc_attr_e( 'This is the content of the first tab.', 'tabby-responsive-tabs' ); ?>


[tabby title="Second Tab"]

<?php esc_attr_e( 'This is the content of the second tab.', 'tabby-responsive-tabs' ); ?>


[tabby title="Third Tab"]

<?php esc_attr_e( 'This is the content of the third tab.', 'tabby-responsive-tabs' ); ?>


[tabbyending]

</pre>
		<?php /* translators: Link to the plugin's readme.txt file, link to the plugin's page on WordPress.org */ ?>
		<p><?php printf( __( 'Please refer to the plugin\'s %1$sreadme file%2$s or the WordPress.org %3$splugin page%4$s for more information.', 'tabby-responsive-tabs' ),'<a href="' . CC_TABBY_PLUGIN_URL . 'readme.txt" target="_blank">', '</a>', '<a href="https://en-gb.wordpress.org/plugins/tabby-responsive-tabs/" target="_blank">', '</a>'); ?></p>
		<?php /* translators: Link to the plugin's support forum on WordPress.org */ ?>
		<p><?php printf( __( 'Plugin support on WordPress.org: %1$stabby responsive tabs support forum%2$s.', 'tabby-responsive-tabs' ),'<a href="https://wordpress.org/support/plugin/tabby-responsive-tabs/" target="_blank">', '</a>' ); ?></p>
	</li>
</ul>
<ul class="tabbycards">
	<li class="card">
		<h2>Tabby responsive tabs customiser</h2>
		<p><?php esc_attr_e( 'An add-on for tabby responsive tabs which allows you to easily customise your tabs.', 'tabby-responsive-tabs' ) ?></p>
		<p><?php esc_attr_e( 'All customisations are made using a simple point & click interface without the need to edit any code.', 'tabby-responsive-tabs' ) ?></p>
		<p><?php esc_attr_e( 'A collection of one-click presets can be used as an easy starting point for further customisation.', 'tabby-responsive-tabs' ) ?></p>
		<p></p>
		<p class="submit">
			<a href="https://cubecolour.co.uk/tabby-responsive-tabs-customiser/" class="button button-primary"><?php esc_attr_e( 'Purchase add-on', 'tabby-responsive-tabs' ) ?></a>
		</p>
	</li>
	<li class="card">
		<h2>Tabby link to tab</h2>
		<p><?php esc_attr_e( 'An add-on for tabby responsive tabs which adds a [tabbylink] shortcode.', 'tabby-responsive-tabs' ) ?></p>
		<p><?php esc_attr_e( 'This shortcode creates a link to a tab on the same page, where selecting the link does not cause the page to reload.', 'tabby-responsive-tabs' ) ?></p>
		<p><?php esc_attr_e( 'This add-on should not be confused with the similarly named \'Tabby tab to url link\' add-on as each one has a different purpose.', 'tabby-responsive-tabs' ) ?></p>
		<p></p>
		<p class="submit">
			<a href="https://cubecolour.co.uk/downloads/tabby-link-to-tab/" class="button button-primary"><?php esc_attr_e( 'Purchase add-on', 'tabby-responsive-tabs' ) ?></a>
		</p>
	</li>
	<li class="card">
		<h2>Tabby tab to url link</h2>
		<p><?php esc_attr_e( 'An add-on for tabby responsive tabs which enables a tab to act as a link to any URL.', 'tabby-responsive-tabs' ) ?></p>
		<p><?php esc_attr_e( 'A tab can be specified within a tabby responsive tabs tabgroup so that it opens a link to any URL.', 'tabby-responsive-tabs' ) ?></p>
		<p><?php esc_attr_e( 'This add-on should not be confused with the similarly named \'Tabby link to tab\' add-on as each one has a different purpose.', 'tabby-responsive-tabs' ) ?></p>
		<p></p>
		<p class="submit">
			<a href="https://cubecolour.co.uk/downloads/tabby-tab-to-url-link/" class="button button-primary"><?php esc_attr_e( 'Purchase add-on', 'tabby-responsive-tabs' ) ?></a>
		</p>
	</li>
	<li class="card">
		<h2>Tabby load accordion closed</h2>
		<p><?php esc_attr_e( 'An add-on for tabby responsive tabs that keeps all sections of the accordion closed on page load.', 'tabby-responsive-tabs' ) ?></p>
		<p><?php esc_attr_e( 'This changes the default behaviour for the accordion so no tab content is shown when the page is initially loaded.', 'tabby-responsive-tabs' ) ?></p>
		<p><?php esc_attr_e( 'The tab display (desktop view) is unaffected.', 'tabby-responsive-tabs' ) ?></p>
		<p></p>
		<p class="submit">
			<a href="https://cubecolour.co.uk/downloads/tabby-load-accordion-closed/" class="button button-primary"><?php esc_attr_e( 'Purchase add-on', 'tabby-responsive-tabs' ) ?></a>
		</p>
	</li>
	<li class="card">
		<h2>Tabby reopen current tab on reload</h2>
		<p><?php esc_attr_e( 'An add-on for tabby responsive tabs to keep the currently active tab open after a reload.', 'tabby-responsive-tabs' ) ?></p>
		<p><?php esc_attr_e( 'This enables the currently active tab in a tabby responsive tabs tabgroup to remain the active (open) tab after the page has been reloaded/refreshed.', 'tabby-responsive-tabs' ) ?></p>
		<p></p>
		<p class="submit">
			<a href="https://cubecolour.co.uk/downloads/tabby-reopen-current-tab-on-reload/" class="button button-primary"><?php esc_attr_e( 'Purchase add-on', 'tabby-responsive-tabs' ) ?></a>
		</p>
	</li>
	<li class="card">
		<h2><?php esc_attr_e( 'Custom styles or functionality', 'tabby-responsive-tabs' ) ?></h2>
		<p><?php esc_attr_e( 'A custom plugin specifically for your site', 'tabby-responsive-tabs' ) ?></p>
		<p><?php esc_attr_e( 'This is a bespoke service to provide a custom-made add-on plugin to extend tabby responsive tabs or load a custom tabs stylesheet created to meet your exact requirements.', 'tabby-responsive-tabs' ) ?></p>
		<p></p>
		<p class="submit">
			<a href="https://cubecolour.co.uk/contact/" class="button button-primary"><?php esc_attr_e( 'Request quote', 'tabby-responsive-tabs' ) ?></a>
		</p>
	</li>
	<li class="card tabbycat"></li>
	<li class="card tabbycat">
		<img src="<?php echo CC_TABBY_PLUGIN_URL . 'images/tabby.png'; ?>">
	</li>
</ul>

<?php
echo '</div>';
}