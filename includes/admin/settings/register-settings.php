<?php
/**
 * Register Settings
 *
 * @package     RatingWP 
 * @subpackage  Admin/Settings
 * @copyright   Copyright (c) 2021, RatingWP
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


/**
 * Reister settings
 */
function rawp_register_settings() {

	register_setting( 'rawp_general_settings', 'rawp_general_settings', 'rawp_sanitize_general_settings' );
	
	add_settings_section( 'rawp_section_general', __( 'General', 'ratingwp' ), 'rawp_section_general_desc', 'rawp_settings' );
	add_settings_section( 'rawp_section_strings', __( 'Strings', 'ratingwp' ), 'rawp_section_strings_desc', 'rawp_settings' );
	add_settings_section( 'rawp_section_styles', __( 'Styles', 'ratingwp' ), 'rawp_section_styles_desc', 'rawp_settings' );

	$setting_fields = array(
		'duplicate_check_methods' => array(
			'title' 	=> __( 'Duplicate Check Methods', 'ratingwp' ),
			'callback' 	=> 'rawp_field_checkboxes',
			'page' 		=> 'rawp_settings',
			'section' 	=> 'rawp_section_general',
			'args' => array(
				'option_name' 	=> 'rawp_general_settings',
				'setting_id' 	=> 'duplicate_check_methods',
				'description' 	=> __( 'Choose methods to check for duplicate form entry.', 'ratingwp' ),
					'checkboxes' 	=> array(
						array(
							'name' => 'cookie',
							'label' => __( 'Cookie', 'ratingwp' )
						),
						array(
							'name' => 'hashed_ip_address',
							'label' => __( 'Hashed IP Address', 'ratingwp' )
						)
					)
			)
		),
		'success_message' => array(
			'title' 	=> __( 'Success Message', 'ratingwp' ),
			'callback' 	=> 'rawp_field_input',
			'page' 		=> 'rawp_settings',
			'section' 	=> 'rawp_section_strings',
			'args' => array(
				'option_name' 	=> 'rawp_general_settings',
				'setting_id' 	=> 'success_message',
				'label' 	=> __( 'Message to show after a successful form submission.', 'ratingwp' ),
				'type' 	=> 'text'
			)
		),
		'duplicate_message' => array(
			'title' 	=> __( 'Duplicate Message', 'ratingwp' ),
			'callback' 	=> 'rawp_field_input',
			'page' 		=> 'rawp_settings',
			'section' 	=> 'rawp_section_strings',
			'args' => array(
				'option_name' 	=> 'rawp_general_settings',
				'setting_id' 	=> 'duplicate_message',
				'label' 	=> __( 'Message to show when a duplicate check fails for a form submission.', 'ratingwp' ),
				'type' 	=> 'text'
			)
		),
		'user_form_entry_exists' => array(
			'title' 	=> __( 'Form Submission Exists', 'ratingwp' ),
			'callback' 	=> 'rawp_field_input',
			'page' 		=> 'rawp_settings',
			'section' 	=> 'rawp_section_strings',
			'args' => array(
				'option_name' 	=> 'rawp_general_settings',
				'setting_id' 	=> 'user_form_entry_exists',
				'label' 	=> __( 'Message to show when a form submission already exists.', 'ratingwp' ),
				'type' 	=> 'text'
			)
		),
		'default_primary_color' => array(
			'title' 	=> __( 'Default Primary Color', 'ratingwp' ),
			'callback' 	=> 'rawp_field_color_picker',
			'page' 		=> 'rawp_settings',
			'section' 	=> 'rawp_section_styles',
			'args' => array(
				'option_name' 	=> 'rawp_general_settings',
				'setting_id' 	=> 'default_primary_color'
			)
		)

	);

	foreach ( $setting_fields as $setting_id => $setting_data ) {
		// $id, $title, $callback, $page, $section, $args
		add_settings_field( $setting_id, $setting_data['title'], $setting_data['callback'], $setting_data['page'], $setting_data['section'], $setting_data['args'] );
	}
}

/**
 * Set default settings if not set
 */
function rawp_default_settings() {

	$general_settings = (array) get_option( 'rawp_general_settings' );

	// defaults
	$general_settings = array_merge( array(
		'duplicate_check_methods' => array( 'cookie' ),
		'success_message' => __( 'Thank you for your form submission.', 'ratingwp' ),
		'duplicate_message' => __( 'Duplicate check failed.', 'ratingwp' ),
		'user_form_entry_exists' => __( 'Form submission already exists.', 'ratingwp' ),
		'default_primary_color' => '#FDCC0D'
	), $general_settings );

	update_option( 'rawp_general_settings', $general_settings );

}

/**
 * Init settings
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
	add_action( 'admin_init', 'rawp_register_settings' );
	add_action( 'init', 'rawp_default_settings' );
}

/**
 * Sanitize general settings
 * @param 	$input
 */
function rawp_sanitize_general_settings( $input ) {

	if ( ! isset( $input['duplicate_check_methods'] ) ) {
		$input['duplicate_check_methods'] = array();
	}

	return $input;
}