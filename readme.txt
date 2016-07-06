=== Schema ===
Contributors: hishaman, schemapress
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=NGVUBT2QXN7YL
Tags: schema, schema.org, json, json-ld, google, seo, structured data, markup, search engine, search, rich snippets, social, post, page, plugin, wordpress, content, article, news, search results, site name, knowledge graph, social, social profiles, keywords, meta-tags, metadata, tags, categories, optimize, ranking, search engine optimization, search engines, serp, sitelinks, google sitelinks, sitelinks search box, google sitelinks search box, semantic, structured, canonical, custom post types, post type, title, terms, media, images, thumb, featured, url, video, video markup, video object, VideoObject, video schema, about, contact, amp, mobile
Requires at least: 3.0
Tested up to: 4.5.3
Stable tag: 1.5.3

Get the next generation of Structured Data to enhance your WordPress site presentation in search results.

== Description ==

Super fast, light-weight plugin for adding schema.org structured data markup in recommended JSON-LD format automatically to WordPress sites.

Enhanced Presentation in Search Results By including structured data appropriate to your content, your site can enhance its search results and presentation.

Check out the [Plugin Homepage](http://schema.press/) for more info and documentation.

Developers? Feel free to [fork the project on GitHub](https://github.com/schemapress/Schema) and submit your contributions via pull request.

**What is Schema markup?**

Schema markup is code (semantic vocabulary) that you put on your website to help the search engines return more informative results for users. So, Schema is not just for SEO reasons, it’s also for the benefit of the searcher. 

**Schema Key Features**

* Easy to use, set it and forget it minimal settings. 
* Enable Schema types at once per custom post type or post category.
* Provide a valid markup, test it in Google Structured Data Testing Tool.
* Output JSON-LD format, the most recommended by Google.
* Extensible, means you can extend its functionality via other plugins, extensions or within your Theme’s functions.php file.

**Supported Google/Schema Markups**

* [Knowledge Graph](https://developers.google.com/structured-data/customize/overview)
 * [Logos](https://developers.google.com/structured-data/customize/logos)
 * [Company Contact Numbers](https://developers.google.com/structured-data/customize/contact-points)
 * [Social Profile Links](https://developers.google.com/structured-data/customize/social-profiles)

* Style Your Search Results:
 * [Enable Sitelinks Search Box](https://developers.google.com/structured-data/customize/logos)
 * [Show Your Site Name in Search](https://developers.google.com/structured-data/site-name)

**Supported Schema Types**
 
* Creative Works
 * [Article](https://schema.org/Article) enabled on Pages
  * [BlogPosting](https://schema.org/BlogPosting) enabled on Posts
  * [NewsArticle](https://schema.org/NewsArticle)
  * [Report](https://schema.org/Report)
  * [ScholarlyArticle](https://schema.org/ScholarlyArticle)
  * [TechArticle](https://schema.org/TechArticle)

* [AboutPage](https://schema.org/AboutPage) to markup the About page.

* [ContactPage](https://schema.org/ContactPage) to markup the Contact page.

* [Person](https://schema.org/Person) enabled on Author pages

* [VideoObject](https://schema.org/VideoObject) enabled VideoObject markup automatically on all videos embedded with oEmbed.

* Supported Plugins: 
 * Yoast SEO plugin.
 * AMP plugin.
 * WPRichSnippets plugin.

* Supported Themes
 * Thesis Theme 2.x 
 
== Installation ==

1. Upload the entire `schema` folder to the `/wp-content/plugins/` directory
2. DO NOT change the name of the `schema` folder
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Navigate to the `Schema > Settings` menu to configure the plugin

== Frequently Asked Questions ==

= The plugin isn't working or have a bug? =

Post detailed information about the issue in the [support forum]() and we will work to fix it.

= Knowledge Graph is not showing? =

The plugin meant to validate markup in Google Structured Data Testing Tool, we don’t have control over the actual display of Knowledge Graph.

= I see an error in Google Structure Data Testing Tool =

* Image error: This is a missing WordPress Featured image, try to upload a Featured image.

* Logo error: This is a missing Organization logo, it can be set in the plugin settings page, under the Knowledge Graph tab: Schema > Settings > Knowledge Graph > Logo.

== Screenshots ==
1. Knowledge Graph settings tab.
2. Create new schema type screen.
3. Google Structured Data Testing Tool.

== Changelog ==

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
* Modified ready.txt file.

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
* Modified ready.txt file.

= 1.4.6 =
* Added ability to set Featured image automatically.
* Added back settings Content tab.
* Fixed post id in schema output function.
* Fixed admin notices.
* Modified ready.txt file.

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
* Modified ready.txt file.

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
* Modified ready.txt file.

= 1.1.1 =
* Now play nicely with Yoast SEO plugin.
* Updated screenshot.
* Modified ready.txt file.

= 1.1 =
* Added new settings tab for content.
* Added support for schema type Article on blog posts.
* Fixed a bug within plugin settings functions.

= 1.0 =
* Initial Release

== Upgrade Notice ==

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