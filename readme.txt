=== Veila – Coming Soon & Maintenance Mode ===
Contributors: sanctoria
Tags: maintenance mode, coming soon, under construction, maintenance, launch page
Requires at least: 6.4
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Coming soon and maintenance mode pages with live preview and correct HTTP status. No remote assets. Settings: Appearance > Veila.

== Description ==

Veila draws a veil over your site while you build it or work on it. Visitors see a branded splash page. Logged-in administrators keep seeing the real site, with a reminder in the admin bar.

Veila is off when you activate it. Nothing changes for visitors until you pick a mode and save.

= Two modes, done properly =

* **Coming soon** — for a site that has not launched yet. Sends HTTP 200 so search engines can index the page, or tick "noindex" to keep it out of results.
* **Maintenance** — for short planned work on a live site. Sends HTTP 503 with a `Retry-After` header and `X-Robots-Tag: noindex`, so search engines come back later instead of dropping your pages.

= Design =

* Five starter templates: Veila Ivory, Veila Night, Clean Light, Clean Dark, and Minimal
* Four layouts: centered, card, split (image beside the text), and minimal
* Logo for light backgrounds and a second logo for dark backgrounds; Veila picks the right one automatically
* Background color, gradient, or image from the Media Library, with an overlay
* Text, heading, accent, and card colors, with a contrast check that falls back to a readable color on the live page
* Headline, badge, and message (basic formatting and links allowed)
* Social links (Facebook, X, Instagram, LinkedIn, YouTube, TikTok, GitHub, email) drawn as inline icons
* Footer text and an optional "Log in" link
* Custom CSS, applied only to the splash page
* A live preview beside the settings, plus a full-page preview of either mode

= SEO and access =

* SEO title and meta description, with Open Graph tags
* Choose who sees the real site by capability (administrators by default)
* An admin-bar indicator whenever a mode is on

= Built to stay out of the way =

* The dashboard, `wp-login.php`, the REST API, `wp-cron.php`, `admin-ajax.php`, XML-RPC, `robots.txt`, and WP-CLI keep working
* Feeds and sitemaps get a short plain-text reply (503 during maintenance, 404 while coming soon)
* Splash responses send no-cache headers, and Veila asks common cache plugins to purge when you change modes (WP Super Cache, WP Rocket, W3 Total Cache, LiteSpeed Cache, SiteGround Optimizer, Breeze, Cache Enabler, Nginx Helper, WP Fastest Cache)
* Emergency off switch in `wp-config.php`: `define( 'VEILA_DISABLE', true );`
* No remote fonts, scripts, tracking, ads, or bundled plugin installs. Images come from your Media Library.

A separate Pro add-on (sold independently, not required) adds eleven more templates, a countdown that can launch the site automatically, an email signup list stored on your site with CSV export, expiring secret preview links, role and IP bypass, page rules, scheduling, video backgrounds, bundled fonts, white-label, and import/export. Paid licenses are a one-time purchase: Veila Pro ($20, one site, three years of updates), Veila Pro Trinity ($50, three sites, lifetime updates), or Veila Pro Infinity ($99, unlimited sites, lifetime updates). The free plugin does not lock any of its own features behind a license.

== Installation ==

1. Upload the `veila` folder to the `/wp-content/plugins/` directory, or install the plugin through the Plugins screen.
2. Activate Veila through the Plugins screen. Veila starts in the "Off" mode.
3. Go to Appearance > Veila, pick a starter template, and set your logo, colors, and text.
4. On the Status tab choose "Coming soon" or "Maintenance" and save. Open the site in a private window to see what visitors see.

== Frequently Asked Questions ==

= Why can I still see my normal site? =

You are logged in with a role that can see the real site (administrators by default). Open the site in a private window, or use the "Open full page" preview link on the settings page.

= Which mode should I use? =

Use Coming soon before launch. Use Maintenance for short work on a live site: the 503 status tells search engines the outage is temporary and when to retry.

= I locked myself out. How do I turn Veila off? =

Add `define( 'VEILA_DISABLE', true );` to `wp-config.php`. Veila stops showing the splash page right away. `wp-login.php` is never blocked, so you can also sign in and switch the mode to Off.

= My visitors still see the splash page after I turned it off. =

A page cache or CDN is probably serving an old copy. Veila purges the common cache plugins automatically; purge your CDN or host cache by hand if you use one.

= Does Veila load anything from other websites? =

No. The splash page uses system fonts and inline icons, and images come from your Media Library. Social links are plain links.

= Does the REST API stay available? =

Yes. The block editor, mobile apps, and integrations rely on it, so Veila leaves it alone. Veila only covers front-end pages.

= Is there a Pro version? =

Yes. The optional add-on is sold separately as a one-time purchase: Veila Pro ($20, one site, three years of updates), Veila Pro Trinity ($50, three sites, lifetime updates), or Veila Pro Infinity ($99, unlimited sites, lifetime updates). The free Veila plugin stays complete and does not need a license key.

= How do I remove all traces of the plugin? =

Deactivate and delete the plugin. Uninstall removes its settings. Images you uploaded stay in the Media Library.

== Screenshots ==

1. Settings page with the live preview beside the Status tab.
2. The Veila Ivory starter on the front end.
3. The Veila Night starter in maintenance mode.
4. The Design tab with light and dark logos.
5. The Veila Ivory starter on a phone.
6. The Veila Night starter in maintenance mode, on a phone.

== Changelog ==

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.0.0 =
Initial release of Veila.
