# wizewp-maintenance-mode
=== WizeWP Maintenance Mode ===
Contributors: wizewp  
Donate link: https://wizewp.com  
Tags: maintenance, coming soon, countdown, password, maintenance mode 
Requires at least: 5.2  
Stable tag: 1.0.0
Tested up to: 6.8
Requires PHP: 7.2    
License: GPLv2 or later  
License URI: https://www.gnu.org/licenses/gpl-2.0.html  

Lightweight and modern maintenance mode plugin. Custom text, image, countdown, password access. Built with simplicity and speed in mind.

== Description ==

**WizeWP Maintenance Mode** is a clean and powerful plugin that lets you display a customizable maintenance page while your website is offline.

**Main Features:**
– Custom logo, background image, and heading text
– Countdown timer with flexible options (show/hide days, hours, minutes, seconds)
– Password access for selected users
– Live preview in admin (without refreshing)
– Custom CSS box with syntax highlighting (CodeMirror)
– Clean, responsive, and fast

No bloat. Just everything you need.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/wizewp-maintenance-mode/` directory or install via WordPress plugins screen.
2. Activate the plugin.
3. Go to `Settings <span aria-hidden="true" class="wp-exclude-emoji"><span aria-hidden="true" class="wp-exclude-emoji"><span aria-hidden="true" class="wp-exclude-emoji">→</span></span></span> WizeWP <span aria-hidden="true" class="wp-exclude-emoji"><span aria-hidden="true" class="wp-exclude-emoji"><span aria-hidden="true" class="wp-exclude-emoji">→</span></span></span> Maintenance Mode` and enable maintenance mode.

== Frequently Asked Questions ==

= Will it block access for admins? =  
No. Logged-in users with admin rights can always see the website.

= Can I add a password for other users? =  
Yes. You can set a custom password in the admin panel. Users will see a password field.

= Does it work with any theme? =  
Yes. The page is completely independent of your theme.

= Is it compatible with caching plugins? =  
Yes, but be sure to clear the cache after enabling maintenance mode.

= Is it translation-ready? =  
Yes. A `.POT` file is included.

== Screenshots ==

1. Settings panel in admin
2. Live preview iframe
3. Countdown and password field on frontend

== Changelog ==

= 1.0.0 =
* First stable release with countdown, password access, live preview, and custom CSS.

== Upgrade Notice ==

= 1.0.0 =
Initial release.
== External Services ==

This plugin connects to an external service provided by WizeWP (https://wizewp.com) in order to retrieve important product announcements, updates, offers or critical notifications.

- What data is sent: No personal data is transmitted. Only a simple HTTP GET request is performed to retrieve public JSON data.
- When: Only when you access the plugin's admin settings page.
- Service provided by: WizeWP (https://wizewp.com)
- Privacy Policy: https://wizewp.com/privacy-policy/
- Terms of Service: https://wizewp.com/terms-of-service/
