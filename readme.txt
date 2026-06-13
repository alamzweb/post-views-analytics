=== Post Views Analytics ===
Contributors: msalam
Tags: post views, analytics, counter, traffic statistics, post counter
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 8.0
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Track post views, analyze content performance, and display detailed view statistics with a modern analytics dashboard.

== Description ==

Post Views Analytics is a lightweight, secure, and modern tracking suite designed to provide high-performance insight vectors directly within your self-hosted WordPress environment. It completely bypasses bloated cloud architectures while giving you access to SaaS-grade analytics reporting widgets.

Engineered directly for performance-obsessed webmasters, the tracking engine utilizes fine-tuned structural post-meta entries rather than locking or scanning direct database entities during high-volume production events.

By default, global frontend outputs are entirely disabled. This leaves you in absolute programmatic control over your performance footprints. Display metrics only exactly where you choose via our modular Gutenberg block architectures or shortcodes.

== Features ==

* Modern SaaS-inspired structural administrative user layout interface.
* Fine-tuned asset counting configurations across posts, pages, and custom post types.
* Integrated cookie-backed anti-flood tracking refreshes.
* Instant generation parameters for native Shortcode configurations.
* Dedicated dynamic decoupled live-rendering Gutenberg block.
* Seamless asynchronous ChartJS rendering modules.
* One-click complete CSV systems extraction mechanisms.
* Hardened internal capability and strict nonce checking parameters.

== Installation ==

1. Upload the entire `post-views-analytics` folder payload directory completely into your active standard `/wp-content/plugins/` structural deployment root directory.
2. Navigate immediately into your WordPress Plugins management dashboard panel screen.
3. Locate the "Post Views Analytics" item block card instance and activate it.
4. Access the dedicated configuration suite by clicking directly onto the new "Post Views Analytics" navigation menu tree layer.

== Frequently Asked Questions ==

= Does this tracking system execute continuous heavy SQL updates? =
No. The engine modifies localized key indices via the core highly optimized Metadata API structure to maximize standard caching layers and scale during intensive concurrency events.

= Will turning on the tracking engine break object caches? =
No. The plugin uses isolated, date-stamped meta fields that update individual metrics records cleanly without modifying full layout arrays or triggering site-wide object drops.

= Can I exclude site administrators from polluting my charts? =
Yes. The settings panel includes distinct toggle controls to prevent administrators and editors from updating counters during content editing workflows.

= How do I display view counts on my site? =
You can display metrics anywhere using the standard `[post_views]` shortcode, or by searching for and embedding the native "Post Views Metrics Display" Gutenberg block in the editor.

= Does this plugin comply with global privacy rules like GDPR? =
Yes. The plugin does not capture personal identifiable information (PII), log user IP addresses, or transfer data back to external third-party servers.

= What happens to data when the plugin is uninstalled? =
When you run a formal uninstall process, the plugin triggers a complete database cleanup via `uninstall.php`, wiping out all global structural settings and post-meta analytics arrays.

= Can I target specific custom post types? =
Yes. The tracking module dynamically scans your active post types and provides multi-select options in the settings interface to restrict tracking to specific content types.

= Is the global display element responsive on mobile devices? =
Yes. The frontend CSS rules use flexbox wrappers to ensure the views badge fits cleanly onto smartphones and tablets.

= Can I pass a specific post ID value inside shortcodes? =
Yes. Use the explicit parameter markup: `[post_views post_id="412"]` to query and fetch view metrics for a specific post from anywhere on your site.

= Does it support WP Super Cache or W3 Total Cache configurations? =
Yes. However, if full HTML page-caching is active, tracking hits are processed natively during non-cached page headers. For fully cached environments, using the shortcode layout provides real-time lookups on load.

= How long does the data stay in the database? =
You can customize data retention directly from the settings panel, setting it to automatically clear old daily tracking logs after 30 days, 90 days, or keep records indefinitely.

= Can I change the icon shown before the count? =
Yes. The settings panel lets you toggle the icon on or off, add custom labels, or rewrite the display string format using tags like `{icon}`, `{count}`, and `{text}`.

== Changelog ==

= 1.0.0 =
* Initial production-ready release. Full SaaS-style layout metrics, tracking controls, and standalone Gutenberg/Shortcode functionality included.

== Upgrade Notice ==

= 1.0.0 =
Initial operational engine deployment. No update required.