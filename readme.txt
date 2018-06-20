=== Schema ===
Contributors: hishaman, schemapress
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=NGVUBT2QXN7YL
Tags: schema, schema.org, json, json-ld, google, seo, structured data, markup, search engine, search, rich snippets, breadcrumbs, social, post, page, plugin, wordpress, content, article, news, search results, site name, knowledge graph, social, social profiles, keywords, meta-tags, metadata, tags, categories, optimize, ranking, search engine optimization, search engines, serp, sitelinks, google sitelinks, sitelinks search box, google sitelinks search box, semantic, structured, canonical, custom post types, post type, title, terms, media, images, thumb, featured, url, video, video markup, video object, VideoObject, video schema, audio object, AudioObject, audio schema, audio, sameAs, about, contact, amp, mobile, taxonomy
Requires at least: 4.0
Tested up to: 4.9.6
Requires PHP: 5.4
Stable tag: 1.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Get the next generation of Schema Structured Data to enhance your WordPress site presentation in Google search results.

== Description ==

Like Schema plugin? Consider leaving a [5 star review](https://wordpress.org/support/plugin/schema/reviews/).

Super fast, light-weight plugin for adding schema.org structured data markup in recommended JSON-LD format automatically to WordPress sites.

Enhanced Presentation in Search Results By including structured data appropriate to your content, your site can enhance its search results and presentation.

Check out the [Plugin Homepage](https://schema.press/) for more info and [documentation](https://schema.press/docs/).


**What is Schema markup?**

Schema markup is code (semantic vocabulary) that you put on your website to help the search engines return more informative results for users. So, Schema is not just for SEO reasons, it’s also for the benefit of the searcher. 

**Schema Key Features**

* Easy to use, set it and forget it, with minimal settings. 
* Enable Schema types at once per post type or post category.
* Valid markup, test it in Google Structured Data Testing Tool.
* Output JSON-LD format, the most recommended by Google.
* Reuse data saved in post meta, which is created by other plugins.
* Extensible, means you can extend its functionality via other plugins, extensions or within your Theme’s functions.php file.

**Plugin Extensions**

* [Schema Review](https://wordpress.org/plugins/schema-review/): Extend Schema functionality by adding review and rating Structured Data functionality for Editors and Authors.
* [Schema Default Image](https://wordpress.org/plugins/schema-default-image/): Add ability to set a default WordPress Featured image for schema.org markup.

**Supported Google/Schema Markups**

* [Knowledge Graph](https://developers.google.com/structured-data/customize/overview)
 * [Logos](https://developers.google.com/structured-data/customize/logos)
 * [Company Contact Numbers](https://developers.google.com/structured-data/customize/contact-points)
 * [Social Profile Links](https://developers.google.com/structured-data/customize/social-profiles)

* Style Your Search Results:
 * [Enable Sitelinks Search Box](https://developers.google.com/structured-data/customize/logos)
 * [Show Your Site Name in Search](https://developers.google.com/structured-data/site-name)

**Supported Schema Types**
 
* Creative Work
 * [Article](https://schema.org/Article) enabled on Pages
  * [BlogPosting](https://schema.org/BlogPosting) enabled on Posts
  * [NewsArticle](https://schema.org/NewsArticle)
  * [Report](https://schema.org/Report)
  * [ScholarlyArticle](https://schema.org/ScholarlyArticle)
  * [TechArticle](https://schema.org/TechArticle)

* [Blog](https://schema.org/Blog) to markup Blog posts list page.
* [WPHeader](https://schema.org/WPHeader) to markup Web Page Header.
* [WPFooter](https://schema.org/WPFooter) to markup Web Page Footer.
* [BreadcrumbList](https://schema.org/BreadcrumbList) to markup Breadcrumbs.
* [CollectionPage](https://schema.org/CollectionPage) to markup Categories Archives.
* [CollectionPage](https://schema.org/CollectionPage) to markup Tags Archives.
* [ItemList](https://schema.org/ItemList) to markup Post Type Archives.
* [AboutPage](https://schema.org/AboutPage) to markup the About page.
* [ContactPage](https://schema.org/ContactPage) to markup the Contact page.
* [Person](https://schema.org/Person) enabled on Author pages
* [VideoObject](https://schema.org/VideoObject) enable automatically on all videos embedded with oEmbed. Supports VideoPress, YouTube, TED, Vimeo, Dailymotion, and Vine.
* [AudioObject](https://schema.org/AudioObject) enable automatically on all audio embedded with oEmbed. Supports SoundCloud, and Mixcloud.

**Supported Plugins**

Schema plugin integrates, and play nicely with (not necessarily a full integration):

 * Yoast SEO
 * AMP plugin
 * WPRichSnippets
 * The SEO Framework
 * Visual Composer
 * ThirstyAffiliates
 * WooCommerce
 * Easy Digital Downloads (EDD)

**Supported Themes**

 * Genesis 2.x 
 * Thesis 2.x
 * Divi

**Developers?**

Feel free to [fork the project on GitHub](https://github.com/schemapress/Schema) and submit your contributions via pull request.

== Installation ==

1. Upload the entire `schema` folder to the `/wp-content/plugins/` directory
2. DO NOT change the name of the `schema` folder
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Navigate to the `Schema > Settings` menu to configure the plugin
5. If you cache your site, make sure to clear cache after configuring the plugin settings.

== Frequently Asked Questions ==

= The plugin isn't working or have a bug? =

Post detailed information about the issue in the [support forum](https://wordpress.org/support/plugin/schema) and we will work to fix it.

= Is there any Documentation for this plugin? =

Indeed, detailed information about the plugin can be found on the [documentation section](https://schema.press/docs/) on our website.

= Are you going to add support for new schema.org types in the future? =

Nope! The Schema plugin is meant to add markup to a basic WordPress installation, including those types (mostly Article, BlogPosting) which needs to be supported on a fresh install. 

Other schema.org types shall be added via [Plugin Extensions](https://schema.press/downloads/), or a custom code.


= Is there a way to add a new schema.org type? =

Luckily… Yes! Schema plugin has a filter which can be used to [add support for new schema.org types](https://schema.press/docs/adding-support-new-schema-org-types/).

= Knowledge Graph is not showing? =

The plugin meant to validate markup in Google Structured Data Testing Tool, we don’t have control over the actual display of Knowledge Graph.

= I see an error in Google Structure Data Testing Tool =

This could be for one -or more- of the following reasons:

 * Image error: This is a missing WordPress Featured image, try to upload a Featured image.
 * Logo error: This is a missing Publisher logo, it can be set in the plugin settings page, Schema > Settings > General > Publisher Logo.

P.S. You may encounter errors for sites hosted locally, for accessibility reasons.

= Compatible with Yoast SEO? =

Yes, Schema plugin will detect Yoast SEO plugin and override its output on the front page of your site, this means all Knowledge Graph and Site Search output will be generated by Schema. However, the plugin settings gives you control over which plugin should output Knowledge Graph markup, Search Results, and Breadcrumbs.

= Compatible with AMP plugin? =

Yes, Schema plugin will detect AMP plugin and output a more complete and valid schema.org markup.

== Screenshots ==
1. Plugin Settings page.
2. Create new schema type screen.
3. Create post meta fields with Post Meta Box generator feature.
4. Google Structured Data Testing Tool.
5. Configuration Wizard.

== Changelog ==

= 1.7 =
* Fixed some typos and wording in the plugin settings.
* Enhanced plugin settings usability by adding functionality for tooltips.
* Enhanced plugin settings by show/hide settings based on selection. 
* Enhanced plugin settings by showing Schema plugin version in settings page title.
* Added check if Genesis Framework functions exists before unhook site Header and Footer.
* Added new step by step settings configuration setup wizard.
* Added new settings for Site type.
* Moved Social Profiles to its own sub tab in plugin settings.
* Moved Yoast SEO setting under Advanced settings tab.
* Updated the Welcome page, added link to configuration wizard.
* Updated readme.txt file.

= 1.6.9.8.2 =
* Fixed typos when return false in a couple of functions.
* Fixed fatal errors caused due duplicate function names with other plugin in post meta.
* Fixed ImageObject output in the About and Contact pages.
* Fixed the About admin sub menu item url, it was not pointing correctly in some cases.
* Fixed duplicate schema output in some cases when Sitelinks Search Box is enabled. 
* Removed the Auto Featured Image feature, it was causing several issues.
* Removed class-settings.php file, which was not used in the plugin.
* Reverted all changes made in version 1.6.9.8 back, since issue has been solved!
* Updated Chosen script and CSS to version 1.8.5 for post meta fields.
* Updated the plugin welcome page.
* Updated readme.txt file to include GPL license details.

= 1.6.9.8.1 =
* Reverted back all changes made in version 1.6.9.8, since it breaks!

= 1.6.9.8 =
* Fixed reset post query in post type enabled function.
* Fixed headlines and names, make sure to remove and clean HTML tags.
* Fixed front-end styles and scripts is not needed, commented the function for now.
* Enhanced the Knowledge Graph functionality by adding a new field for contact URL.
* Added new feature, support for WPHeader and WPFooter markup.
* Added new feature, support for ItemList markup on post types archive pages.
* Added new admin page for plugin Extensions.
* Added new function schema_wp_get_archive_link to get archive page link.
* Added new function schema_wp_get_categories_as_keywords to be used by WPHeader.
* Added new function to get blog posts page URL.
* Added new integration and fix for Easy Digital Downloads (EDD) plugin.
* Updated the readme.txt and README.md files and pumped the tested WP version to 4.9
* Code cleanup.

= 1.6.9.7 =
* Fix for Sitelinks Search Box markup output, echo the value instead of returning it.

= 1.6.9.6 =
* Fixed video object, removed a check for variable that always returns false.
* Fixed Organization markup output, it was not working. 
* Fixed error, make sure PHP-XML extension is installed before parsing page HTML.
* Fixed the query on home page, the blog posts page.
* Fixed PHP notice when enabled types returns a string instead of an array.
* Modified some wording and corrected typos across the plugin.
* Added post id to the media function which is responsible for pulling images.
* Added organization and author ids so Google can identify multiple.
* Added post id to schema_wp_get_ref in schema_wp_get_type function.
* Added new filter for overriding post type in Schema > Types screen.
* Added new filter schema_author_output for overriding author markup output.
* Added new filter sitelinks_search_box for overriding Sitelinks Search Box.
* Added new function to get current post type.
* Updated README.md file.

= 1.6.9.5 =
* Fixed markup errors on AMP pages, now markup is pulled correctly form Schema.
* Fixed an error due to conflict with previously declared function in another plugin.
* Fixed CSS style used to hide the taxonomy add new link, target only schema post type.
* Fixed Warning raised by delete_term_meta when deleting tags.
* Fixed breadcrumbs, disabled breadcrumbs on WooCommerce to avoid duplication.
* Fixed meta tax styles for sameAs input.
* Fixed Blog posts page markup, create own loop with WP_Query to avoid conflicts.
* Added new function schema_wp_get_type to get schema type by post id.
* Added new function schema_wp_get_ref to get schema reference by post id.
* Added two new filters to allow disable default sameAs feature.
* Added a new filter to disable breadcrumbs.
* Added new feature, support for schema.org markup on tags archives pages.
* Added new features, support for sameAs markup on tag archive pages.
* Updated readme.txt file.

= 1.6.9.4 =
* Fixed bug in AMP plugin integration, function was called too early.
* Fixed bug in post meta input field types when object post type is not set.
* Fixed broken link in Contextual Help with the plugin settings pages.
* Fixed bad requests happened in the backend for broken links.
* Fixed PHP notice for undefined variable: results, in schema ref.
* Fixed PHP notice in post meta Text input field when $meta has an array.
* Added support for schema.org markup for taxonomy archive pages.
* Added support for breadcrumbs json-ld, added new settings for it under Content tab.
* Enhanced integration of Yoast SEO plugin, remove breadcrumb markup output automatically.
* Enhanced integration of Genesis theme, remove breadcrumb markup output automatically.
* Enhanced category archives markup, reduced 4 database queries, so now it is faster.
* Enhanced Article schema output, only output if Article or sub types is chosen.
* Enhanced post meta generator, added activation filters to generator and meta box.
* Enhanced the display of post types list in Schema Types edit page.
* Enhanced post meta save function, remove check for permissions on save.
* Enhanced description by giving it its own function.
* Updated readme.txt file, modified the plugin details and extended the FAQ section.

= 1.6.9.3 =
* Fixed a bug in Yoast SEO integration while checking if plugin is active.
* Fixed a warning showing when original post status is not set.
* Fixed a warning when use the Quick Edit screen, could not retrieve post type.
* Fixed input field styles in post meta.
* Added new filter schema_wp_filter_description_word_count for description words count.
* Added new property for post meta called class, to allow styling and targeting inputs.
* Added new custom Bootstrap 4 styles to be used by extensions.
* Extended readme.txt file FAQ section with more details about the plugin.
* Pumped tested version to 4.8.2

= 1.6.9.2 =
* Fixed post meta fields array, it was not defined properly.
* Fixed minifying admin css file.
* Fixed a bug in the Knowledge Graph markup output with other plugins.
* Tweak modified admin menus to allow ordering admin sub menus by priority. 
* Added new filter schema_wp_types_post_meta_fields to allow adding fields to post meta.
* Added new post meta field type for checkbox group inline.
* Added better way to output Knowledge Graph markup, make it filterable. 

= 1.6.9.1 =
* Fixed a notice when WP Rich Snippets plugin is active on a post.
* Fixed post meta class issue, scripts was not loading properly for some types.
* Fixed post meta slider field and added range min property for better presentation.
* Tweak repeated row fields styles, removed the extra li height from post meta CSS. 
* Tweak remove repeated fields icon link hover, force mouse cursor to pointer.
* Tweak remove functions from deprecated-functions file.
* Added new post meta sanitizers, santitize_title_with_dashes and sanitize_html_class.
* Added new alert when deleting repeated fields to enhance user experience. 
* Added new function and filter to admin backend schema_wp_get_post_types.
* Added new integration for ThirstyAffiliates, prevent thirstylink type from showing.
* Added the required PHP version 5.4 in readme.txt file.

= 1.6.9 =
* Fixed using JSON_UNESCAPED_UNICODE of PHP 5.4 or later.
* Added new function schema_wp_get_currency_symbol to misc functions file.
* Moved function schema_wp_get_currencies to misc file so it can be used globally.
* Deprecate schema_wp_get_currency function, never been used.
* Update: Bumped minimum required PHP version from 5.3 to 5.4 

= 1.6.8 =
* Fixed googleplus key in user profile meta.
* Fixed Quick Edit links removed on all post types, this should be limited only to schema.

= 1.6.7 =
* Added new class for admin post list columns, not Schema type has new columns.
* Added new columns to Schema post type, example Schema Type, Post Type, and Content.
* Removed the View and Quick Edit links from actions column.
* Updated custom post meta class, added required and default options for fields.
* Updated a few wording in the Types section in backend.
* Updated custom post meta boxes CSS, set select input field width to auto.
* Cleaned some code and removed code comments.
* Tested with PHP version 7.0.22

= 1.6.6 =
* Fixed logo guidelines link in plugin settings.
* Fixed site url by using get_home_url instead of get_site_url.
* Fixed a few notices in about, contact, category, and 404 pages.
* Fixed a Trying to get property of non-object warning when saving ref.
* Fixed unwanted field id was showing in the repeated post meta generator.
* Updated to version 1.6.13 of license handler for EDD.
* Updated to version 1.6.11 of EDD updater class. 

= 1.6.5 =
* Fixed empty array output on front page when set Yoast SEO output to true.
* Cleaned comments in code files and corrected a couple of typos.

= 1.6.4 =
* Removed Yoast SEO plugin check, now it is done via the plugin settings.
* Added new settings checkbox will show when Yoast SEO plugin is active.
* Added Person markup, now a site can be defined as an Organization or a Person.  
* Added missing @id to Website markup.
* Added missing @id to Organization markup.
* Updated the welcome page.
* Cleaned and enhanced wording in settings and comments in code files.
* Modified tested up to version to 4.7
* Modified requires at least version to 4.0
* Modified readme.txt file.

= 1.6.3 =
* Fixed an error in schema markup caused by misspelling articleSection.

= 1.6.2 =
* Fixed fatal error when previewing or activating non-Genesis themes.
* Fixed fatal error when calling get_current_screen in admin pages.
* Fixes gravatar cached response problem.
* Modified readme.txt file.

= 1.6.1 =
* Fixed hide VideoObject and AudioObject meta boxes if not enabled in settings.
* Added new hook schema_wp_do_after_settings_updated.
* Added new function schema_wp_json_delete_cache to flush cached json-ld post meta.
* Added flush cached json-ld post meta whenever plugin settings got updated.

= 1.6 =
* Fixed exclude post was not working properly.
* Fixed cached post meta timestamp should be deleted on post save.
* Fixed Schema post type label value.
* Fixed Schema post types was not created on plugin activation.
* Fixed schema reference post meta was not saved for scheduled posts.
* Enhanced gravatar validation function.
* Enhanced Blog markup performance by pulling data from cached post meta.
* Added missing BlogPosting description in Blog page markup.
* Added sameAs markup to BlogPosting property in Blog page markup.
* Added sameAs markup to About page.
* Added sameAs markup to Contact page.
* Added new filter schema_blog_output to allow dev extend markups.
* Added new filter schema_about_page_output to allow dev extend markups.
* Added new filter schema_contact_page_output to allow dev extend markups.
* Added missing post meta to the plugin uninstall function.
* Added new function for recursive array search to admin functions file.
* Modified readme.txt file.

= 1.5.9.9 =
* Fixed category id on category pages.
* Fixed a notice on VideoObject function.
* Fixed Schema JSON-LD not updating by flushing cache on schema type save.
* Added Schema property sameAs core extension.
* Added delete Schema JSON-LD post meta on plugin uninstall.
* Added delete Schema Exclude post meta on plugin uninstall.

= 1.5.9.8 =
* Fixed missing translation in category title.
* Added new filter to schema_category_json to extend category markup.
* Added new function to clear/delete schema json post meta value on post save.
* Added support for schema.org sameAS property for category pages.
* Added new custom taxonomy meta fields class.  
* Added new admin link to the plugin About page. 
* Corrected names of some functions, and renamed files.
* Modified readme.txt file.

= 1.5.9.7 =
* Fixed slow performance by reducing number of queries made by the plugin.
* Fixed post meta generator, empty meta box display.
* Fixed an error on post save, global post variable needed to be called.
* Enhanced overall performance by caching JSON-LD array in post meta. 
* Enhanced author output, now it has been added as a core extension.
* Added new settings to control AudioObject, VideoObject, and Comments.
* Cleaned up some code and moved admin and other functions to its own files.
* Pumped WordPress version, checked and tested with 4.6.1 release.

= 1.5.9.6 =
* Added new extension support for AudioObject.
* Added missing translation to Schema VideoObject settings.
* Enhanced plugin activation process.
* Enhanced update post meta reference.
* Added deprecated functions to its own file.
* Corrected words in the Contextual Help screen.
* Updated the welcome screen.
* Updated the readme.txt file. 

= 1.5.9.5 =
* Fixed post meta generator missing description. 
* Fixed Publisher logo image, image url was not used correctly in some cases.
* Updated plugin settings text fields with placeholder info.
* Updated plugin settings page screenshot.
* Pumped WordPress version, checked and tested with 4.6 release.
* Modified readme.txt file, added link to updated plugin documentation page.

= 1.5.9.4 =
* Fixed capabilities issue, plugin settings was not saving properly.
* Modified readme.txt file, added more info to the FAQ section.

= 1.5.9.3 =
* Added new integration file for Visual Composer plugin.
* Added save new setting field to save the upgraded from version on plugin install. 
* Added new filter schema_output_blog_post.
* Added new filter schema_output_category_post.
* Added new filter schema_wp_exclude.
* Added size class for post meta field with the type of number.
* Added new Contextual Help tab to the plugin settings page.
* Added new filters and hooks to allow developers to extend the settings page.
* Added new features for extensions, now it supports licenses and auto upgrades. 
* Added new settings field for Publisher Logo.
* Fixed schema markup for Publisher Logo. 
* Fixed schema markup for Organization Logo.
* Enhanced preview image display in plugin settings, set width to 300px.
* Enhanced wording in plugin settings page and simplified settings fields.

= 1.5.9.2 =
* Removed the Disable SiteLinks Search Box feature, not needed in the plugin.
* Removed the Content tab within the plugin settings page.
* Enhanced the plugin settings page, rearranged settings in a simpler way.

= 1.5.9.1 =
* Fixed a warning notice when saving drafts in editor, editing posts was not possible.

= 1.5.9 =
* Fixed query conflicts on category pages by creating a secondary loop.
* Fixed category description, apply strip_tags function to remove HTML tags.
* Added check to prevent processing code in backend when runs category schema.
* Added new Post Meta Box feature.
* Added a new schema_wp_filter_content filter.
* Added new integration filter function for Divi theme.
* Use wp_strip_all_tags instead of strip_tags for short content.
* Updated plugin screenshots, added a new screenshot for post meta box feature.
* Corrected a typo in plugin settings page.
* Modified readme.txt file, corrected link to support forum and some typos.  

= 1.5.8 =
* Fixed datePublished and dateModified markups, make sure it is in ISO 8601 format.
* Fixed Preview button was not showing in edit screen.
* Extended the plugin settings extensibility with new repeatable field type.
* Enhanced post meta wording and fixed typos.
* Modified readme.txt file, corrected link to support forum and some typos.  

= 1.5.7 =
* Added support for schema type CollectionPage for Categories.
* Added new filter schema_wp_types to override or extend options.
* Added new filter schema_wp_publisher to override publisher array.
* Added new filter schema_wp_author to override author array.
* Added new function schema_wp_do_post_meta to allow adding custom post meta boxes.
* Added new function to return Author array.

= 1.5.6 =
* Fixed a notice on front end caused when admin bar menu is called.
* Added new post meta box for Schema exclusions, turn off Schema on specific posts.
* Added new integration for SEO Framework plugin.
* Modified search_term to search_term_string search variable in SiteLinks Search Box.
* Enhanced WPRichSnippets integration, put code on its own file.
* Enhanced Yoast SEO integration, put code on its own file.
* Enhanced functions naming to prevent any possible conflict with Schema Removal.
* Updated the plugin welcome page.
* Modified readme.txt file.

= 1.5.5 =
* Fixed a fatal error when Genesis is not the active Theme.

= 1.5.4 =
* Added admin notification for feedback.
* Added support for Schema type Blog, markup blog post list.
* Added new function to retrieve post comments.
* Added admin bar menu item for easy Structured Data testing access.
* Added a check if WPRichSnippets plugin is active, remove its admin bar menu.
* Added integration with Genesis Framework.
* Enhanced Media function, check for images in content if Featured image not found.
* Enhanced plugin wording in menu, change Schema Types to read Types, make it simple.
* Updated the plugin welcome page.
* Modified readme.txt file.

= 1.5.3 =
* Added better support for enabled AMP post types.
* Added support Schema for comment to markup comments in Article types.
* Added support Schema for commentCount markup to Article types.
* Added new filter to override default comments number to include in markups.
* Added better support for author, now include description and gravatar. 
* Added support for author sameAs for social profiles if provided in user account.
* Added GNU GENERAL PUBLIC LICENSE file.
* Added README.md

= 1.5.2 =
* Fixed a warning on 404 and search result pages.
* Added new schema type AboutPage, core extension for the about page.
* Added new schema type ContactPage, core extension for the contact page.
* Added new settings under the Content tab to select About and Contact pages.
* Updated the plugin welcome page.
* Modified readme.txt file.

= 1.5.1 =
* Fixed an error, AMP was not working because a call to wrong function.
* Fixed wrong Class name for post meta creation.
* Fixed schema output when set a static page as Front page. 
* Fixed a bug in WPRichSnippets integration on pages that includes VideoObject.
* Added several checks not to process oEmbed unless an embedded video is in content. 
* Tested with WordPress, version 4.5.3 release.
* Updated the plugin welcome page. 

= 1.5 =
* Fixed error caused on plugin activation.
* Fixed Schema post was saved as a draft.
* Added ability to filter the Schema JSON array schema_json.
* Added support for Schema VideoObject markups through oEmbed.
* Added new post meta box features for VideoObject with Schema type edit page.
* Added new post meta box features for completing missing video info.
* Modified wording in the Schema post type post meta.
* Modified the Publish button to read Save.

= 1.4.7 =
* Fixed admin notices by adding conditions to run the Auto Featured image script. 
* Added new ability to filter schema markups to work with post categories.
* Added new function to automatically save categories in Schema post meta.
* Added an alert on Schema post save when no Post Type is selected.
* Enhanced the default submit box on Schema post type and made cleaner. 
* Removed the Preview button from Create Schema meta box.
* Modified the Publish button in Create Schema meta box to read “Create Schema”.
* Updated the plugin welcome page.
* Modified readme.txt file.

= 1.4.6 =
* Added ability to set Featured image automatically.
* Added back settings Content tab.
* Fixed post id in schema output function.
* Fixed admin notices.
* Modified readme.txt file.

= 1.4.5 =
* Added Person schema to author archive pages.
* Added schema keywords to BlogPosting.
* Added a new function to retrieve post first category to be set as articleSection.
* Now play nice with WPRichSnippets plugin, do not output schema if WPRS is enabled.
* Fixed notices in edit screen by running functions only on schema post type screen.

= 1.4.4 =
* Added support for schema specific type Article > Report.
* Added new filter schema_wp_cpt_enabled to override enabled post types array.
* Added new function to insert schema post ref on wp_insert_post.
* Added new function to insert meta post ref on schema type save.
* Added insert ref on plugin activation for post and page post types.
* Added delete ref on uninstall.
* Fixed media, do not output media if image width and height are not presented. 

= 1.4.3 =
* Fixed media output, do not output media if image url is not presented. 

= 1.4.2 =
* Fixed invalid Thesis post image url, make sure url is a valid one.

= 1.4.1 =
* Fixed avoid running schema output on home page and archive pages.

= 1.4 =
* Introduced new way to output schema.
* Added new schema post type to allow users to create new Schema types.
* Added new post meta functions for easy creation.
* Added the ability to enable Schema.org markups on Post Type bases.
* Added automatically insert post and page schema types on plugin activation.
* Added new schema types, now Article can have sub types.
* Added a new function for media handling.
* Added new filter schema_output for overriding schema output array.
* Added integration for Thesis theme 2.x Post Image.
* Added check version, if below 1.4 run required functions on activation.
* Removed functions that has been used to output schema in version 1.3
* Removed the content tab from settings page. 
* Fixed uninstall.php issue, it was not working properly on multisite.
* Cleaned admin styles file.
* Enhanced the plugin Welcome page.
* Updated plugin screenshots.
* Modified readme.txt file.

= 1.3 =
* Added integration for AMP plugin, Schema will take over for better schema output handling.
* Enhanced JSON-LD output functions.

= 1.2 =
* Added new schema type BlogPosting.
* Switched blog posts schema to BlogPosting.
* Set schema for Article on page post type.
* Added new filters schema_blog_posting to override schema BlogPosting array output.
* Added new filters schema_article to override schema Article array output.
* Added new screenshot.
* Modified readme.txt file.

= 1.1.1 =
* Now play nicely with Yoast SEO plugin.
* Updated screenshot.
* Modified readme.txt file.

= 1.1 =
* Added new settings tab for content.
* Added support for schema type Article on blog posts.
* Fixed a bug within plugin settings functions.

= 1.0 =
* Initial Release

== Upgrade Notice ==

= 1.7 =
In this release, we have introduced new settings page and step-by-step settings Configuration Wizard. Please, update the plugin on your website now to get fixes and enhancements.

= 1.6.9.8.2 =
In this release, most of the reported bugs has been fixed. Please, update the plugin on your website now to get fixes and enhancements.

= 1.6.9.8.1 =
In this release, we reverted back to 1.6.9.8, update the plugin on your website now to get fixes.

= 1.6.9.8 =
In this release, most of the reported bugs has been fixed, including a fix for Easy Digital Downloads plugin. Also, new features has been introduced, example WPHeader and WPFooter markups, and support for ItemList markup on post types archive pages. Please, update the plugin on your website now to get fixes and enhancements.

= 1.6.9.7 =
This quick update include a fixe for Sitelinks Search Box markup output. Please, update the plugin on your site to get these fixes and enhancements.

= 1.6.9.6 =
This update include several bug fixes and enhancements. Please, update the plugin on your site to get these fixes and enhancements.

= 1.6.9.5 =
This update include several bug fixes and enhancements including AMP fixes, WooCommerce breadcrumb fixes, and introducing schema markup on tags archives pages. Please, update the plugin on your site to get these fixes and features.

= 1.6.9.4 =
This update include several bug fixes, enhancements, and new features including Breadcrumbs JSON-LD markup, which play nicely with Yoast SEO and Genesis. Please, update the plugin on your site to get these fixes and features.

= 1.6.9.3 =
This update include several bug fixes and new enhancements. Please, update the plugin on your site to get these fixes and enhancements.

= 1.6.9.2 =
This update include several bug fixes and new enhancements. Please, update the plugin on your site to get these fixes and enhancements.

= 1.6.9.1 =
This update include several bug fixes and user experience enhancements. Please, update the plugin on your site to get these fixes and enhancements.

= 1.6.9 =
This update include bug fixes, and more. Also minimum version of PHP is set to 5.4, so make sure you have this version running on your server. Please, update the plugin on your site to get these fixes.

= 1.6.8 =
This update include bug fixes. Please, update the plugin on your site to get these fixes.

= 1.6.7 =
This update include important functions, and introduce a new columns in Schema post type. Please, update the plugin on your site to get these new features.

= 1.6.6 =
This update include an important fixes. Please, update the plugin on your site to get this fix.

= 1.6.5 =
This update include important fixes for empty array output on front page when set Yoast SEO output to true. Please, update the plugin on your site to get this fix.

= 1.6.4 =
In this update, Schema will override Yoast SEO plugin JSON-LD output on the front page, also a new feature has been added to allow you define the site for an Organization or a Person. Upgrade now to get these enhancements, Note: you will need to configure the Knowledge Graph settings after the upgrade. 

= 1.6.3 =
This update includes a fix for articleSection which caused an error in schema.org markup. Please, upgrade now to get this fix.

= 1.6.2 =
In this update, important bug fixes has been made, prevent fatal errors on admin pages and with older versions of Genesis. Please, upgrade now to get this fix.

= 1.6.1 =
In this update, a bug got fixed and a couple of other enhancements which enhance plugin settings usability has been applied. Please, upgrade now to get these enhancements.

= 1.6 =
Several bug fixes and enhancement has been done in this release, plus a new extension release for Schema Review. Please, upgrade now to get these enhancements.

= 1.5.9.9 =
Several bug fixes and enhancement has been made to the plugin in this release, plus a new sameAs property for your content. Please, upgrade now to get these enhancements.

= 1.5.9.8 =
More enhancement has been made to the plugin in this release, plus a couple of new features. Please, upgrade now to get these enhancements.

= 1.5.9.7 =
This update includes enhancement to performance by caching JSON-LD output in post meta, this will reduce database queries and make your site loads faster. Please, upgrade now to get these enhancements. 

= 1.5.9.6 =
This update includes enhancement to plugin activation, support for schema.org AudioObject and more. Please, upgrade now to get these enhancements. 

= 1.5.9.5 =
This update has an important fix for Publisher logo and some other functions in the plugin. Please, upgrade now to get this fix. 

= 1.5.9.4 =
This update has an important fix for capabilities, plugin settings was not saving properly on newly installed sites. Please, upgrade now to get this fix. 

= 1.5.9.3 =
This update has several features and enhancements, fix for Publisher Logo image, and support for Visual Composer plugin. Please, upgrade now to get this fix. 

= 1.5.9.2 =
In this release, the disable SiteLinks Search Box feature has been removed, this feature was causing issues and having a bug. Please, upgrade now to get this fix. 

= 1.5.9.1 =
This release has a bug fix for a warning notice when saving drafts in editor, editing posts was not possible. Please update the plugin on your website to git this important fix.

= 1.5.9 =
In this release, reported bugs has been fixed. Also, new fixes and integration for Divi theme has been introduced. Please, update the plugin on your website now to get fixes and enhancements.

= 1.5.8 =
In this release, reported bugs has been fixed. Please, update the plugin on your website now to get fixes and enhancements.

= 1.5.7 =
In this release, a new Schema type CollectionPage has been added to empower your site Categories. Please, update the plugin on your website now to get the new enhancements.

= 1.5.6 =
In this release, new integration for SEO Framework plugin and other cool features has been introduced. Please, update the plugin on your website now to get the new enhancements.

= 1.5.5 =
Fixed a fatal error. Please, update the plugin on your website now to get the new enhancements.

= 1.5.4 =
In this release, a good set of enhancements has been added, also introduced new Schema type Blog, which markup your Blog posts page automatically. Also added integration for Genesis Framework. Please, update the plugin on your website now to get the new enhancements.

= 1.5.3 =
In this release, a better support added to AMP pages, now the plugin will work on post types enabled AMP pages if found, and present Schema type by ref post meta saved in each post. Also added support Schema markup for comments. Please, update the plugin on your website now to get the new enhancements.

= 1.5.2 =
New features introduced in this update release, fixed a minor warning on 404 pages. Please, update the plugin on your website now to get this fix and new features.

= 1.5.1 =
Fixed a fatal error on AMP posts, also another fix for VideoObject. Please, update the plugin on your website now to get this fix and other enhancements.

= 1.5 =
Fixed a fatal error on plugin activation, added VideoObject support and more awesome enhancements. Please, update the plugin on your website now to get this fix and other enhancements.

= 1.4.8 =
Fixed an important bug within the plugin, Schema post was saved as draft and no way to set is as publish.Please, update the plugin on your website now to get this fix and other enhancements.

= 1.4.6 =
Added ability to set Featured image automatically when creating or editing a post. Added back settings Content tab. Fixed admin notices and bugs. A few minor code/ documentation tweaks, updated readme.txt file with new details. Please, update the plugin on your website now to get bug fixes, enhancements and new cool features in this release. 