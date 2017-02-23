<?php
/**
 * Adds the admin menu pages.
 */

namespace WPDiscourse\Admin;

use WPDiscourse\Utilities\Utilities as DiscourseUtilities;

class AdminMenu {
	protected $options_page;

	public function __construct( $options_page ) {
		$this->options_page = $options_page;

		add_action( 'admin_menu', array( $this, 'admin_menu_pages' ) );
		add_action( 'wp_ajax_process_options_reset', array( $this, 'process_reset' ) );
	}

	/**
	 * Adds the Discourse menu and submenu page, called from the 'admin_menu' action hook.
	 */
	public function admin_menu() {
		$settings = add_menu_page(
			__( 'Discourse', 'wp-discourse' ),
			__( 'Discourse', 'wp-discourse' ),
			'manage_options',
			'wp_discourse_options',
			array( $this->options_page, 'display' ),
			'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyhpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMTExIDc5LjE1ODMyNSwgMjAxNS8wOS8xMC0wMToxMDoyMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTUgKE1hY2ludG9zaCkiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MUYxNjlGNkY3NjAxMTFFNjkyRkZBRTlDQTMwREJDQzUiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MUYxNjlGNzA3NjAxMTFFNjkyRkZBRTlDQTMwREJDQzUiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDoxRjE2OUY2RDc2MDExMUU2OTJGRkFFOUNBMzBEQkNDNSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDoxRjE2OUY2RTc2MDExMUU2OTJGRkFFOUNBMzBEQkNDNSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/Pq7th6IAAAP8SURBVHjalFbbT5tlHH56grKWnicw5ijdAjNGg5CxbPGIM7qx7cIlxihxd7oLr/wHlpgsu/FWjbdeeOWFwUQixmW6GGbKpDKcgqhj7AA90CM9t/j8Xr+vKZW08Gue0O/jfZ/n/R3fGg73+dHE2ogzxEvEU4SPMBJxYon4kZjUnnc0QxOBD4j3iX40txjxKfEhUdqNwJPE58SwWmAwoFQqoVQsolqtwmgyoa2tDWazGVtbW/X7FokJYrb+pbGB/DliTsiNRiPyuRw2YjEY+HF7fejqOQCHw4FcNotoOKyEZZ1mg0SQeKWe0Fz3PUBck3cGCepGDE6XG8dOnIQ/cBgutwcmnrpcLiEaieB2KIRfgj+jnd54fT5UKhWdZ1oTW2oUmFTkDEl8Y4OkAbx69jwOPn5IhaO76zH0dHfB7Xaj3WpFMpXGt1Pf4KOrV7Fy9x/4+wMUL+tcX2sitRxckkQJeTIRRy9J35h4B06nEz6vFyPDQ/D3HYKJ8W+0UGgeFyfexh93fqeIv94TKZCPdYH7RK/EVBJ54c23MHD0CXXiU2MvorPT3rSM7q7cw8ljo8xZFr79+xUH7SFxUDL0gpDLm81MBkcGj6qY2237cOrl1uRi4t3lK1dQYojy+Zz++gAxJgJj8iQlJyHo6e1VZTgy/Aw67a3JdXvt9GmcePZ5hjihSlszJTAg38QtIXY4nHC5nAgE+rEX28fED42MwGazq/LVS1cEOvQn8UKEfCy7Dmv7ngTyhbyKv4coFAr6a58IqNqShimyW1OpJOx2G/ZqaZatgRxWelKt1ippq9aGErdKpYzVlRWeprBngYeP1lApVxSMhhptZNuosDGpfy0t4vr31+rruaWtcWys3n9AL5LIpFOwWCz6v37bJmC1dnBBGqG5ORRK5V2RS1hv3gyqA/29/CcS8Q20tdfyN10/Kv5rdYZq9Pgoq6J1ktPpDG78NIP1cASRSBi/3rrFsWJRHKyYZS6Z2SZQZOzFi7Pj402Js9kcVu6tYmHhDuLJJDY3M5ia/Arh9Udwe7z6GL/cOOwQjUVxZvwchoaeRjyRxPztBczOztKzCr06rhovzW6PcYTH2VClYkkNuh++m8Yyc+fkINTIZ4gv/icgwy3HeXLp3fcQDM5ifW1dzReLxYwjA4Po4wiRNSazCSkKPFhdxfLiIkOVgsvjUZVIgRSpXq+/0b7k3wvyINlPcGMkGoHD3qlq2sLulubLMgxym0kIhahYLCgPbDabGt/ayRPabJvf6cJRLS4bBI2mSCgk1SKTRkaCsdOoiDVy+QFwUYZr443Wvat6JImcXC6f+tGinfYT4rOdtsnqKSkMfWS0MIOGtEZ8g7jebMO/AgwANr2XXAf8LaoAAAAASUVORK5CYII='
		);
		add_action( 'load-' . $settings, array( $this, 'connection_status_notice' ) );

		$all_settings = add_submenu_page(
			'wp_discourse_options',
			__( 'All Options', 'wp-discourse' ),
			__( 'All Options', 'wp-discourse' ),
			'manage_options',
			'wp_discourse_options'
		);
		add_action( 'load-' . $all_settings, array( $this, 'connection_status_notice' ) );

		$connection_settings = add_submenu_page(
			'wp_discourse_options',
			__( 'Connection', 'wp-discourse' ),
			__( 'Connection', 'wp-discourse' ),
			'manage_options',
			'connection_options',
			array( $this, 'connection_options_tab' )
		);
		add_action( 'load-' . $connection_settings, array( $this, 'connection_status_notice' ) );

		$publishing_settings = add_submenu_page(
			'wp_discourse_options',
			__( 'Publishing', 'wp-discourse' ),
			__( 'Publishing', 'wp-discourse' ),
			'manage_options',
			'publishing_options',
			array( $this, 'publishing_options_tab' )
		);
		add_action( 'load-' . $publishing_settings, array( $this, 'connection_status_notice' ) );

		$commenting_settings = add_submenu_page(
			'wp_discourse_options',
			__( 'Commenting', 'wp-discourse' ),
			__( 'Commenting', 'wp-discourse' ),
			'manage_options',
			'commenting_options',
			array( $this, 'commenting_options_tab' )
		);
		add_action( 'load-' . $commenting_settings, array( $this, 'connection_status_notice' ) );

		$configurable_text_settings = add_submenu_page(
			'wp_discourse_options',
			__( 'Text Content', 'wp-discourse' ),
			__( 'Text Content', 'wp-discourse' ),
			'manage_options',
			'text_content_options',
			array( $this, 'text_content_options_tab' )
		);
		add_action( 'load-' . $configurable_text_settings, array(
			$this,
			'connection_status_notice',
		) );

		$sso_settings = add_submenu_page(
			'wp_discourse_options',
			__( 'SSO', 'wp-discourse' ),
			__( 'SSO', 'wp-discourse' ),
			'manage_options',
			'sso_options',
			array( $this, 'sso_options_tab' )
		);
		add_action( 'load-' . $sso_settings, array( $this, 'connection_status_notice' ) );
	}

	/**
	 * Called to display the 'connection_options' tab.
	 */
	public function connection_options_tab() {
		if ( current_user_can( 'manage_options' ) ) {
			$this->options_page->display( 'connection_options' );
		}
	}

	/**
	 * Called to display the 'publishing_options' tab.
	 */
	public function publishing_options_tab() {
		if ( current_user_can( 'manage_options' ) ) {
			$this->options_page->display( 'publishing_options' );
		}
	}

	/**
	 * Called to display the 'commenting_options' tab.
	 */
	public function commenting_options_tab() {
		if ( current_user_can( 'manage_options' ) ) {
			$this->options_page->display( 'commenting_options' );
		}
	}

	/**
	 * Called to display the 'text_content_options' tab.
	 */
	public function text_content_options_tab() {
	    if ( current_user_can( 'manage_options' ) ) {
		$this->options_page->display( 'text_content_options' );
        }
	}

	/**
	 * Called to display the 'sso_options' tab.
	 */
	public function sso_options_tab() {
	    if ( current_user_can( 'manage_options' ) ) {
		$this->options_page->display( 'sso_options' );
        }
	}

	/**
	 * Adds notices to indicate the connection status with Discourse.
	 *
	 * This method is called by the `load-{settings_page_hook}` action - see https://codex.wordpress.org/Plugin_API/Action_Reference/load-(page).
	 */
	function connection_status_notice() {
		$tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : ''; // Input var okay.
		if ( ! $tab ) {
			$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : ''; // Input var okay.
		}

		$current_page = $tab ? $tab : $page;

		if ( ! DiscourseUtilities::check_connection_status() ) {

			if ( 'publishing_options' === $current_page || 'commenting_options' === $current_page || 'text_content_options' === $current_page || 'sso_options' === $current_page ) {
				add_action( 'admin_notices', array( $this, 'establish_connection' ) );
			} else {
				add_action( 'admin_notices', array( $this, 'disconnected' ) );
			}
		} else if ( 'connection_options' === $current_page || 'wp_discourse_options' === $current_page ) {
			add_action( 'admin_notices', array( $this, 'connected' ) );
		}
	}

	/**
	 * Outputs the markup for the 'disconnected' notice that is displayed on the 'connection_options' tab.
	 */
	function disconnected() {
		?>
        <div class="notice notice-warning is-dismissible">
            <p>
                <strong><?php esc_html_e( 'You are not connected to a Discourse forum. Please check your settings for \'Discourse URL\', \'API Key\', and \'Publishing username\'
				Also, make sure that your Discourse forum is online.', 'wp-discourse' ); ?></strong>
            </p>
        </div>
		<?php
	}

	/**
	 * Outputs the markup for the 'connected' notice that is displayed on the 'connection_options' tab.
	 */
	function connected() {
		?>
        <div class="notice notice-success is-dismissible">
            <p>
                <strong><?php esc_html_e( 'You are connected to Discourse!', 'wp-discourse' ); ?></strong>
            </p>
        </div>
		<?php
	}

	/**
	 * Outputs the markup for the 'establish_connection' notice that is displayed when a connection is
	 * not established on all tabs except 'connection_options'.
	 */
	function establish_connection() {
		?>
        <div class="notice notice-warning is-dismissible">
            <p>
                <strong><?php esc_html_e( 'You are not connected to a Discourse forum. To establish a connection
				navigate back to the \'Connection\' tab and check your settings.', 'wp-discourse' ); ?></strong>
            </p>
        </div>
		<?php
	}
}