=== GravityView - Social Sharing ===
Tags: gravityview
Requires at least: 3.3
Tested up to: 6.2
Stable tag: trunk
Contributors: gravitykit
License: GPL 3 or higher

Improve SEO and add support for social sharing links to Views or entries.

== Installation ==

1. Upload plugin files to your plugins folder, or install using WordPress' built-in Add New Plugin installer
2. Activate the plugin
3. Follow the instructions

== Changelog ==

= 3.3.1 on April 19, 2023 =

* Fixed: Fatal error on a single entry when using Jetpack Sharing

= 3.3 on April 5, 2023 =

* Fixed: Single Entry Settings (such as SEO Title) were not being applied for when the View was embedded in a page (requires GravityView 2.17.2)

__Developer Updates__

* Modified: Added a third `$view` (`\GV\View`) parameter to `gravityview_social_get_title()`

= 3.2 on February 20, 2023 =

* Fixed: PHP 8 deprecation notices

= 3.1 on November 21, 2022 =

* Improved: Sharing links now include text for screen readers
* Improved: Added icon in the GravityView "Add Field" picker
* Fixed: Appearance of sharing buttons on the frontend
* Fixed: Scripts being loaded on every page in the WordPress dashboard
* Fixed: Removed usage of a deprecated GravityView filter

= 3.0 on August 10, 2020 =

**Now requires Yoast SEO 14.1 or newer**

* Added: Content sharing using WordPress 5.4 social icons
    * Removed support for JP Sharing and ShareThis plugins, since they are no longer developed
* Fixed: Integration with Yoast SEO
* Fixed: Sharing single entries from the Multiple Entries screen
* Fixed: Update details would be shown for GravityView core releases instead of this plugin

= 2.0 on June 1, 2018 =

* Fixed: Compatibility with Yoast 7.0 - Access Single Entry settings by clicking on the plug icon in Yoast SEO
* Fixed: Sharing field not working in GravityView 2.0
* Fixed: Sharing links for Multiple Entries shows link to a single entry with GravityView 2.0
* Process shortcodes in all Yoast SEO fields, allowing use of `[gvlogic]` to display different values based on whether showing a single entry or multiple entries screen. [Learn more](https://docs.gravityview.co/article/252-gvlogic-shortcode#context)
* Allow Merge Tags to be processed in all Yoast SEO fields
* Updated: Plugin updater library
* Now requires GravityView 2.0 or newer
* Now requires Yoast SEO 7.0 or newer

= 1.0.2 on June 2 =
* Fixed: Sharing links linking to entries when View is embedded in a post, page, or attachment

= 1.0.1 on June 1 =
* Fixed: Display the sharing buttons even if a View's "Show sharing buttons" setting is unchecked (Jetpack and JP Sharing)
* Tweak: Improved loading order of plugin files
* No longer in beta!

= 1.0 beta on April 7 =
* Fixed: Fatal Error when going to an entry URL and there was no entry retrieved
* Fixed: PHP errors in the Admin when creating a new Post

= 0.2 on March 24 =
* Make Jetpack Open Graph work properly
* Fixed: Set single entry title
* Fixed: Infinite loop filter issue (requires 1.7.3)
* Added: `gravityview_social_get_title()` function to get the right title for the sharing functions
* Changed: Allow Jetpack global checkbox in View
* Fixed: Don't hide Jetpack sharing for non-Views

= 0.1 =
* Launch


= 1682448864-5513 =