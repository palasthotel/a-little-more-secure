=== A litte more secure ===
Contributors: palasthotel, edwardbock, janaeggebrecht
Donate link: http://palasthotel.de/
Tags: security
Requires at least: 5.0
Requires PHP: 8.2
Tested up to: 6.7.1
Stable tag: 1.0.4
License: GPL-3.0-or-later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Stop bots from brute force hacking your wp-login.php

== Description ==

Stop bots from brute force hacking your wp-login.php

A request to wp-login.php without an unlock parameter answers with a 404 and a
holding page. JavaScript counts down a few seconds, then redirects to the same
URL with the parameter appended. Only that request renders a usable login form,
and only it carries the nonce that a login POST has to contain — a POST without
a valid nonce is rejected.

This raises the cost of naive automation. It is not a lockout mechanism: whoever
requests the unlock URL first can read the nonce and post with it, which is why
the plugin is called *a little* more secure.

There is nothing to configure. Activating the plugin is enough.

== Customising ==

Three filters, for a theme or a small plugin of your own.

`a_little_more_secure_is_unlocked` decides whether a request counts as unlocked.
By default that is the presence of the parameter; override it to implement your
own rule, for example a one-time token or an office IP allowlist:

`add_filter( 'a_little_more_secure_is_unlocked', function ( $is_unlocked ) {
	return $is_unlocked || my_own_check();
} );`

`a_little_more_secure_get_param_name` changes the name of the unlock parameter,
`a-little-more-secure` by default.

`a_little_more_secure_redirect_wait_seconds` changes the delay before the
redirect, 3 seconds by default.

A theme with its own login form that posts to wp-login.php has to render the
nonce itself, otherwise the POST is rejected — call
`a_little_more_secure_nonce_field()` inside the form. Forms built with
`wp_login_form()` get it automatically.

== Installation ==

1. Upload `a-little-more-secure.zip` to the `/wp-content/plugins/` directory
1. Extract the Plugin to a `a-little-more-secure` Folder
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==


== Screenshots ==

1. Wait a few seconds for the login to be secured

2. Your login is a little bit more secure

== Changelog ==

= 1.0.4 =
* Check compatiblility with 6.5.2

= 1.0.3 =
* Optimization: load textdomain properly
* Optimization: 404 if no valid login parameter

= 1.0.2 =
* Bugfix: Not redirected properly if hash in url

= 1.0.1 =
* WP5.7 compatible

= 1.0.0 =
 * First release


== Upgrade Notice ==
