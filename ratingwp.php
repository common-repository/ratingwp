<?php
/**
 * Plugin Name: RatingWP - Rating System for WordPress
 * Plugin URI: https://ratingwp.com
 * Description: A fully fledged rating system for WordPress. Add product reviews, voting competitions, polls, contests and collect user feedback.
 * Author: RatingWP
 * Author URI: https://ratingwp.com
 * Version: 1.0
 * Text Domain: ratingwp
 * Domain Path: languages
 *
 * RatingWP is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * RatingWP is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Easy Digital Downloads. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package     RatingWP 
 * @author 		RatingWP
 * @version		1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'RatingWP' ) ) :


/**
 * Main RatingWP Class.
 *
 * @since 1.0
 */
final class RatingWP {

	/** Singleton *************************************************************/

	/**
	 * @var RatingWP The one true RatingWP
	 * @since 1.0
	 */
	private static $instance;

	/**
	 * Used to identify multiple chatbots on the same page...
	 */
	public static $sequence = 0;


	/**
	 * Main RatingWP Instance.
	 *
	 * Insures that only one instance of RatingWP exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since 1.0
	 * @static
	 * @staticvar array $instance
	 * @uses RatingWP::setup_constants() Setup the constants needed.
	 * @uses RatingWP::includes() Include the required files.
	 * @uses RatingWP::load_textdomain() load the language files.
	 * @see RatingWP ()
	 * @return object|RatingWP The one true RatingWP
	 */
	public static function instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof RatingWP ) ) {

			self::$instance = new RatingWP;
			self::$instance->setup_constants();

			add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );

			self::$instance->includes();

			// instantiate classes
			$gutenberg = new RAWP_Gutenberg();
		}
		return self::$instance;
	}

	/**
	 * Throw error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since 1.0
	 * @access protected
	 * @return void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'ratingwp' ), '1.6' );
	}

	/**
	 * Disable unserializing of the class.
	 *
	 * @since 1.0
	 * @access protected
	 * @return void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'ratingwp' ), '1.6' );
	}

	/**
	 * Setup plugin constants.
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function setup_constants() {

		// Plugin version.
		if ( ! defined( 'RAWP_VERSION' ) ) {
			define( 'RAWP_VERSION', '1.1' );
		}

		// Plugin slug.
		if ( ! defined( 'RAWP_SLUG' ) ) {
			define( 'RAWP_SLUG', 'ratingwp' );
		}

		// Plugin Folder Path.
		if ( ! defined( 'RAWP_PLUGIN_DIR' ) ) {
			define( 'RAWP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Plugin Folder URL.
		if ( ! defined( 'RAWP_PLUGIN_URL' ) ) {
			define( 'RAWP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		// Plugin Root File.
		if ( ! defined( 'RAWP_PLUGIN_FILE' ) ) {
			define( 'RAWP_PLUGIN_FILE', __FILE__ );
		}
	}

	/**
	 * Include required files.
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function includes() {

		require_once RAWP_PLUGIN_DIR . 'includes/admin/settings/register-settings.php';
		require_once RAWP_PLUGIN_DIR . 'includes/template-functions.php';
		require_once RAWP_PLUGIN_DIR . 'includes/misc-functions.php';
		require_once RAWP_PLUGIN_DIR . 'includes/db-functions.php';
		require_once RAWP_PLUGIN_DIR . 'includes/scripts.php';
		require_once RAWP_PLUGIN_DIR . 'includes/rest-api.php';
		require_once RAWP_PLUGIN_DIR . 'includes/class-gutenberg.php';

		if ( is_admin() ) {
			require_once RAWP_PLUGIN_DIR . 'includes/admin/class-forms-admin.php';
			require_once RAWP_PLUGIN_DIR . 'includes/admin/admin-pages.php';
			require_once RAWP_PLUGIN_DIR . 'includes/admin/class-submissions-table.php';
			require_once RAWP_PLUGIN_DIR . 'includes/admin/settings/display-settings.php';
		}

	}

	/**
	 * Loads the plugin language files.
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function load_textdomain() {
		global $wp_version;

		// Set filter for plugin's languages directory.
		$rawp_lang_dir  = dirname( plugin_basename( RAWP_PLUGIN_FILE ) ) . '/languages/';
		$rawp_lang_dir  = apply_filters( 'rawp_languages_directory', $rawp_lang_dir );

		// Traditional WordPress plugin locale filter.

		$get_locale = get_locale();

		if ( $wp_version >= 4.7 ) {

			$get_locale = get_user_locale();
		}

		/**
		 * Defines the plugin language locale used.
		 *
		 * @var $get_locale The locale to use. Uses get_user_locale()` in WordPress 4.7 or greater,
		 *                  otherwise uses `get_locale()`.
		 */
		$locale        = apply_filters( 'plugin_locale',  $get_locale, 'ratingwp' );
		$mofile        = sprintf( '%1$s-%2$s.mo', 'ratingwp', $locale );

		// Look for wp-content/languages/myc/ratingwp-{lang}_{country}.mo
		$mofile_global1 = WP_LANG_DIR . '/myc/ratingwp-' . $locale . '.mo';

		// Look for wp-content/languages/myc/ratingwp-{lang}_{country}.mo
		$mofile_global2 = WP_LANG_DIR . '/myc/ratingwp-' . $locale . '.mo';

		// Look in wp-content/languages/plugins/ratingwp
		$mofile_global3 = WP_LANG_DIR . '/plugins/ratingwp/' . $mofile;

		if ( file_exists( $mofile_global1 ) ) {

			load_textdomain( 'ratingwp', $mofile_global1 );

		} elseif ( file_exists( $mofile_global2 ) ) {

			load_textdomain( 'ratingwp', $mofile_global2 );

		} elseif ( file_exists( $mofile_global3 ) ) {

			load_textdomain( 'ratingwp', $mofile_global3 );

		} else {

			// Load the default language files.
			load_plugin_textdomain( 'ratingwp', false, $rawp_lang_dir );
		}

	}

	/**
	 * Activates the plugin
	 */
	public static function activate_plugin() {

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		/*
		 * DB tables
		 */
		try {
			global $wpdb, $charset_collate;

			$query = 'CREATE TABLE ' . $wpdb->prefix . 'rawp_subject (
					id int(11) NOT NULL AUTO_INCREMENT,
					name varchar(200) NOT NULL,
					PRIMARY KEY  (id)
			) ' . $charset_collate;
			dbDelta( $query );

			$wpdb->show_errors();

			$query = 'CREATE TABLE ' . $wpdb->prefix . 'rawp_form (
					id int(11) NOT NULL AUTO_INCREMENT,
					name varchar(50) NOT NULL,
					PRIMARY KEY  (id)
			) ' . $charset_collate;
			dbDelta( $query );

			$wpdb->show_errors();

			$query = 'CREATE TABLE ' . $wpdb->prefix . 'rawp_criteria (
					id int(11) NOT NULL AUTO_INCREMENT,
					label varchar(255) NOT NULL,
					type varchar(20) NOT NULL,
					value int(11) NOT NULL DEFAULT 100,
					display_order int(11) NOT NULL,
					display varchar(10),
					PRIMARY KEY  (id)
			) ' . $charset_collate;
			dbDelta( $query );

			$query = 'CREATE TABLE ' . $wpdb->prefix . 'rawp_form_criteria  (
					form_id int(11) NOT NULL,
					criteria_id int(11) NOT NULL,
					PRIMARY KEY  (form_id, criteria_id)
			) ' . $charset_collate;
			dbDelta( $query );

			$wpdb->show_errors();

			$query = 'CREATE TABLE ' . $wpdb->prefix . 'rawp_criteria_lookup (
					id int(11) NOT NULL AUTO_INCREMENT,
					criteria_id int(11) NOT NULL,
					option_text varchar(50) NOT NULL,
					percentage_value decimal(10,2) DEFAULT 1.00,
					is_default_option tinyint(1) NOT NULL DEFAULT 0,
					PRIMARY KEY  (id)
			) ' . $charset_collate;
			dbDelta( $query );

			$wpdb->show_errors();

			$query = 'CREATE TABLE ' . $wpdb->prefix . 'rawp_criteria_numeric (
					id int(11) NOT NULL AUTO_INCREMENT,
					criteria_id int(11) NOT NULL,
					is_ascending tinyint(1) NOT NULL DEFAULT 1,
					min int(11) NOT NULL DEFAULT 0,
					max int(11) NOT NULL,
					default_input int(11) NOT NULL,
					PRIMARY KEY  (id)
			) ' . $charset_collate;
			dbDelta( $query );

			$wpdb->show_errors();

			$query = 'CREATE TABLE ' . $wpdb->prefix . 'rawp_criteria_star_rating (
					id int(11) NOT NULL AUTO_INCREMENT,
					criteria_id int(11) NOT NULL,
					out_of int(11) NOT NULL DEFAULT 5,
					PRIMARY KEY  (id)
			) ' . $charset_collate;
			dbDelta( $query );

			$wpdb->show_errors();

			$query = 'CREATE TABLE ' . $wpdb->prefix . 'rawp_form_entry  (
					id int(11) NOT NULL AUTO_INCREMENT,
					form_id int(11) NOT NULL,
					subject_type varchar(20) NOT NULL,
					subject_id int(11) NOT NULL,
					entry_date date NOT NULL,
					user_id int(11) NOT NULL,
					hashed_ip_address varchar(256) NOT NULL,
					PRIMARY KEY  (id)
			) ' . $charset_collate;
			dbDelta( $query );

			$wpdb->show_errors();

			/*
			 * Make sure at least one form exists on activation...
			 */
			$forms = rawp_get_forms();
 			
 			if (empty( $forms ) ) {
 				$form = array(
					'id' => null,
					'name' => __( 'Sample form', 'ratingwp' ),
					'criteria_items' => array(
						array(
							'type' => 'star-rating',
							'label' => __( 'Sample star rating', 'ratingwp' ),
							'id' => null,
							'value' => 20,
							'display_order' => 0,
							'out_of' => 5
						),
						array(
							'type' => 'lookup',
							'label' => __( 'Sample lookup', 'ratingwp' ),
							'id' => null,
							'value' => 20,
							'display_order' => 1,
							'lookup_options' => array(
								array(
									'id' => null,
									'percentage_value' => .5,
									'option_text' => __( 'Option 1', 'ratingwp' ),
									'is_default' => false
								),
								array(
									'id' => null,
									'percentage_value' => .75,
									'option_text' => __( 'Option 2', 'ratingwp' ),
									'is_default' => false
								),
								array(
									'id' => null,
									'percentage_value' => 1,
									'option_text' => __( 'Option 3', 'ratingwp' ),
									'is_default' => true
								)
							),
							'display' => 'select'
						)
					)
				);

				$form = rawp_create_update_form( $form );
 			}

		} catch ( Exception $e ) {
			// do nothing
		}
	}	

}

endif; // End if class_exists check.

/**
 * Checks whether function is disabled.
 *
 * @param string  $function Name of the function.
 * @return bool Whether or not function is disabled.
 */
function rawp_is_func_disabled( $function ) {
	$disabled = explode( ',',  ini_get( 'disable_functions' ) );

	return in_array( $function, $disabled );
}


/**
 * Activate plugin
 */
function rawp_activate_plugin() {

	if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
		RatingWP::activate_plugin();
	}
}
register_activation_hook( __FILE__, 'rawp_activate_plugin');

/**
 * The main function for that returns RatingWP
 *
 * The main function responsible for returning the one true RatingWP
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $rawp = RatingWP (); ?>
 *
 * @since 1.0
* @return object|RatingWP The one true RatingWP Instance.
 */
function RatingWP() {
	return RatingWP::instance();
}

// Get RatingWP Running.
RatingWP();
