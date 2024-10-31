<?php
/**
 * Scripts
 *
 * @package     RatingWP
 * @subpackage  Functions
 * @copyright   Copyright (c) 2021, RatingWP
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Load Scripts
 *
 * Enqueues the required scripts.
 *
 * @since 0.1
 * @return void
 */
function rawp_load_scripts() {

	$general_settings = (array) get_option( 'rawp_general_settings' );
	
	$script = '';
	$in_footer = true;

  	wp_register_script( 'ratingwp-script', false, null, null, $in_footer );
  	wp_add_inline_script( 'ratingwp-script', $script );
  	wp_enqueue_script( 'ratingwp-script' );

  	wp_enqueue_style( 'rawp-frontend-style', RAWP_PLUGIN_URL . 'assets/css/frontend.css' );

  	wp_enqueue_style( 'dashicons' );

  	$config_array = [
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'ajax_nonce' => wp_create_nonce( RAWP_SLUG .'-nonce' ),
    	'rest_url' => esc_url_raw( rest_url() ),
    	'rest_nonce' => wp_create_nonce( 'wp_rest' ),
    	'user_id' => get_current_user_id(),
    	'strings' => [],
	];

	wp_enqueue_script( 'rawp-frontend-script', RAWP_PLUGIN_URL . 'assets/js/frontend.js', array('jquery' ), RAWP_VERSION, true );
	wp_localize_script( 'rawp-frontend-script', 'rawp_frontend_data', $config_array );

	
}
add_action( 'wp_enqueue_scripts', 'rawp_load_scripts' );


/**
 * Load Admin Scripts
 *
 * Enqueues the required admin scripts.
 *
 * @since 0.1
 * @return void
 */
function rawp_load_admin_scripts() {

	$current_screen = get_current_screen();
	if ( function_exists( 'is_gutenberg_page' ) && is_gutenberg_page() || method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) {
		rawp_load_scripts();
	}

	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'wp-color-picker' );

	wp_enqueue_style( 'rawp-admin-style', RAWP_PLUGIN_URL . 'assets/css/admin.css' );
	wp_enqueue_style( 'rawp-admin-style', RAWP_PLUGIN_URL . 'assets/css/frontend.css' );

	$config_array = [
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'admin_url' => admin_url(),
		'ajax_nonce' => wp_create_nonce( RAWP_SLUG .'-nonce' ),
		'strings' => [
			'sample_option_text' => __( 'Sample option', 'ratingwp' )
		]
	];

	wp_enqueue_script( 'rawp-admin-script', RAWP_PLUGIN_URL . 'assets/js/admin.js', array('jquery', 'jquery-ui-core', 'jquery-ui-sortable' ), RAWP_VERSION, true );
		wp_localize_script( 'rawp-admin-script', 'rawp_admin_data', $config_array );

}
add_action( 'admin_enqueue_scripts', 'rawp_load_admin_scripts' );
