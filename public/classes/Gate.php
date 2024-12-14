<?php

namespace Palasthotel\ALittleMoreSecure;

use Palasthotel\ALittleMoreSecure\Components\Component;

class Gate extends Component {

	public function onCreate(): void {
		parent::onCreate();

		add_action( 'login_form', [ $this, 'login_form' ] );
		add_action( "login_form_login", [ $this, 'login_action' ] );
		add_filter( 'login_form_bottom', [ $this, 'login_form_bottom' ], 10, 2 );

	}

	public function isUnlocked(): bool {
		return apply_filters(
			Plugin::FILTER_IS_UNLOCKED,
			isset($_GET[$this->plugin->environment->getParamName()])
		);
	}

	public function login_form() {

		if (!$this->isUnlocked()) {

			http_response_code(404);

			$waitForSeconds = $this->plugin->environment->getWaitForSeconds();

			if ( WP_DEBUG ) {
				echo "<!-- START secure login -->";
			}
			$img = esc_url( get_admin_url() . 'images/spinner.gif' );
			// --- START
			echo "<div id='secure-login-wrapper'><img src='$img' alt='' />";

			// ------ wait for secure login ---
			echo "<div id='wait-for-secure-login'>";
			printf( "<p>%s</p>", __( "Securing login...", Plugin::DOMAIN ) );
			$text = sprintf(
				__( "%s seconds left", Plugin::DOMAIN ),
				"<span id='wait-for-secure-login__seconds'>$waitForSeconds</span>"
			);
			echo "<p><i>$text</i></p>";
			echo "</div>";

			// ------ redirect to login ---
			printf( "<div id='redirect-to-secure-login'>%s</div>", __( "Redirect to secure login...", Plugin::DOMAIN ) );

			// --- END
			echo "</div>";

			if ( WP_DEBUG ) {
				echo "<!-- END: secure login -->";
			}

			?>
			<style>
				#secure-login-wrapper {
					position: relative;
					padding-top: 20px;
				}

				#secure-login-wrapper img {
					position: absolute;
					top: 22px;
				}

				#secure-login-wrapper > div {
					padding-left: 30px;
				}

				#wait-for-secure-login, #redirect-to-secure-login {
					position: relative;
					font-size: 1.1rem;

				}

				#wait-for-secure-login p:nth-child(2) {
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
					const hashParts = href.split("#");
					const connector = hashParts[0].indexOf("?") > 0 ? "&" : "?";
					window.location.href = hashParts[0] + connector + "<?= $this->plugin->environment->getParamName(); ?>" + (hashParts.length > 1 ? "#" + hashParts[1] : "");
				}, waitForSeconds * 1000);
			</script>
			<?php
			login_footer();
			exit;
		} else {
			?>
			<style>
				#secure-login-info {
					padding-bottom: 10px;
					font-size: 14px;
				}
			</style>
			<?php
			printf( "<p id='secure-login-info'>🔒 %s</p>", __( "Your login is a little more secure.", Plugin::DOMAIN ) );
			$this->nonceField();
		}
	}

	public function nonceField() {
		wp_nonce_field( Plugin::NONCE_ACTION, Plugin::NONCE_NAME );
	}

	public function login_action() {
		if (
			$_SERVER['REQUEST_METHOD'] === 'POST'
			&&
			(
				! isset( $_POST[ Plugin::NONCE_NAME ] )
				||
				! wp_verify_nonce( $_POST[ Plugin::NONCE_NAME ], Plugin::NONCE_ACTION )
			)
		) {
			wp_die(
				__( "Sorry, this feels not very secure.", Plugin::DOMAIN ),
				__( "🔒", Plugin::DOMAIN ),
				[
					"response" => 400,
					"link_text" => __("Goto login form", Plugin::DOMAIN),
					"link_url" => wp_login_url(),
				]
			);
		}
	}

	public function login_form_bottom( $content, $args ) {
		// other login forms that are not on /wp-login.php are ignored by this plugin
		ob_start();
		$this->nonceField();
		$field = ob_get_contents();
		ob_end_clean();

		return $content . $field;
	}
}
