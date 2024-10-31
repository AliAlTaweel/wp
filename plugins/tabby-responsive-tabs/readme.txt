=== Tabby Responsive Tabs ===
Contributors: numeeja
Donate link: https://cubecolour.co.uk/wp
Tags: tabs, tab, responsive, accordion, shortcode, ClassicPress
Tested up to: 6.5.2
Stable tag: 1.4.1
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
License of javascript:

Create responsive tabs inside your posts, pages or custom post content by adding simple shortcodes inside the post editor.

== Description ==

* Adds a set of horizontal tabs which changes to an accordion on narrow viewports
* Tabs and accordion are created with jQuery
* Supports multiple sets of tabs on same page
* Uses semantic header and content markup
* Aria attributes and roles aid screen reader accessibility
* Tabs and content are accessible via keyboard

The Tabby responsive tabs plugin is designed to be an easy and lightweight way to add responsive tabs to your content. Experienced developers should be able to easily customize how the tabs display on their site by replacing the built-in CSS rules with an edited version (see note below for more details of this).

= Optional Add-ons =
> The [Tabby responsive tabs customiser](https://cubecolour.co.uk/tabby-responsive-tabs-customiser/ "Tabby responsive tabs customiser") add-on adds a settings panel with several parameters to provide the easiest way to customise the display of your tabs without editing any code. You can use the default tabby styles or one of the included one-click presets as a starting point for customisation. It also enables you to easily add icons to your tab titles.

> The [Tabby link to tab](https://cubecolour.co.uk/downloads/tabby-link-to-tab/ "Tabby link to tab") add-on provides a simple shortcode to create links to specific tabs which can appear anywhere on the same page as the tabgroup without the page reloading.

> The [Tabby tab to URL link](https://cubecolour.co.uk/downloads/tabby-tab-to-url-link/ "Tabby tab to URL link") add-on enables you to set one or more of your tabs to act as a link to any URL.

> The [Tabby load accordion closed](https://cubecolour.co.uk/downloads/tabby-load-accordion-closed/ "Tabby load accordion closed") add-on changes the default behaviour when the tabs are displayed as an accordion so that no accordion sections are open when the page initially loads.

> The [Tabby reopen current tab on reload](https://cubecolour.co.uk/downloads/tabby-reopen-current-tab-on-reload/ "Tabby reopen current tab on reload") add-on enables the currently active tab to remain the active (open) tab after the page has been reloaded/refreshed.

= Usage: =

There are two shortcodes used to create the tab group: `[tabby]` and `[tabbyending]` both must be used as below to create a tab group.

To start a new tab use a `[tabby]` shortcode, eg:

`[tabby title="tabname"]`

*replace tabname with the name of your tab.*

Add the tab content after the shortcode.

Add a `[tabbyending]` shortcode after the content of the last tab in a tabgroup.

= Example =
*If you copy & paste this example into your own page instead of typing them, ensure that you delete any stray &lt;code&gt; or &lt;pre&gt; tags that might have appeared.*

`

[tabby title="First Tab"]


This is the content of the first tab.


[tabby title="Second Tab"]


This is the content of the second tab. This is the content of the second tab.


[tabby title="Third Tab"]


This is the content of the third tab. This is the content of the third tab. This is the content of the third tab.


[tabbyending]

`

*note: To prevent stray paragraph tags being introduced by WordPress's wpautop filter, ensure that there is a blank line above and below each tabby shortcode and the tabbyending shortcode.*

You can see the tabs on the [demo page](https://cubecolour.co.uk/tabby-responsive-tabs/ "Tabby Responsive Tabs demo").

You can add the shortcodes to a page made using the WordPress block editor by using WordPress's shortcode block.

If you want to change how the tabs and accordion display on your site, you have two options:

1. Use the [Tabby Responsive Tabs Customiser](https://cubecolour.co.uk/tabby-responsive-tabs-customiser/ "Tabby Responsive Tabs Customiser") plugin which provides a very easy way to customise the display of your tabs without needing to edit any code.

2. Copy the contents of the plugin's stylesheet into your child theme or custom styles plugin and make the changes to that copy as required. If you do this you will also need to prevent the built-in styles from loading by going to the admin page at settings => tabby and unchecking the "Include the default tabby stylesheet" checkbox.

= Additional Shortcode attributes =

**Open**

The first (leftmost) tab panel will be open by default in 'tab view' and in 'accordion view'.

If you want a specific tab other than the first tab to be open by default when the page first loads, you can add the parameter & value **open="yes"** to the shortcode for that tab:

`
[tabby title="My Tab" open="yes"]
`

If you use the 'open' shortcode parameter in one of your tab shortcodes, ensure that you only add it to single tab as having more than one tab open within a tab group is not supported.

**Icon**

The markup required to show an icon alongside a tab title can be added by using the **'icon'** attribute. Tabby responsive tabs does not add the icon files, you will also need to use a theme or plugin (such as the tabby responsive tabs customiser add-on) to add the icon files:
`

[tabby title="My Tab" icon="cog"]

`
This adds a pseudo element before the tab title with the classes "fa" and "fa-cog". Other icon font sets can be used if you ensure the CSS rules target the classes added by the plugin.

The [Tabby Responsive Tabs Customiser](https://cubecolour.co.uk/tabby-responsive-tabs-customiser/ "Tabby Responsive Tabs Customiser") plugin can be used to add the Font Awesome files required to display the icons in the tab titles.

**Class**

This allows a custom class to be added to each tab and tab content area. The class added to the tab will be the value of the class parameter and the class of the tab content area associated with that tab will be the class with the '-content' suffix.

= Controlling which tab is open when linking to the page =
You can use a 'target' URL parameter in your link to set which tab will be open when the page initially loads. The value of this parameter is based on the tab title specified in the tabby shortcode which built the tab, but formatted with punctuation & special characters removed, accents removed, and with dashes replacing the spaces.

If you want to link to a 'contacts' page with a tab titled 'Phone Numbers' open, the url you use to link to this page would look like:
`
yoursite.com/contact/?target=phone-numbers

`


If you want a tab with the title 'email addresses' to be open, the url would look like:
`
yoursite.com/contact/?target=email-addresses
`
If you want a tab with the title 'entr&eacute;es' to be open (with an acute accent over the second e), the url would look like:
`
yoursite.com/contact/?target=entrees
`
Using a target url parameter will override any open shortcode parameters used.

== Installation ==

1. Upload the Tabby Responsive Tabs plugin folder to your '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Why isn't it working for me? =

There are a few things you can investigate when troubleshooting any plugin which is not working as expected:

**Incorrectly formed shortcodes**
If you copied &amp; pasted in the Tabby Responsive Tabs shortcodes from a web page showing an example usage rather than directly typing them into the page, it is possible that there may be invisible or invalid characters in the shortcode text, or the shortcodes are enclosed within code tags. Correct this by deleting the shortcodes and type them directly instead.

**Plugin or theme conflicts**
To troubleshoot whether a plugin or theme is conflicting with the Tabby Responsive Tabs plugin on your site, install the [health check and troubleshooting plugin](https://wordpress.org/plugins/health-check/ "health check and troubleshooting plugin") and use the troubleshooting mode.

*If the plugin isn't working for you*, please read the documentation carefully to check whether your issue is covered. Then review the topics in the [plugin support forum](https://wordpress.org/support/plugin/tabby-responsive-tabs/ "Tabby Responsive Tabs plugin support forum"). You may find an appropriate solution outlined in a resolved topic if someone previously posted the same or a similar issue. If you do not find an answer that enables you to solve your issue, please post a new topic on the forum so we have an opportunity to get it working before you consider leaving a review.

= What levels of support are available? =
You can receive free support for the plugin if you have problems getting it working. To access this please open a new topic on the [plugin support forum](https://wordpress.org/support/plugin/tabby-responsive-tabs/ "Tabby Responsive Tabs plugin support forum") all communication must take place on the forum for free support.

Please note that CSS support for customization of the tab display is not within the scope of the free support that can be provided.

If you require a greater level of support than can be provided on the plugin support forum on WordPress.org - eg you prefer not to post the url, or if you require CSS support to change how your tabs appear on your site, you can request non-free support via the [paid email support form for cubecolour plugins](https://cubecolour.co.uk/premium-support/ "paid email support form for cubecolour plugins") form.

= How can I remove extra paragraph tags which appear at the beginning or end of the tab content? =
These extra tags are often be added by WordPress's wpautop function. It is recommended to leave a blank line before and after each tabby shortcode to prevent these from appearing.

= Pasted-in shortcodes aren't working or the tabs have a 'stepped' appearance =
If you are copying & pasting the example shortcodes into the visual editor and the shortcodes don't seem to be working or the tabs appear in a stepped configuration, look at the page in the text editor to be sure that you aren't adding in any extra markup that isn't visible in the visual editor. Delete any opening and closing &lt;pre&gt; and/or &lt;code&gt; tags pairs surrounding the tab shortcodes. (this would apply to any plugin using shortcodes).

= I am using custom styles in my theme, how do I ensure the default tabby stylesheet is not loaded? =
In your your WordPress admin, go to settings -> tabby and uncheck the "Include the default tabby stylesheet" checkbox.

= Where is the plugin's admin page? =
The admin page is at settings => tabby.

If you want to be able to customise the tabs without editing code, the [Tabby Responsive Tabs Customiser plugin](https://cubecolour.co.uk/tabby-responsive-tabs-customiser/ "Tabby Responsive Tabs Customiser plugin") is available, and provides options to customise how the tabs display.

= Does it work with a multisite installation of WordPress? =
Yes

= How can I change the colours? =
The recommended method for experienced developers to customise how the tabs display is to copy the css rules from the plugin's stylesheet into the child theme's stylesheet and then customise the colours and other CSS as required. When using customised version of the plugin's styles in the child theme, you should also prevent the plugin's default built-in styles from loading by going to settings -> tabby in your WordPress admin, and uncheck the "Include the default tabby stylesheet" checkbox.

If you prefer to use a settings page in your WordPress admin to set a custom tab style, you can use the [Tabby Responsive Tabs Customiser plugin](https://cubecolour.co.uk/tabby-responsive-tabs-customiser/ "Tabby Responsive Tabs Customiser plugin") which contains several tab style presets which can be further customised with a comprehensive set of easy to set options. The customiser plugin was designed to be easy for non-developers to use to customise how the tabs display.

= Can I change the responsive breakpoint from the default 767px? =
Yes, you can see where that is set in the plugin's CSS. Refer to the answer above about using custom css to use a custom value.

This value can also be set using the Tabby Responsive Tabs Customiser plugin's admin panel.


= How can I get rid of unmatched opening or closing paragraph tags in the tabs making my markup invalid? =
This can be caused by WordPress's wpautop filter being applied to the post/page content. To prevent stray paragraph tags appearing, leave a blank line before and after each shortcode.

= Can I display multiple tab groups on a single page? =
Yes you can have as many sets of tabs as you like.

= Can I include tabs in my sidebar? =
It is possible to include tabs within a text widget if you have added shortcode support to text widgets by adding the filter below to your child theme's functions.php or a custom functionality plugin.

`
add_filter('widget_text', 'do_shortcode');
`
This filter will enable you to use any shortcodes within text widgets.


= Can I nest a Tag Group within an existing tab? =
No, this is not supported.

= Can I specify which tab is open when the page initially loads? =
Yes, see the documentation for the 'open' shortcode parameter for details.

= Can I specify which tab is open from a link pointing to the page =
Yes, see the documentation for the usage of a 'target' URL parameter in the link.

= I want to use a custom class for my icons without the font-awesome prefixes =
You can do this by using an 'ico' parameter instead of the 'icon' parameter in the shortcode.

= Can you create a customised stylesheet for me to fit in with the colours or style of my website? =
Site-specific customisation work is beyond of the scope of the free support. You can style your tabs to match your theme using the optional [Tabby Responsive Tabs Customiser](https://cubecolour.co.uk/tabby-responsive-tabs-customiser/ "Tabby Responsive Tabs Customiser") add-on plugin or you can commission a custom plugin which provides custom tab styes specific to your site. This is a paid service, for a quote please send a message via the [cubecolour contact form](https://cubecolour.co.uk/contact/ "cubecolour contact form")

Plugin support for Tabby Responsive Tabs is provided at the [plugin's support forum](https://wordpress.org/support/plugin/tabby-responsive-tabs/ "Tabby Responsive Tabs plugin support forum") on WordPress.org.

= Why can't I get the Target Parameter to Work? =
This is used just like any other URL parameter in a query string so you need to use a valid structure for the query string.

If there's already a parameter in a query string, including the one included in the url when not using 'pretty' permalinks, subsequent parameters must be appended using an ampersand.
eg:

`
yoursite.com/?page_id=1&target=phone-numbers
`

= How can I use the target parameter on a link on the same page as the tabgroup without the page reloading?
This is not possible with the target parameter, however this can be achieved by using the optional [Tabby link to tab plugin](https://cubecolour.co.uk/downloads/tabby-link-to-tab/ "Tabby Link to Tab plugin")

= How will the tabs print? =
Basic print styles are included. This is designed to print the tab titles and content in series.

= Are there any other free cubecolour plugins? =
If you like Tabby Responsive Tabs, you may like some of my other plugins in the WordPress.org plugins directory. These are listed on my [profile](https://profiles.wordpress.org/numeeja/ "cubecolour profile") page under the 'plugins' tab.

= Who or what is cubecolour? =
My name is Michael Atkins. Cubecolour is the name of my web design and development business in South London where I work with businesses, organisations and individuals to build and support their websites using WordPress. I enjoy attending local WordCamps and WordPress meetups. I have used WordPress since 2007 and I am a moderator on the WordPress.org support forums. When I'm not sitting in front of my MacBook I can usually be found playing bass, guitar or ukulele.

= Why do you spell the word 'color' incorrectly? =
I don't, I'm from England and 'colour' is the correct spelling.

= I am using the plugin and love it, how can I show my appreciation? =
You can donate any amount via [my donation page](https://cubecolour.co.uk/wp/ "cubecolour donation page") or you could purchase a copy of the [Tabby responsive tabs customiser plugin](https://cubecolour.co.uk/tabby-responsive-tabs-customiser/ "Tabby responsive tabs customiser plugin").

If you find Tabby responsive tabs useful, I would also appreciate a review on the [plugin review page](https://wordpress.org/support/view/plugin-reviews/tabby-responsive-tabs/ "Tabby responsive tabs plugin reviews")

= Is the Tabby responsive tabs customiser plugin a Premium or Pro version of Tabby responsive tabs? =
No, Tabby responsive tabs works great on its own and customising how the tabs display should be straightforward for anyone comfortable with editing a child theme. The Tabby Responsive Tabs Customiser plugin is an add-on which is designed to be useful for anyone who wants an easy way to customise how their tabs display without touching any code.

= What is the Tabby link to tab plugin? =
Tabby link to tab is an optional add-on for Tabby responsive tabs which provides a simple shortcode to create links to specific tabs which can appear anywhere on the same page as the tabgroup. When this is used, the tab becomes active without the page reloading. This add-on is not required in most cases but can be useful if you want to include links to specific tabs within the tab content or in a different area of the page.

For more details please see: [Tabby link to tab plugin](https://cubecolour.co.uk/downloads/tabby-link-to-tab/ "Tabby link to tab plugin"). This add-on was developed after several users requested the functionality.

== Screenshots ==

1. On a desktop browser the content is displayed within tabs.
2. When the browser width is below the size set in the media query, the tabs are replaced by an accordion.
3. The basic print styles are intended to present the tab titles & content appropriately when printed out.

== Changelog ==

= 1.4.1 =
* New setting to specify the font awesome icon style

= 1.4.0 =
* Added localization and a few translations

= 1.3.4 =
* tab titles sanitized with `wp_kses()` to allow line breaks in tab titles
* Sanitize html for tab title element

= 1.3.3 =
* Added option to change the tab title element

= 1.3.1 =
* Fixed issue with the option value saving
* using target url parameter overrides open shortcode parameter for which tab is to be open on page load
* Shortcode parameters now used in array, not extracted
* Priority of tabbytrigger can be changed by adding 'priority' shortcode parameter with an appropriate value
* Priority of tabbytrigger changed to 30 by default to prevent breakage on pages created with gutenberg
* ico shortcode parameter added to enable custom icons to be added without adding font-awesome prefixes

= 1.3.0 =
* using target url parameter overrides open shortcode parameter for which tab is to be open on page load
* Shortcode parameters now used in array, not extracted
* Priority of tabbytrigger can be changed by adding 'priority' shortcode parameter with an appropriate value
* Priority of tabbytrigger changed to 30 by default to prevent breakage on pages created with gutenberg
* ico shortcode parameter added to enable custom icons to be added without adding font-awesome prefixes

= 1.2.3 =
* Enable targeting the tab from url query string when the title contains an accent

= 1.2.2 =
* Included print stylesheet as a separate file

= 1.2.1 =
* Added index.php to prevent the content of plugin directories being viewed if the site has not had directory browsing disallowed.

= 1.2.0 =
* Added basic print styles to default stylesheet

= 1.1.1 =
* Improvements to default CSS
* Addition of 'open' shortcode attribute to allow tabs other than the first to be open when the page loads
* First tab now is open by default when displayed as accordion
* Changed links in plugin table
* Get Plugin Version function
* Prevent tabs overlapping if there are too many
* Remove hard coded paragraph tags in tab content & improve
* Added icon font support. Note: Font Awesome needs to be loaded by your theme or another plugin (including the Tabby Responsive Tabs Customiser)
* Added functionality to allow target url parameter to control which tab is open on page load.

= 1.0.3 =
* improved theme compatibility with default css

= 1.0.2 =
* enqueue plugin js only when needed
* css for improved specificity

= 1.0.1 =
* Updated js & css

= 1.0.0 =
* Initial Version

== Upgrade Notice ==

= 1.4.1 =
* New setting to specify the font awesome icon style

= 1.4.0 =
* Added localization and various translations

= 1.3.4 =
* tab titles sanitized with `wp_kses()` to allow line breaks in tab titles
* Sanitize html for tab title element

= 1.3.3 =
* Added option to change the tab title element markup

= 1.3.1 =
* Fixed issue with the option value saving

= 1.3.0 =
* using target url parameter overrides open shortcode parameter for which tab is to be open on page load
* Shortcode parameters now used in array, not extracted
* Priority of tabbytrigger can be changed by adding 'priority' shortcode parameter with an appropriate value
* Priority of tabbytrigger changed to 30 by default to prevent breakage on pages created with gutenberg
* ico shortcode parameter added to enable custom icons to be added without adding font-awesome prefixes

= 1.2.3 =
* Enable targeting the tab from url query string when the title contains an accent

= 1.2.2 =
* Included print stylesheet as a separate file

= 1.2.1 =
* Added index.php to prevent the content of plugin directories being viewed if the site has not had directory browsing disallowed.

= 1.2.0 =
* Added print styles to default stylesheet

= 1.1.1 =
* Added Support for Tabby Responsive Tabs Customiser add-on
* Further improved theme compatibility with default css
* Control which tab is open on page load using short code parameter or url parameter
* Font Awesome icon support in tabs

= 1.0.3 =
* improved theme compatibility with default css

= 1.0.2 =
* improved efficiency - enqueue plugin only when needed
* improved theme compatibility with default css

= 1.0.1 =
* Updated js & css

= 1.0.0 =
* Initial Version