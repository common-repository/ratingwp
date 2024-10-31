<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * RAWP_Submissions_Table class
 * @author RatingWP
 *
 */
class RAWP_Submissions_Table extends WP_List_Table {

	/**
	 * Constructor
	 */
	function __construct() {

		parent::__construct( array(
				'singular'=> __( 'Submission', 'ratingwp' ),
				'plural' => __( 'Submissions', 'ratingwp' ),
				'ajax'	=> false
		) );
	}

	/**
	 * Retrieve the view types
	 *
	 * @access public
	 * @since 2.1.7
	 * @return array $views All the views available
	 */
	public function get_views() {

		$current = isset( $_GET['subject-type'] ) ? sanitize_key( $_GET['subject-type'] ) : 'post';
		$form_id  = isset( $_REQUEST['form-id'] ) && is_numeric( $_REQUEST['form-id'] ) ? intval( $_REQUEST['form-id'] ) : null;

		// choose the first form...
		if ( $form_id === null) {
			$forms = rawp_get_forms();
			if (count( $forms ) > 0 ) { // :)
				$form_id = $forms[0]->id;
			}
		}

		global $wpdb;
	    $query = '
	    	SELECT 
	        	COUNT(*)
	        FROM ' . $wpdb->prefix . 'rawp_form_entry 
	        WHERE 
	           	form_id = %d
	           	AND subject_type = "post"';
	    $post_count = $wpdb->get_var( $wpdb->prepare( $query, $form_id ) );


		$query = '
	    	SELECT 
	        	COUNT(*)
	        FROM ' . $wpdb->prefix . 'rawp_form_entry 
	        WHERE 
	           	form_id = %d
	           	AND subject_type = "taxonomy"';

		$taxonomy_count = $wpdb->get_var( $wpdb->prepare( $query, $form_id ) );

		$query = '
	    	SELECT 
	        	COUNT(*)
	        FROM ' . $wpdb->prefix . 'rawp_form_entry 
	        WHERE 
	           	form_id = %d
	           	AND subject_type = "user"';
		$user_count  = $wpdb->get_var( $wpdb->prepare( $query, $form_id ) );

		$views = array(
				'post'=> sprintf( '<a href="%s"%s>%s</a>', add_query_arg( array( 'subject-type' => 'post', 'paged' => FALSE ) ), $current === 'post' ? ' class="current"' : '', __( 'Post', 'ratingwp' ) . '&nbsp;<span class="count">(' . esc_html( $post_count ) . ')</span>' ),
				'taxonomy_count' => sprintf( '<a href="%s"%s>%s</a>', add_query_arg( array( 'subject-type' => 'taxonomy', 'paged' => FALSE ) ), $current === 'taxonomy' ? ' class="current"' : '', __( 'Taxonomy', 'ratingwp' ) . '&nbsp;<span class="count">(' . esc_html( $taxonomy_count ) . ')</span>' ),
				'user_count' => sprintf( '<a href="%s"%s>%s</a>', add_query_arg( array( 'subject-type' => 'user', 'paged' => FALSE ) ), $current === 'user' ? ' class="current"' : '', __( 'User', 'ratingwp' ) . '&nbsp;<span class="count">(' . esc_html( $user_count ) . ')</span>' )
		);

		return $views;
	}


	/**
	 * (non-PHPdoc)
	 * @see WP_List_Table::extra_tablenav()
	 */
	function extra_tablenav( $which ) {

		if ( $which == 'top' ){

			$form_id = '';
			if ( isset( $_REQUEST['form-id'] ) && is_numeric( $_REQUEST['form-id'] ) ) {
				$form_id = intval( $_REQUEST['form-id'] );
			}

			$subject_type = 'post';
			if ( isset( $_REQUEST['subject-type'] ) ) {
				$subject_type = sanitize_key( $_REQUEST['subject-type'] );
			}

			$subject_sub_type = '';
			if ( isset( $_REQUEST['subject-sub-type'] ) ) {
				$subject_sub_type = sanitize_key( $_REQUEST['subject-sub-type'] );
			}

			$forms = rawp_get_forms();
			?>
			<label><?php _e( 'Form', 'ratingwp' ); ?></label>
			<select name="form-id">
				<?php
				foreach ( $forms as $temp_form ) { 
					?>
					<option value="<?php echo esc_attr( $temp_form->id ); ?>" <?php if ( $temp_form->id == $form_id ) echo 'selected="selected"'; ?>>
						<?php echo esc_html( $temp_form->name ); ?>
					</option>
					<?php
				}
				?>
			</select>

			<?php
			if ( $subject_type === 'post') {
				$args = array(
					'public'   => true,
					'show_ui' => true
				);
				$post_types = get_post_types( $args, 'objects' );
				?>
				<label><?php _e( 'Post Type', 'ratingwp' ); ?></label>
				<select name="subject-sub-type">
					<option value=""><?php _e( 'All posts', 'ratingwp' ); ?></option>
					<?php
					foreach ( $post_types as $slug => $post_type ) { 
						?>
						<option value="<?php echo esc_attr( $slug ); ?>" <?php if ( $slug == $subject_sub_type ) echo 'selected="selected"'; ?>>
							<?php echo esc_html( $post_type->labels->singular_name ); ?>
						</option>
						<?php
					}
					?>
				</select>
				<?php 
			} else if ( $subject_type === 'taxonomy') {
				$args = array(
					'public'   => true,
					'show_ui' => true
				);
				$taxonomies = get_taxonomies( $args, 'objects' );
				?>
				<label><?php _e( 'Taxonomy', 'ratingwp' ); ?></label>
				<select name="subject-sub-type">
					<option value=""><?php _e( 'All taxonomies', 'ratingwp' ); ?></option>
					<?php
					foreach ( $taxonomies as $slug => $taxonomy) { 
						?>
						<option value="<?php echo esc_attr( $slug ); ?>" <?php if ( $slug == $subject_sub_type ) echo 'selected="selected"'; ?>>
							<?php echo esc_html( $taxonomy->labels->singular_name ); ?>
						</option>
						<?php
					}
					?>
				</select>
				<?php
			}

			$result_type = '';
			if ( isset( $_REQUEST['result-type'] ) ) {
				$result_type = sanitize_key( $_REQUEST['result-type'] );
			}
			?>

			<label><?php _e( 'Rating Type', 'ratingwp' ); ?></label> <select name="result-type">
				<option value="score" <?php if ( $result_type == 'score' ) echo 'selected="selected"'; ?>><?php _e( 'Score', 'ratingwp' ); ?></option>
				<option value="star-rating" <?php if ( $result_type == 'star-rating' ) echo 'selected="selected"'; ?>><?php _e( 'Star Rating', 'ratingwp' ); ?></option>
				<option value="percentage" <?php if ( $result_type == 'percentage' ) echo 'selected="selected"'; ?>><?php _e( 'Percentage', 'ratingwp' ); ?></option>
			</select>

			<input type="submit" class="button" value="<?php _e( 'Filter', 'ratingwp' ); ?>"/>
			<?php

		}

		if ( $which == 'bottom' ){
			echo '';
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see WP_List_Table::get_columns()
	 */
	function get_columns() {

		$columns = array();

		if ( count( $this->get_bulk_actions() ) > 0 ) {
			$columns = array_merge( $columns, array(
				'cb' => '<input type="checkbox" />'
			) );
		}

		$subject_type = isset( $_GET['subject-type'] ) ? sanitize_key( $_GET['subject-type'] ) : 'post';
		$subject_sub_type_column_name = __( 'Sub Type', 'ratingwp' );
		if ( $subject_type === 'post' ) {
			$subject_sub_type_column_name = __( 'Post Type', 'ratingwp' );
		} else if ( $subject_type === 'taxonomy' ) {
			$subject_sub_type_column_name = __( 'Taxonomy', 'ratingwp' );
		}

		$columns = array_merge( $columns, array(
				'form-entry-id' => __( 'Entry Id', 'ratingwp' ),
				'subject-name' => __( 'Subject', 'ratingwp' ),
				'form-id' => __( 'Form', 'ratingwp' ),
				'subject-sub-type' => $subject_sub_type_column_name,
				'overall-rating' => __( 'Overall Rating', 'ratingwp' ),
				'user-id' => __( 'User', 'ratingwp' ),
				'entry-date' => __( 'Entry Date', 'ratingwp' ),
		) );

		return $columns;
	}

	/**
	 * (non-PHPdoc)
	 * @see WP_List_Table::prepare_items()
	 */
	function prepare_items() {

		global $wpdb;

		// Process any bulk actions first
		$this->process_bulk_action();

		$form_id  = isset( $_REQUEST['form-id'] ) && is_numeric( $_REQUEST['form-id'] ) ? intval( $_REQUEST['form-id'] ) : null;
		$sort_by = ( isset( $_REQUEST['sort-by'] )  && strlen( trim( $_REQUEST['sort-by'] ) ) > 0 ) ? sanitize_key( $_REQUEST['sort-by'] ) : null;
		$subject_type = isset( $_GET['subject-type'] ) ? sanitize_key( $_GET['subject-type'] ) : 'post';
		$subject_sub_type = isset( $_GET['subject-sub-type'] ) ? sanitize_key( $_GET['subject-sub-type'] ): null;
	
		$hidden = array( 'form-id');
		if ( $subject_type === 'user') {
			$hidden = array( 'subject-sub-type', 'form-id' );
		}

		// Register the columns
		$columns = $this->get_columns();

		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		// pagination
		$items_per_page = 25;
		$page_num = ! empty( $_GET['paged'] ) ? $_GET['paged'] : '';
		if ( empty( $page_num ) || ! is_numeric( $page_num ) || $page_num <= 0 ) {
			$page_num = 1;
		}
		$offset = 0;
		if ( ! empty( $page_num ) && ! empty( $items_per_page ) ) {
			$offset = ( $page_num -1 ) * $items_per_page;
		}

		// choose the first form...
		if ( $form_id === null) {
			$forms = rawp_get_forms();
			if (count( $forms ) > 0 ) { // :)
				$form_id = $forms[0]->id;
			}
		}

		$subject_form_ratings_list = rawp_get_subject_form_ratings( array(
				'form_id' => $form_id,
				'subject_type' => $subject_type,
				'subject_sub_type' => $subject_sub_type,
				'sort_by' => $sort_by,
				'limit' => $items_per_page,
				'offset' => $offset
		 ), false );

		global $wpdb;
	    $query = '
	    	SELECT 
	        	COUNT(*)
	        FROM ' . $wpdb->prefix . 'rawp_form_entry 
	        WHERE 
	           	form_id = %d
	           	AND subject_type = %s';
	    if ( $subject_sub_type ) {
	       	// TODO
	    }

	    $total_items = $wpdb->get_var( $wpdb->prepare( $query, $form_id, $subject_type ) );

		$total_pages = ceil( $total_items / $items_per_page );

		$this->set_pagination_args( array(
				'total_items' => $total_items,
				'total_pages' => $total_pages,
				'per_page' => $items_per_page
		) );

		$this->items = $subject_form_ratings_list;
	}

	/**
	 * Default column
	 * @param unknown_type $item
	 * @param unknown_type $column_name
	 * @return unknown|mixed
	 */
	function column_default( $item, $column_name ) {

		switch( $column_name ) {

			case 'cb' :
				return $item[$column_name];
				break;

			case 'user-id' : {
				if ( intval( $item['user_id'] ) === 0) {
					_e( 'Guest', 'ratingwp' );
				} else {
					$user_info = get_userdata( $item['user_id'] );
					echo esc_html( $user_info->display_name );
				}
				break;
			}

			case 'subject-sub-type' : {

				if ( $item['subject_type'] === 'post' ) {
					$post_type_object = get_post_type_object( $item['subject_sub_type'] );
					echo esc_html( $post_type_object->labels->singular_name ); 
				} else if ( $item['subject_type'] === 'taxonomy' ) {
					$taxonomy = get_taxonomy( $item['subject_sub_type'] );
					echo esc_html( $taxonomy->labels->singular_name ); 
				}
				break;
			}

			case 'form-entry-id' : {
				echo intval( $item['form_entry_id'] );
				break;
			}

			case 'subject-name' : {
				echo esc_html( $item['subject_name'] );
				break;
			}

			case 'form-id' : {
				$form = rawp_get_form( $item['form_id'] );
				if ( $form['name'] ) {
					echo esc_html( $form['name'] );
				} else {
					echo intval( $item['form_id'] );
				}
				break;
			}

			case 'entry-date' :
				echo date( get_option( 'date_format' ), strtotime( $item['form_entry_date'] ) );
				// 'F j, Y, g:i A'
				break;

			case 'overall-rating' :
				
				$result_type = 'score';
				if ( isset( $_REQUEST['result-type'] ) ) {
					$result_type = sanitize_key( $_REQUEST['result-type'] );
				}

				if ( $result_type === 'score') {
				
					echo number_format( $item['rating_score'] , 1) . ' ' . __( 'out of', 'ratingwp' ) . ' ' . intval( $item['max_rating_score'] );
				
				} else if ( $result_type === 'star-rating') {

					$value = ( $item['rating_score'] / $item['max_rating_score'] ) * 5;
					rawp_get_template_part( 'star-rating', null, true, array(
						'stars' => round( $value * 2 ) / 2,
						'primary_color' => 'inherit'
					) );
					echo number_format( $value, 1 ) . ' ' . __( 'out of 5', 'ratingwp' );
				
				} else {

					$percentage = ( $item['rating_score'] / $item['max_rating_score'] ) * 100;
					if ( $percentage == 100 ) {
						echo number_format( $percentage, 0 ) . '%';
					} else {
						echo number_format( $percentage, 1 ) . '%'; 
					}
				
				}
				
				break;

			default:
				return print_r( $item, true ) ;
		}
	}

	/**
	 * checkbox column
	 * @param unknown_type $item
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
				'<input type="checkbox" name="bulk_action[]" value="%s" />', $item['form_entry_id']
		);
	}

	/**
	 * (non-PHPdoc)
	 * @see WP_List_Table::get_bulk_actions()
	 */
	function get_bulk_actions() {

		$bulk_actions = array();

		$bulk_actions = array_merge( array(
				'delete' => __( 'Delete', 'ratingwp' )
		), $bulk_actions );

		return $bulk_actions;
	}

	/**
	 * Handles bulk actions: delete, approve and unapprove
	 */
	function process_bulk_action() {

		if ( ! isset( $_REQUEST['bulk_action'] ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return; // should not be here
		}

		global $wpdb;

		$checked = ( is_array( $_REQUEST['bulk_action'] ) ) ? (array) $_REQUEST['bulk_action'] : array( $_REQUEST['bulk_action'] );
		
		try {

			if ( $this->current_action() === 'delete' ) {
				$to_delete = implode( ',', $checked );
				$result = $wpdb->query( 'DELETE FROM ' . $wpdb->prefix . 'rawp_form_entry WHERE ID IN( ' . esc_sql( $to_delete  ). ' )' );
				echo '<div class="updated"><p>' . sprintf( __( '%d rows deleted.', 'ratingwp' ), $result ) . '</p></div>';
			}

		} catch ( Exception $e ) {
			echo '<div class="error"><p>' . sprintf( __( 'An error has occured. %s', 'rartingwp' ), $e->getMessage() ) . '</p></div>';
		}
	}

}
