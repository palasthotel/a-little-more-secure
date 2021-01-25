<?php

/**
 * Plugin Name: A little more secure
 * Plugin URI: https://palasthotel.de
 * Description: Prevent brute force login attacks on wp-login.php
 * Version: 1.0.0
 * Author: Palasthotel <edward.bock@palasthotel.de>
 * Author URI: https://palasthotel.de
 * Text Domain: a-little-more-secure
 * Domain Path: /languages
 * Requires at least: 4.0
 * Tested up to: 5.6
 * License: http://www.gnu.org/licenses/gpl-3.0.html GPLv3
 *
 * @copyright Copyright (c) 2018, Palasthotel
 * @package Palasthotel\ALittleMoreSecure
 */

namespace Palasthotel\ALittleMoreSecure;

class Plugin {

	const DOMAIN = "a-little-more-secure";

	const NONCE_ACTION = 'a-little-more-secure-login-action';
	const NONCE_NAME = 'a-little-more-secure-login-nonce';

	const FILTER_GET_PARAM_NAME = "a_little_more_secure_get_param_name";
	const FILTER_REDIRECT_WAIT_SECONDS = "a_little_more_secure_redirect_wait_seconds";

	public function __construct() {
		add_action( 'login_form', [ $this, 'login_form' ] );
		add_action( "login_form_login", [ $this, 'login_action' ] );
	}

	public function getParamName() {
		return apply_filters( self::FILTER_GET_PARAM_NAME, "a-little-more-secure" );
	}

	public function login_form() {
		$getParam = $this->getParamName();
		if ( ! isset( $_GET[ $getParam ] ) ) {
			$waitForSeconds = apply_filters( self::FILTER_REDIRECT_WAIT_SECONDS, 3 );
			$text           = sprintf(
				__( "Redirect to login form in %s seconds.", self::DOMAIN ),
				"<span id='wait-for-secure-login__seconds'>$waitForSeconds</span>"
			);
			echo "<div id='wait-for-secure-login'>$text</div>";
			?>
			<style>
				#wait-for-secure-login {
					font-size: 0.9rem;
					padding-top: 20px;
				}
			</style>
			<script>
				const waitForSeconds = <?= $waitForSeconds ?>;
				let waited = 0;
				const el = document.getElementById("wait-for-secure-login__seconds");
				document.getElementById("user_login").closest("p").remove();
				document.getElementById("user_pass").closest(".user-pass-wrap").remove();
				setInterval(function () {
					waited++;
					const remaining = waitForSeconds - waited;
					el.innerText = remaining >= 0 ? remaining + "" : "0";
				}, 1000);
				setTimeout(function () {
					const href = window.location.href;
					const connector = href.indexOf("?") > 0 ? "&" : "?";
					window.location.href = href + connector + "<?= $getParam; ?>";
				}, waitForSeconds * 1000);
			</script>
			</form>
			<?php
			login_footer();
			exit;
		} else {
			wp_nonce_field( self::NONCE_ACTION, self::NONCE_NAME );
		}
	}

	public function login_action() {
		if (
			$_SERVER['REQUEST_METHOD'] === 'POST'
			&&
			! wp_verify_nonce( $_POST[ self::NONCE_NAME ], self::NONCE_ACTION )
		) {
			echo "Sorry, this feels not very secure.";
			exit;
		}
	}

}

new Plugin();
