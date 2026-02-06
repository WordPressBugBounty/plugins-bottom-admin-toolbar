<?php
/**
 * Plugin Name:       Bottom Admin Toolbar
 * Plugin URI:        https://wordpress.org/plugins/bottom-admin-toolbar/
 * Description:       Stick the WordPress admin bar to the bottom of the screen and hide it with a keyboard shortcut.
 * Version:           1.5.2
 * Requires at least: 3.0 or higher
 * Requires PHP:      5.6
 * Tested up to:      6.9
 * Stable tag:        1.5.2
 * Author:            M . Code
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Contributors:      M . Code
 * Donate link:       https://ko-fi.com/devloper
 */

if ( ! class_exists( 'BottomAdminToolbar' ) ) :
	/**
	 * BottomAdminToolbar
	 */
	class BottomAdminToolbar {
		/**
		 * Plugin version
		 *
		 * @var string
		 */
		const VERSION = '1.5.2';

		/**
		 * Constructor
		 */
		public function __construct() {
			define( 'BAB_PATH', plugin_dir_path( __FILE__ ) );
			define( 'BAB_ASSETS_URL', plugin_dir_url( __FILE__ ) . 'assets/' );
			define( 'BAB_BASENAME', plugin_basename( __FILE__ ) );
			add_theme_support( 'admin-bar', array( 'callback' => '__return_false' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_files' ) );
			add_action( 'admin_init', array( $this, 'register_settings_section' ) );
			add_action( 'admin_menu', array( $this, 'register_submenu_page' ) );

			// Check if admin bar should be shown in admin area
			if ( $this->should_show_in_admin() ) {
				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_files' ) );
			}
		}

		/**
		 * Check if admin bar should be shown in admin area
		 *
		 * @return bool
		 */
		private function should_show_in_admin() {
			$options = get_option( 'bab_show_in_admin' );
			if ( ! is_array( $options ) || empty( $options ) ) {
				return false;
			}
			$value = reset( $options );
			return $value === 'yes';
		}

		/**
		 * Enqueue custom files
		 */
		public function enqueue_files() {
			if ( is_admin_bar_showing() ) {
				wp_enqueue_style( 'bab-css', BAB_ASSETS_URL . 'bab.css', array(), self::VERSION, 'all' );
				wp_enqueue_script( 'bab-js', BAB_ASSETS_URL . 'bab.js', array(), self::VERSION, true );
			}
		}

		/**
		 * Register settings section
		 */
		public function register_settings_section() {

			/**
			 * Register setting with sanitization
			 */
			register_setting(
				'bottom-admin-bar',
				'bab_show_in_admin',
				array(
					'sanitize_callback' => array( $this, 'sanitize_bab_show_in_admin' ),
				)
			);

			/**
			 * Register setting section
			 */
			add_settings_section(
				'bab_settings_section',
				false,
				false,
				'bottom-admin-bar'
			);

			/**
			 * Register setting field
			 */
			add_settings_field(
				'bab_show_in_admin',
				__( 'Activer dans l\'administration', 'bottom-admin-toolbar' ),
				array( $this, 'bab_show_in_admin_cb' ),
				'bottom-admin-bar',
				'bab_settings_section',
				array(
					'label_for'       => 'bab_show_in_admin',
					'bab_custom_data' => 'custom',
				)
			);
		}

		/**
		 * Sanitize setting value
		 *
		 * @param array $input Input value.
		 * @return array Sanitized value.
		 */
		public function sanitize_bab_show_in_admin( $input ) {
			if ( ! is_array( $input ) ) {
				return array();
			}
			$sanitized = array();
			foreach ( $input as $key => $value ) {
				$sanitized[ sanitize_key( $key ) ] = ( 'yes' === $value ) ? 'yes' : 'no';
			}
			return $sanitized;
		}

		/**
		 * Show html output
		 */
		public function bab_show_in_admin_cb( $args ) {
			$options     = get_option( 'bab_show_in_admin', array() );
			$field_value = isset( $options[ $args['label_for'] ] ) ? $options[ $args['label_for'] ] : 'no';
			?>
			<select id="<?php echo esc_attr( $args['label_for'] ); ?>" data-custom="<?php echo esc_attr( $args['bab_custom_data'] ); ?>" name="bab_show_in_admin[<?php echo esc_attr( $args['label_for'] ); ?>]">
				<option value="yes" <?php selected( $field_value, 'yes' ); ?>>
					<?php esc_html_e( 'Oui', 'bottom-admin-toolbar' ); ?>
				</option>
				<option value="no" <?php selected( $field_value, 'no' ); ?>>
					<?php esc_html_e( 'Non', 'bottom-admin-toolbar' ); ?>
				</option>
			</select>
			<?php
		}

		/**
		 * Register sub menu page
		 */
		public function register_submenu_page() {
			add_submenu_page(
				'options-general.php',
				'Bottom Admin Toolbar',
				'Bottom Admin Toolbar',
				'manage_options',
				'bab-settings',
				array( $this, 'bab_settings_display' )
			);
		}

		/**
		 * Display settings
		 */
		public function bab_settings_display() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			settings_errors( 'wporg_messages' );
			?>
			<div class="wrap">
				<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
				<form action="options.php" method="post">
					<?php
					settings_fields( 'bottom-admin-bar' );
					do_settings_sections( 'bottom-admin-bar' );
					submit_button( 'Sauvegarder' );
					?>
				</form>
			</div>
			<?php
		}

	}
	new BottomAdminToolbar();
endif;
