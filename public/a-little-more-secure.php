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

	const DEFAULT_GET_PARAM_NAME = "a-little-more-secure";
	const DEFAULT_REDIRECT_WAIT_SECONDS = 3;

	private function __construct() {

		/**
		 * load translations
		 */
		load_plugin_textdomain(
			self::DOMAIN,
			false,
			plugin_basename( dirname( __FILE__ ) ) . '/languages'
		);

		add_action( 'login_form', [ $this, 'login_form' ] );
		add_action( "login_form_login", [ $this, 'login_action' ] );
		add_filter( 'login_form_bottom', [$this, 'login_form_bottom'], 10, 2);
	}

	public function getParamName() {
		return apply_filters( self::FILTER_GET_PARAM_NAME, self::DEFAULT_GET_PARAM_NAME );
	}

	public function login_form() {
		$getParam = $this->getParamName();
		if ( ! isset( $_GET[ $getParam ] ) ) {
			$waitForSeconds = apply_filters( self::FILTER_REDIRECT_WAIT_SECONDS, self::DEFAULT_REDIRECT_WAIT_SECONDS );

			if(WP_DEBUG){
				echo "<!-- START secure login -->";
            }
			$img = esc_url( get_admin_url() . 'images/spinner.gif' );
			// --- START
			echo "<div id='secure-login-wrapper'><img src='$img' />";

			// ------ wait for secure login ---
			echo "<div id='wait-for-secure-login'>";
			printf("<p>%s</p>", __("Securing login...", self::DOMAIN));
			$text           = sprintf(
				__( "%s seconds left", self::DOMAIN ),
				"<span id='wait-for-secure-login__seconds'>$waitForSeconds</span>"
			);
			echo "<p><i>$text</i></p>";
			echo "</div>";

			// ------ redirect to login ---
			printf("<div id='redirect-to-secure-login'>%s</div>", __( "Redirect to secure login...", self::DOMAIN ));

			// --- END
			echo "</div>";

			if(WP_DEBUG){
				echo "<!-- END: secure login -->";
            }

			?>
            <style>
                #secure-login-wrapper{
                    position: relative;
                    padding-top: 20px;
                }
                #secure-login-wrapper img{
                    position: absolute;
                    top: 22px;
                }
                #secure-login-wrapper > div {
                    padding-left: 30px;
                }
                #wait-for-secure-login, #redirect-to-secure-login  {
                    position: relative;
                    font-size: 1.1rem;

                }
                #wait-for-secure-login p:nth-child(2){
                    font-size: 0.9rem;
                }
            </style>
            <script>
                const waitForSeconds = <?= $waitForSeconds ?>;
                let waited = 0;

                const waitEl = document.getElementById("wait-for-secure-login");
                const secondsEl = document.getElementById("wait-for-secure-login__seconds");
                const redirectEl = document.getElementById("redirect-to-secure-login");

                redirectEl.style.display = "none";

                document.getElementById("user_login").closest("p").remove();
                document.getElementById("user_pass").closest(".user-pass-wrap").remove();

                const uiInterval = setInterval(function () {
                    waited++;
                    const remaining = waitForSeconds - waited;
                    secondsEl.innerText = remaining >= 0 ? remaining + "" : "0";
                    if (remaining <= 0) clearInterval(uiInterval);
                }, 1000);
                setTimeout(function () {
                    waitEl.style.display = "none";
                    redirectEl.style.display = "inherit";
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
			$this->nonceField();
		}
	}

	public function nonceField(){
		wp_nonce_field( self::NONCE_ACTION, self::NONCE_NAME );
	}

	public function login_action() {
		if (
			$_SERVER['REQUEST_METHOD'] === 'POST'
			&&
            (
                !isset($_POST[ self::NONCE_NAME ])
                ||
                ! wp_verify_nonce( $_POST[ self::NONCE_NAME ], self::NONCE_ACTION )
			)
		) {
			wp_die( __( "Sorry, this feels not very secure.", self::DOMAIN ));
		}
	}

	public function login_form_bottom($content, $args){
	    // other login forms that are not on /wp-login.php are ignored by this plugin
	    ob_start();
	    $this->nonceField();
		$field = ob_get_contents();
		ob_end_clean();
		return $content.$field;
	}

	private static $instance;

	/**
	 * @return Plugin
	 */
	public static function instance(){
	    if(!static::$instance){
	        static::$instance = new static();
	    }
	    return static::$instance;
	}
}

Plugin::instance();

require_once dirname(__FILE__). "/public-functions.php";
