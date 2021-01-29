<?php
/**
 * AMP One Signal compatibility plugin bootstrap.
 *
 * @package   Google\AMP_One_Signal_Compat
 * @author    Your Name, Google
 * @license   GPL-2.0-or-later
 * @copyright 2020 Google Inc.
 *
 * @wordpress-plugin
 * Plugin Name: AMP One Signal Push Compat
 * Plugin URI: https://wpindia.co.in/
 * Description: Plugin to add <a target="_blank" href="https://wordpress.org/plugins/amp/">AMP</a> compatibility to <a target="_blank" href="https://wordpress.org/plugins/onesignal-free-web-push-notifications/">OneSignal – Web Push Notifications</a> plugin.
 * Version: 0.1
 * Author: milindmore22
 * Author URI: https://wpindia.co.in/
 * License: GNU General Public License v2 (or later)
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace Google\AMP_One_Signal_Compat;

/**
 * Whether the page is AMP.
 *
 * @return bool Is AMP.
 */
function is_amp() {
	return function_exists( 'is_amp_endpoint' ) && is_amp_endpoint();
}

/**
 * Run Hooks.
 */
function add_hooks() {

	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	/**
	 * Check if One Signal WP plugin is active and we are on AMP page.
	 */
	if ( \is_plugin_active( 'onesignal-free-web-push-notifications/onesignal.php' ) && is_amp() ) {

		add_action( 'wp_head', __NAMESPACE__ . '\override_scripts_and_styles', 11 );
		add_action( 'wp_head', __NAMESPACE__ . '\amp_one_signal_style' );
		add_action( 'amp_post_template_css', __NAMESPACE__ . '\amp_one_signal_style' );

		/**
		 * Add sanitizers to convert non-AMP functions to AMP components.
		 *
		 * @see https://amp-wp.org/reference/hook/amp_content_sanitizers/
		 */
		add_filter( 'amp_content_sanitizers', __NAMESPACE__ . '\filter_sanitizers' );

		if ( function_exists( 'amp_is_legacy' ) && amp_is_legacy() ) {
			add_action( 'amp_post_template_body_open', __NAMESPACE__ . '\add_amp_web_push' );
			add_action( 'amp_post_template_footer', __NAMESPACE__ . '\add_amp_one_signal_widget' );
		} else {
			add_action( 'wp_body_open', __NAMESPACE__ . '\add_amp_web_push' );
			add_action( 'wp_footer', __NAMESPACE__ . '\add_amp_one_signal_widget' );
		}
	}
}

add_action( 'wp', __NAMESPACE__ . '\add_hooks' );

/**
 * Remove enqueued JS.
 *
 * @see lovecraft_load_javascript_files()
 */
function override_scripts_and_styles() {

	if ( defined( 'ONESIGNAL_DEBUG' ) && defined( 'ONESIGNAL_LOCAL' ) ) {
		/**
		 * Remove Local SDK file if in debug mode
		 */
		wp_dequeue_script( 'local_sdk' );
	} else {
		/**
		 * Removes remote SDK file.
		 */
		wp_dequeue_script( 'remote_sdk' );
	}

}

/**
 * Check if onesignal plugin is installed and configured.
 *
 * @return boolean string |false App id if plugin is configured otherwise false.
 */
function amp_is_onesignal() {

	if ( ! class_exists( 'OneSignal' ) ) {
		return false;
	}

	// Get App ID.
	$onesignal_wp_settings = \OneSignal::get_onesignal_settings();
	$onesignal_app_id      = $onesignal_wp_settings['app_id'];

	if ( ! empty( $onesignal_app_id ) ) {
		return $onesignal_app_id;
	}

	return false;

}

/**
 * Add AMP style.
 */
function amp_one_signal_style() {

	$style = file_get_contents( __DIR__ . '/css/amp-style.css' );

	if ( function_exists( 'amp_is_legacy' ) && amp_is_legacy() ) {

		echo $style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	} else {
		?>
		<style type="text/css">
			<?php echo $style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</style>
		<?php
	}
}
/**
 * Add sanitizer to fix up the markup.
 *
 * @param array $sanitizers Sanitizers.
 * @return array Sanitizers.
 */
function filter_sanitizers( $sanitizers ) {
	require_once __DIR__ . '/sanitizers/class-sanitizer.php';
	$sanitizers[ __NAMESPACE__ . '\Sanitizer' ] = array();
	return $sanitizers;
}

/**
 * Add amp-web-push component.
 *
 * @return html
 */
function add_amp_web_push() {

	$onesignal_app_id = amp_is_onesignal();

	if ( empty( $onesignal_app_id ) ) {
		return;
	}

	$one_signal_sdk_files_url = plugins_url( 'onesignal-free-web-push-notifications/sdk_files/' );

	$helper_iframe_url     = $one_signal_sdk_files_url . 'amp-helper-frame.html?appId=' . $onesignal_app_id;
	$permission_dialog_url = $one_signal_sdk_files_url . 'amp-permission-dialog.html?appId=' . $onesignal_app_id;
	$service_worker_url    = $one_signal_sdk_files_url . 'OneSignalSDKWorker.js.php?appId=' . $onesignal_app_id;

	echo sprintf(
		'<amp-web-push id="amp-web-push" layout="nodisplay" helper-iframe-url="%1$s" permission-dialog-url="%2$s" service-worker-url="%3$s"></amp-web-push>',
		esc_url( $helper_iframe_url ),
		esc_url( $permission_dialog_url ),
		esc_url( $service_worker_url )
	);
}

/**
 * Add AMP webpush widget.
 */
function add_amp_one_signal_widget() {
	$onesignal_app_id = amp_is_onesignal();

	if ( empty( $onesignal_app_id ) ) {
		return;
	}
	?>
	<!-- A subscription widget -->
	<amp-web-push-widget visibility="unsubscribed" layout="fixed" width="245" height="45">
		<button class="subscribe" on="tap:amp-web-push.subscribe">
			<amp-img
					class="subscribe-icon"
					width="24"
					height="24"
					layout="fixed"
					src="data:image/svg+xml;base64,PHN2ZyBjbGFzcz0ic3Vic2NyaWJlLWljb24iIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxwYXRoIGQ9Ik0xMS44NCAxOS44ODdIMS4yMnMtLjk0Ny0uMDk0LS45NDctLjk5NWMwLS45LjgwNi0uOTQ4LjgwNi0uOTQ4czMuMTctMS41MTcgMy4xNy0yLjYwOGMwLTEuMDktLjUyLTEuODUtLjUyLTYuMzA1czIuODUtNy44NyA2LjI2LTcuODdjMCAwIC40NzMtMS4xMzQgMS44NS0xLjEzNCAxLjMyNSAwIDEuOCAxLjEzNyAxLjggMS4xMzcgMy40MTMgMCA2LjI2IDMuNDE4IDYuMjYgNy44NyAwIDQuNDYtLjQ3NyA1LjIyLS40NzcgNi4zMSAwIDEuMDkgMy4xNzYgMi42MDcgMy4xNzYgMi42MDdzLjgxLjA0Ni44MS45NDdjMCAuODUzLS45OTYuOTk1LS45OTYuOTk1SDExLjg0ek04IDIwLjk3N2g3LjExcy0uNDkgMi45ODctMy41MyAyLjk4N1M4IDIwLjk3OCA4IDIwLjk3OHoiIGZpbGw9IiNGRkYiLz48L3N2Zz4=">
			</amp-img>
			<?php esc_html_e( 'Subscribe' ); ?>
		</button>
	</amp-web-push-widget>


	<!-- An unsubscription widget -->
	<amp-web-push-widget visibility="subscribed" layout="fixed" width="230" height="45">
		<button class="unsubscribe" on="tap:amp-web-push.unsubscribe">
			<?php esc_html_e( 'Unsubscribe' ); ?>
		</button>
	</amp-web-push-widget>
	<?php
}
