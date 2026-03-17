=== Internal Link Preview Tooltip ===
Contributors:       nmtnguyen56
Tags:               link preview, tooltip, internal link, post preview
Requires at least:  5.2
Tested up to:       6.9
Stable tag:         1.0.0
Requires PHP:       7.2
License:            GPLv2 or later
License URI:        https://www.gnu.org/licenses/gpl-2.0.html

Displays a neat and responsive preview tooltip when hovering over internal links within your post content, enhancing user experience and engagement.

== Description ==

**Internal Link Preview Tooltip** is a lightweight and highly optimized plugin that keeps your readers engaged by allowing them to peek into an internal link without leaving the current page. 

When a user hovers over an internal link in your post, a clean, modern tooltip appears displaying the destination post's title, a brief excerpt (or content), and a customizable "Read more" button.

### Features:
* **Smart Positioning:** Automatically calculates space to show the tooltip above or below the link, complete with a dynamic pointer arrow.
* **AJAX Powered:** Fetches preview data seamlessly without reloading the page.
* **Customizable via Settings:** * Choose between showing the post 'Excerpt' or 'Full Content'.
    * Set the exact number of words to display.
    * Customize the "Read more" button text.
* **Highly Optimized:** Only targets links within the main content area, ignoring menus, sidebars, and external links to prevent bloat.
* **Translation Ready:** Fully localized and ready to be translated into any language.

== Installation ==

1. Upload the `int-link-preview` folder to the `/wp-content/plugins/` directory, or install the ZIP file via the WordPress Plugins menu.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to **Settings > Link Preview Tooltip** to configure the word limit, content type, and button text.
4. Done! Hover over any internal link inside your post content to see it in action.

== Frequently Asked Questions ==

= Does it work with external links? =
No. To protect your site's performance and UX, this plugin specifically targets internal links (links within your own domain) only.

= Can I change the tooltip design? =
Yes! You can easily override the CSS by adding custom CSS to your theme, or modifying the `assets/int-link-preview.css` file directly.

== Changelog ==

= 1.0.0 =
* Initial release.