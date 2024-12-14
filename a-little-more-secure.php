<?php

/**
 * Plugin Name:       A little more secure DEV
 * Description:       Dev inc file
 * Version:           X.X.X
 * Requires at least: X.X
 * Tested up to:      X.X.X
 * Domain Path:       /plugin/languages
 */

use Palasthotel\ALittleMoreSecure\Plugin;

include dirname(__FILE__) . "/public/a-little-more-secure.php";

register_activation_hook(__FILE__, function($multisite){
	Plugin::instance()->onActivation($multisite);
});

register_deactivation_hook(__FILE__, function($multisite){
	Plugin::instance()->onDeactivation($multisite);
});
