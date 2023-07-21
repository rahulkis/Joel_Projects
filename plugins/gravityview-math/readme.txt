=== GravityMath ===
Tags: gravityview, math, calculation, gravity forms
Requires at least: 4.4
Tested up to: 6.2
Requires PHP: 7.2.0
Contributors: The GravityKit Team
License: GPLv2 or later

Calculations. Uses the [Hoa Math](https://github.com/hoaproject/Math) library.

== Installation ==

1. Upload plugin files to your plugins folder, or install using WordPress' built-in Add New Plugin installer
2. Activate the plugin
3. Follow the instructions on [the GravityMath documentation page](https://docs.gravitykit.com/category/794-gravitymath)

== Changelog ==

= 2.3.3 on May 30, 2023 =

* Fixed: Incorrect calculation results for Survey fields (Likert type) with multiple rows that use score values
* Fixed: Calculation for Product fields with "user-defined price" not working when using the "filter" parameter in the [[gravitymath] shortcode](https://docs.gravitykit.com/article/295-math-shortcode)
* Fixed: Incompatibility with some plugins/themes that use Laravel components
* Updated: [Foundation](https://www.gravitykit.com/foundation/) to version 1.0.12

__Developer Updates:__

* Added: `gk/gravitymath/scope/form/transform-meta-value` filter to modify the SQL statement used to transform the meta value for complex fields such as multi-row Survey (Likert) or Product with user-defined price
* Added: `gk/gravitymath/table-footer-calculation/supported-fields-schema` filter to modify the list of View fields that support table footer calculations

= 2.3.2 on February 20, 2023 =

**Note: GravityMath now requires PHP 7.2 or newer**

* Updated: [Foundation](https://www.gravitykit.com/foundation/) to version 1.0.9

= 2.3.1 on January 30, 2023 =

* Fixed: Changing View settings or filters would not affect cached calculations
* Fixed: Calculations would be cached for Views connected to the same form
* Fixed: Pricing field calculations may not be accurate for values over 999 when using `view` and `visible` scopes

= 2.3 on January 5, 2023 =

* Updated: [Foundation](https://www.gravitykit.com/foundation/) to version 1.0.8

= 2.2 on December 21, 2022 =

* Added: Support for nesting shortcodes! Perform calculations inside calcuations using multiple forms ([read more about nested calculations](https://docs.gravitykit.com/article/900-nested-calcuations))
    * Added: `[gravitymath2]` and `[gravitymath3]` shortcodes for use inside a `[gravitymath]` shortcode
* Fixed: Multiple filters, separated by `&` in the shortcode `filter` attribute, were not working correctly
* Cache improvements:
    * Improved cache speed by returning object cache before checking GravityView cache (if GravityView is active)
    * Don't use cache when a form has been submitted on the page or if `?nocache`, `?cache=0`, or `?gform_debug` are in the URL
    * Fixed cache showing the wrong value when using the `:count` modifier in certain cases
* Fixed: Fatal error on some hosts due to a conflict with one of the plugin dependencies (psr/log)
* Fixed: PHP 8.1 notices

__Developer Updates:__

* Added: `gk/gravitymath/use_db_cache` filter to modify whether or not to use the database cache if GravityView is enabled. Default is `true`.

= 2.1.4 on December 15, 2022 =

* Fixed: Count operation not working for multiple input fields (e.g., First Name [1.3])
* Fixed: Multiple PHP 8.x warnings
* Fixed: Fatal error on some hosts that use weak security keys and salts

= 2.1.3 on December 1, 2022 =

* Fixed: It was not possible to remove an expired license key

= 2.1.2 on November 29, 2022 =

* Fixed: "Undefined index" PHP notice
* Fixed: Product quantity footer calculation was being formatted as currency
* Fixed: Footer calculations were added in the Single Entry layout

= 2.1.1 on November 14, 2022 =

* Fixed: Fatal error when loading plugin translations
* Fixed: Slow loading times on some hosts
* Fixed: Plugin failing to install on some hosts

= 2.1.0.3 on October 31, 2022 =

* Fixed: Plugin was not appearing in the "Add-Ons" section of the Gravity Forms System Status page

= 2.1.0.2 on October 20, 2022 =

* Fixed: Potential error when the plugin tries to log an unsuccessful operation

= 2.1.0.1 on October 19, 2022 =

* Fixed: Error when trying to activate license keys

= 2.1 on October 19, 2022 =

* Added: New WordPress admin menu where you can now centrally manage all your GravityKit product licenses and settings ([learn more about the new GravityKit menu](https://www.gravitykit.com/foundation/))
    - Go to the WordPress sidebar and check out the GravityKit menu!
    - We have automatically migrated your existing GravityMath license, which was previously entered in the Gravity Forms settings page
    - Request support using the "Grant Support Access" menu item
* Added: A new `[gravitymath]` shortcode—it works exactly the same as `[gv_math]`, but it's named properly
* Fixed: PHP 8 warnings

= 2.0.6 on June 21, 2022 =

* [GravityView (the company) is now GravityKit](https://www.gravitykit.com/rebrand/) and this plugin is now called GravityMath!
* Added: Support for product field with "Radio Buttons", "Drop Down" and "User Defined Price" field types

__Developer Updates:__

**IMPORTANT: `GravityView_Math_*` classes were renamed to `GravityMath_*` and future versions will see `gravityview/math/*` hooks renamed to `gk/gravitymath/*`**

= 2.0.5 on February 21, 2022 =

* Fixed: "Trying to get property of non-object" notice when displaying a View with footer calculations
* Fixed: Incorrect calculation of the average Quiz Score
* Added: Option to calculate a sum of the Quiz Score

= 2.0.4 on October 25, 2021  =

* Fixed: Filters using CONTAINS or NOTCONTAINS operators would not work
* Fixed: Filters would not work with multi-input fields unless the exact input is specified

= 2.0.3 on June 9, 2021 =

* Fixed: Fatal error if performing calculations using Gravity Forms data, using the shortcode's `filter` attribute, and GravityView is not installed
* Fixed: Filter conditions using fields with multiple input did not work

= 2.0.2 on May 13, 2021 =

* Fixed: Incorrect formatting of calculation results with certain locales
* Added: Option to specify the number of decimal places for footer calculations

= 2.0.1 on March 8, 2021 =

* Fixed: PHP fatal error when the plugin is used without GravityView
* Added: Footer calculations for Custom Content fields

= 2.0 on February 25, 2021 =

* Added: Easily add footer calculations to your GravityView Table and DataTables layouts! [Learn how to add calculations](https://docs.gravityview.co/article/750-how-to-add-field-calculations-to-the-table-footer).
    - Effortlessly calculate field values
    - Quiz: # Passed, # Failed, % Passed, % Failed, Average Score
    - High, low, average, sum, checked
    - Count of checked and unchecked checkboxes, radio inputs, consent fields
    - Time duration: Fastest, slowest, average time
* Added: Perform duration calculations, great for races and time sheets! [See a video for setting up duration calculations](https://docs.gravityview.co/article/756-gravity-forms-duration-calculations).
* Added: Filter support for `scope="visible"`. [Read more about filters](https://docs.gravityview.co/article/295-math-shortcode#filters).
* Added: An `{entry_count}` Merge Tag you can use inside math formula as well as throughout Gravity Forms and GravityView! [Read about the `{entry_count}` Merge Tag](https://docs.gravityview.co/article/754-entry-count-merge-tag)
* Improved: Execution time when a Math filter is used on large data sets
* Fixed: Incorrect results returned by `min`/`max`/`avg`/`sum` operations
* Fixed: Calculations on non-numeric fields (e.g., product price) would not work in View widget areas
* Fixed: Only using one page of results were used when calculating `scope="view"`
* Logging improvements!
    * New: Not sure why your Math formula isn't working, but you don't want to modify the shortcode? Administrators can now debug Math formulae from the front-end by adding `?gv_math_debug=true` to the URL.
    * Added: Better debugging information is now shown, including the contents of the formula before and after it was processed.
    * Improved: Notices are now grouped for each shortcode, making it easy to debug.
    * Fixed: "Additional info" shown when debugging results not displaying when clicked.
    * Fixed: Math by GravityView wasn't showing in the [Gravity Forms "Logging" screen](https://docs.gravityforms.com/logging-and-debugging/#viewing-logs).
    * Fixed: Display user notices only when `notices` shortcode attribute is set.

= 1.3.2 on April 6, 2020 =

* Fixed: If using `scope=form` as well as a filter, only the first 20 entries in the form were used in calculations

= 1.3.1 on March 5, 2020 =

* Fixed: Shortcode not working when used inside GravityView DataTables layouts
* Updated: French translation

= 1.3 on January 29, 2020 =

* Fixed: The `[gvmath]` shortcode not working properly inside GravityView `[gvlogic]` shortcodes
* Fixed: Potential errors when editing content in Gutenberg editor
* Fixed: Potential error when using Math while running Gravity Forms < 2.3 and GravityView < 2.5
* Fixed: Properly handles dates passed via Merge Tags
* Added: Support for Gravity Forms Merge Tags in the `filter` attribute (when using `scope="form"`)

_Developer Updates:_

* Added: `gravityview/math/debug` filter to modify whether debugging is on or off. Return `false` to disable debugging. Note: Viewing debugging requires `edit_others_posts` capability.

= 1.2 on May 10, 2019 =

* **The plugin now requires PHP 7.1**
* Added: Filtering on `form` and `view` scopes. This will filter the entries in the scope by the specified values. [Read more about filtering](https://docs.gravityview.co/article/295-math-shortcode#scope-filtering).
* Fixed: `SQRT_PI`, `SQRT_{Number}` functionality
* Added: Russian Translation (thanks, Viktor S) and Ukrainian translation (thanks, Dariusz Zielonka!)
* Updated: Chinese translation (thanks, Edi Weigh!)

= 1.1.1 on May 8, 2018 =

* Fixed: Aggregate form data not calculating with Gravity Forms 2.3
* Fixed: `.mo` translation files weren't being generated
* Updated: Dutch, Turkish, and Spanish translations (thanks, jplobaton, SilverXp, and suhakaralar!)

= 1.1 on April 28, 2018 =

* Fixed: Compatibility with Gravity Forms 2.3
* Updated: Dutch and Turkish translations

= 1.0.3 on May 24, 2017 =

* Fixed: Don't link to entry in debug mode if the entry doesn't exist 👻
* Fixed: Incorrect argument passed to Gravity Forms Add-On registration function
* Fixed: Compatibility issue with the (excellent) [Gravity Forms Utility plugin](https://gravityplus.pro/gravity-forms-utility/) - thanks, Naomi C Bush!

= 1.0.2 on December 15 =

* Fixed PHP error when there are no values to calculate
* Updated German translation (Thank you, Hubert Test!)

= 1.0.1 on September 14 =

* Fix potential error blocking activation

= 1.0 =

* Launch!


= 1686600410-5513 =