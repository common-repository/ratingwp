<?php
/**
 * Registers REST API routes
 */
add_action( 'rest_api_init', function () {
	
	/**
	 * Route for forms
	 */
	register_rest_route( 'ratingwp/v1', '/forms', array(
		'methods' => WP_REST_Server::READABLE,
		'callback' => 'rawp_rest_api_get_forms',
		'permission_callback' =>  function () { // user must be logged in
			return apply_filters( 'rawp_save_form_entry_permission_callback', current_user_can( 'read' ) );
		}
	) );

	/*
	 * Route for form entry
	 */
	register_rest_route( 'ratingwp/v1', '/forms/(?P<formId>[\d]+)/entry', array(
		array(
			'methods' => WP_REST_Server::CREATABLE,
			'callback' => 'rawp_rest_api_save_form_entry',
			'args' => array(
				'formId' => array(
					'type' => 'integer',
					'description' => __( 'Form ID', 'ratingwp' ),
					'validate_callback' => function($param, $request, $key) {
						return is_numeric( $param );
					}
				),
				'subjectType' => array(
					'required' => true,
					'type' => 'string',
					'description' => __( 'Subject Type', 'ratingwp' ),
					'enum' => array( 'post', 'taxonomy', 'user' )
				),
				'subjectId' => array(
					'description' => __( 'Subject Id', 'ratingwp' ),
					'type' => 'string'
				),
				'criteria' => array(
					'description' => __( 'Criteria', 'ratingwp' ),
					'type' => 'array',
					'validate_callback' => function($param, $request, $key) {
						return true;
					},
				)
			),
			'permission_callback' => '__return_true'
		),
	) );
	
} );


/**
 * REST API callback for saving form entry
 */
function rawp_rest_api_save_form_entry( WP_REST_Request $request ) {

	$general_settings = (array) get_option( 'rawp_general_settings' );

	$form_id = $request['formId'];

	$form = rawp_get_form( $form_id );
	if ( $form === null ) {
		return new WP_Error( 'form_not_found', 'Form not found', array( 'status' => 404 ) );
	}

	// validation
	if ( ! ( is_array( $request['criteriaItems'] ) && is_numeric( $request['subjectId'] ) ) ) {

		return rest_ensure_response( array( 
			'success' => false, 
			'data' => array( 
				'message' => __( 'Validation failed.', 'ratingwp' )
			) 
		) );
	}
	if ( is_array( $request['criteriaItems']) ) {
		foreach ( $request['criteriaItems'] as $criteria ) {
        	if ( ! ( is_numeric( $criteria['id'] ) && is_numeric( $criteria['value'] ) ) ) {
        		return rest_ensure_response( array( 
					'success' => false, 
					'data' => array( 
						'message' => __( 'Validation failed.', 'ratingwp' )
					) 
				) );
        	}
        }
	}

	$user_id = get_current_user_id();

	$form_entry = [
		'form_id' => $form_id,
		'subject_id' => intval( $request['subjectId'] ),
		'subject_type' => $request['subjectType'],
		'criteria_items' => $request['criteriaItems'],
		'user_id' => $user_id,
		'entry_date' => current_time( 'mysql' ),
		'hashed_ip_address' => hash( 'sha256', rawp_get_user_ip_address() )
	];

	$form_entry_id = null;

	if ( $user_id > 0 ) {
		
		/**
		 * For logged in users, check a form entry does not already exist
	 	*/
		if ( ! rawp_user_form_entry_exists( $form_entry ) ) { 
			$form_entry_id = rawp_save_form_entry( $form_entry );
		} else {
			return rest_ensure_response( array( 
				'success' => false, 
				'data' => array( 
					'message' => $general_settings['user_form_entry_exists']
				) 
			) );
		}

	} else {
		
		/*
		 * For guests, check for duplicates either by IP address and or cookie validation
		 */
		if ( rawp_duplicate_check( $form_entry ) ) {
			$form_entry_id = rawp_save_form_entry( $form_entry );
		} else {
			return rest_ensure_response( array( 
				'success' => false, 
				'data' => array( 
					'message' => $general_settings['duplicate_message']
				) 
			) );
		}
	}

	/**
	 * Success
	 */
	if ( $form_entry_id ) {
		if ( in_array( 'cookie', $general_settings['duplicate_check_methods'] ) && ! headers_sent() ) {
			rawp_set_cookie( $form_entry );
		}
		return rest_ensure_response( array( 
			'success' => true, 
			'data' => array( 
				'message' => $general_settings['success_message']
			) 
		) );
	}

}
/**
 *
 */
function rawp_set_cookie( $form_entry ) {
	// 1 week default cookie expiry
	$time_limit = apply_filters( 'rawp_form_entry_duplicate_check_cookie_expiry', 7 * 24 * 60 * 60 );
	$cookie_name = 'ratingwp-' . $form_entry['form_id'] . '-' . $form_entry['subject_type'] 
		. '-' . $form_entry['subject_id'] . '-' . $form_entry['user_id'];
			
	setcookie( $cookie_name, true, time() + $time_limit, COOKIEPATH, COOKIE_DOMAIN, false, true );
}


/**
 * REST API callback for getting forms. This is used in the Gutenberg editor.
 */
function rawp_rest_api_get_forms( WP_REST_Request $request ) {
  
 	$forms = rawp_get_forms();
 	if (empty( $forms ) ) {
 		return new WP_Error( 'no_author', 'Invalid author', array( 'status' => 404 ) );
 	}

 	return rest_ensure_response( $forms );
}