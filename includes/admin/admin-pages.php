<?php
/**
 * Admin Pages
 *
 * @package     RatingWP 
 * @subpackage  Admin/Pages
 * @copyright   Copyright (c) 2021, RatingWP
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Creates an options page for plugin settings and links it to a global variable
 *
 * @since 0.1
 * @return void
 */
function rawp_add_menu() {

	add_menu_page( 
		__( 'RatingWP', 'ratingwp' ), // page_title
		__( 'RatingWP', 'ratingwp' ), // menu title
		'manage_options', // capability
		'rawp_forms', // menu_slug
		'rawp_forms_page', // callback function
		'dashicons-star-filled', 
		null // position
	);

	add_submenu_page( 
		'rawp_forms', // parent_slug
		__( 'Forms', 'ratingwp' ), // page_title
		__( 'Forms', 'ratingwp' ), // menu title
		'manage_options', // capability
		'rawp_forms', // menu_slug
		'rawp_forms_page' // callback function
	);

	add_submenu_page( 
		'rawp_forms', // parent_slug
		__( 'Submissions', 'ratingwp' ), // page_title
		__( 'Submissions', 'ratingwp' ), // menu title
		'manage_options', // capability
		'rawp_submissions', // menu_slug
		'rawp_submissions_page' // callback function
	);

	add_submenu_page( 
		'rawp_forms', // parent_slug
		__( 'Settings', 'ratingwp' ), // page_title
		__( 'Settings', 'ratingwp' ), // menu title
		'manage_options', // capability
		'rawp_settings', // menu_slug
		'rawp_settings_page' // callback function
	);
	
}
add_action( 'admin_menu', 'rawp_add_menu', 10 );


/**
 * Forms page
 */
function rawp_forms_page() {
	?>
	<div class="wrap rawp-admin-page">
		
		<?php
		$id = isset( $_GET['id'] ) && is_numeric( $_GET['id'] ) ? intval( $_GET['id'] ) : null;
		$action = isset( $_GET['action'] ) ? sanitize_key( $_GET['action'] ) : null;
		if ( ! ( $action == 'new' || $action == 'edit' || $action == 'delete' ) ) {
			$action = null;
		}
		$nonce = isset( $_GET['nonce'] ) ? $_GET['nonce'] : null;

		if ( $nonce && ! wp_verify_nonce( $nonce ) ) {
			?>
			<p><?php _e( 'An error occured', 'ratingwp' ); ?></p>
			<?php
			return;
		}

		$forms_admin = new RAWP_Forms_Admin();

		if ( $action === 'new' || $action === 'edit') {

			$forms_admin->display_edit_page( $id );

		} else if ( $id && $action === 'delete' ) {

			$forms_admin->delete_form( $id );
			$forms_admin->display_view_page();

		} else {
			
			$forms_admin->display_view_page();
		}
		?>
	</div>
	<?php

}


/**
 * Forms page
 */
function rawp_submissions_page() {
	?>
	<div class="wrap rawp-admin-page">
		<h1 style="margin-bottom: 0px">
			<?php _e( 'Submissions', 'ratingwp' ); ?>
		</h1>

		<form method="get" id="rawp-submissions-table-form" action="<?php echo admin_url( 'admin.php?page=rawp_submissions' ); ?>">
			<?php
			$submissions_table = new RAWP_Submissions_Table();
			$submissions_table->prepare_items();
			$submissions_table->views();
			$submissions_table->display();

			$subject_type = 'post';
			if ( isset( $_REQUEST['subject-type'] ) ) {
				$subject_type = sanitize_key( $_REQUEST['subject-type'] );
			}
			$page_num = ! empty( $_GET['paged'] ) && is_numeric( $_GET['paged'] ) ? intval( $_GET['paged'] ) : '';

			?>
			<input type="hidden" name="page" value="rawp_submissions" />
			<input type="hidden" name="paged" value="<?php echo esc_attr( $page_num ); ?>" />
			<input type="hidden" name="subject-type" value="<?php echo esc_attr( $subject_type ); ?>" />

		</form>
	</div>
	<?php

}

/**
 * Settings Page
 *
 * Renders the settings page contents.
 *
 * @since 1.0
 * @return void
 */
function rawp_settings_page() {
	$page = 'ratingwp';

	?>
	<div class="wrap rawp-admin-page">
		<h1>
			<!-- <img src="" class="rawp-logo" height="23px" / > -->
			<?php _e( 'Settings', 'ratingwp' ); ?>
		</h1>

		<?php 
		if ( isset( $_GET['updated'] ) && isset( $_GET['page'] ) ) {
			add_settings_error( 'general', 'settings_updated', __('Settings saved.', 'ratingwp' ), 'updated' );
		}

		settings_errors();

		?>
		<form method="post" name="rawp_general_settings" action="options.php">
			<?php
			wp_nonce_field( 'update-options' );
			settings_fields( 'rawp_general_settings' );
			do_settings_sections( 'rawp_settings' );
			submit_button( null, 'primary', 'submit', true, null);
			?>
		</form>
	</div>
	<?php
}