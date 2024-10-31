<?php

/**
 * Gutenberg class. Registers plugin in Gutenberg Editor sidebar and registers 
 * blocks
 * 
 * @author RatingWP
 *
 */
class RAWP_Gutenberg {

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'set_script_translations' ) );
		add_action( 'init', array( $this, 'register_blocks' ) );
		add_action( 'init', array( $this, 'register_post_meta' ) );
	}

	/**
	 * Adds support for script language translations
	 */
	public function set_script_translations() {
		wp_set_script_translations( 'rawp-gutenberg-plugin-script', 'ratingwp' );
    	wp_set_script_translations( 'rawp-gutenberg-blocks-script', 'ratingwp' );
	}

	/**
	 * Register blocks
	 */
	public function register_blocks() { 

		if ( ! function_exists( 'register_block_type' ) ) {
			// Gutenberg is not active.
			return;
		}

		$default_form_id = null;
		$forms = rawp_get_forms();
		if ( count( $forms ) > 0 ) {
			$default_form_id = $forms[0]->id;
		}
	 
		$general_settings = (array) get_option( 'rawp_general_settings' );

	    wp_register_script( 'rawp-gutenberg-blocks-script', plugins_url( '../build/index.js', __FILE__ ), array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-editor', 'wp-element', 'wp-api-fetch' ) );

	    register_block_type( 'ratingwp/rating-form', array(
	        'editor_script' => 'rawp-gutenberg-blocks-script',
	        'render_callback' => array( $this, 'rating_form_block_render' ),
	        'attributes' => [
	        	'formId' => [
					'type' => 'integer',
					'default' => $default_form_id
				],
				'useCurrentPostAsSubject' => [
					'type' => 'boolean',
					'default' => true
				],
				'subjectType' => [
					'type' => 'string',
					'default' => 'post'
				],
				'subjectSubType' => [
					'type' => 'string'
				],
				'subjectId' => [
					'type' => 'string'
				],
				'subjectSearch' => [
					'type' => 'string'
				],
				'primaryColor' => [
					'type' => 'string',
					'default' => $general_settings['default_primary_color']
				],
			]
	    ) );

	    register_block_type( 'ratingwp/rating-summary', array(
	        'editor_script' => 'rawp-gutenberg-blocks-script',
	        'render_callback' => array( $this, 'rating_summary_block_render' ),
	        'attributes' => [
	        	'formId' => [
					'type' => 'integer',
					'default' => $default_form_id
				],
				'useCurrentPostAsSubject' => [
					'type' => 'boolean',
					'default' => true
				],
				'subjectType' => [
					'type' => 'string',
					'default' => 'post'
				],
				'subjectSubType' => [
					'type' => 'string',
					'default' => 'post'
				],
				'subjectId' => [
					'type' => 'string'
				],
				'header' => [
					'type' => 'string',
					'default' => 'h1'
				],
				'primaryColor' => [
					'type' => 'string',
					'default' => $general_settings['default_primary_color']
				],
				'layout' => [
					'type' => 'string',
					'default' => 'overall'
				],
				'subjectSearch' => [
					'type' => 'string'
				],
				'resultType' => [
					'type' => 'string',
					'default' => 'score'
				],
				'textAlign' => [
					'type' => 'string',
					'default' => 'left'
				]
			]
	    ) );

	    register_block_type( 'ratingwp/rating-list-table', array(
	        'editor_script' => 'rawp-gutenberg-blocks-script',
	        'render_callback' => array( $this, 'rating_list_table_block_render' ),
	        'attributes' => [
	        	'formId' => [
					'type' => 'integer',
					'default' => $default_form_id
				],
				'subjectType' => [
					'type' => 'string',
					'default' => 'post'
				],
				'subjectSubType' => [
					'type' => 'string',
					'default' => 'post'
				],
				'fixedWidthTableCells' => [
					'type' => 'boolean',
					'default' => false
				],
				'defaultStyle' => [
					'type' => 'string',
					'default' => 'default'
				],
				'showRank' => [
					'type' => 'boolean',
					'default' => true
				],
				'limit' => [
					'type' => 'integer',
					'default' => 10
				],
				'layout' => [
					'type' => 'string',
					'default' => 'table'
				],
				'showHeader' => [
					'type' => 'boolean',
					'default' => true
				],
				'primaryColor' => [
					'type' => 'string',
					'default' => $general_settings['default_primary_color']
				],
				'resultType' => [
					'type' => 'string',
					'default' => 'score'
				],
			]
	    ) );

	}

	/**
	 * Renders the rating form block
	 */
	public function rating_form_block_render( $attributes ) {

		$form_id = isset( $attributes['formId'] ) ? $attributes['formId'] : null;
		$subject_id = $attributes['useCurrentPostAsSubject'] ? get_the_ID() 
			: ( isset( $attributes['subjectId'] ) ? $attributes['subjectId'] : '' );
		$subject_type = isset( $attributes['subjectType'] ) ? $attributes['subjectType'] : null;
		$subject_sub_type = isset( $attributes['subjectSubType'] ) ? $attributes['subjectSubType'] : null;
		$primary_color = isset( $attributes['primaryColor'] ) ? $attributes['primaryColor'] : '#04AA6D';

		// validate block can display
		if ( $subject_id === '' || $form_id === null ) {
			// show notice in Gutenberg only
			if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
				return '<div class="components-notice is-warning is-dismissible"><div class="components-notice__content">' . __( 'Block cannot display because block settings are incomplete.', 'ratingwp' ) . '</div></div>';
			} else {
				return; // do not show...
			}
		}

		$form = rawp_get_form( $form_id );

		ob_start();
		rawp_get_template_part( 'rating-form', null, true, array(
			'form' => $form,
			'subject_type' => $subject_type,
			'subject_sub_type' => $subject_sub_type,
			'subject_id' => $subject_id,
			'primary_color' => $primary_color
		) );
		$html = ob_get_contents();
		ob_end_clean();

		$html = apply_filters( 'rawp_template_html', $html );

		return $html;

	}


	/**
	 * Renders the rating summary block
	 */
	public function rating_summary_block_render( $attributes ) {

		$form_id = isset( $attributes['formId'] ) ? $attributes['formId'] : null;
		$subject_id = $attributes['useCurrentPostAsSubject'] ? get_the_ID() 
			: ( isset( $attributes['subjectId'] ) ? $attributes['subjectId'] : null );
		$subject_type = isset( $attributes['subjectType'] ) ? $attributes['subjectType'] : null;
		$header = isset( $attributes['header'] ) ? $attributes['header'] : 'h1';
		$primary_color = isset( $attributes['primaryColor'] ) ? $attributes['primaryColor'] : '#04AA6D';
		$layout = $layout = apply_filters( 'rawp_rating_summary_layout', isset( $attributes['layout'] ) ? $attributes['layout'] : 'overall' );
		$result_type = isset( $attributes['resultType'] ) ? $attributes['resultType'] : 'score';
		$text_align = isset( $attributes['textAlign'] ) ? $attributes['textAlign'] : 'left';

		

		// validate block can display
		if ( $subject_id === '' || $form_id === null ) {
			// show notice in Gutenberg only
			if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
				return '<div class="components-notice is-warning is-dismissible"><div class="components-notice__content">' . __( 'Block cannot display because block settings are incomplete.', 'ratingwp' ) . '</div></div>';
			} else {
				return; // do not show...
			}
		}

		$subject_form_rating_summary = rawp_get_subject_form_ratings( array( 
		    'subject_id' => $subject_id, 
		    'subject_type' => $subject_type, 
		    'form_id' => $form_id ),
		    true );

		$form = rawp_get_form( $form_id );

		ob_start();
		rawp_get_template_part( 'rating-summary', $layout, true, array(
			'subject_form_rating_summary' => $subject_form_rating_summary,
			'form' => $form,
			'header' => $header,
			'primary_color' => $primary_color,
			'result_type' => $result_type,
			'text_align' => $text_align
		) );
		$html = ob_get_contents();
		ob_end_clean();

		$html = apply_filters( 'rawp_template_html', $html );

		return $html;
	}

	/**
	 * Renders the rating list table block
	 */
	public function rating_list_table_block_render( $attributes ) {

		$form_id = isset( $attributes['formId'] ) ? $attributes['formId'] : null;
		$subject_type = isset( $attributes['subjectType'] ) ? $attributes['subjectType'] : null;
		$subject_sub_type = isset( $attributes['subjectSubType'] ) ? $attributes['subjectSubType'] : null;
		$default_style = isset( $attributes['defaultStyle'] ) ? $attributes['defaultStyle'] : null;
		$fixed_width_table_cells = isset( $attributes['fixedWidthTableCells'] ) ? $attributes['fixedWidthTableCells'] : false;
		$show_rank = isset( $attributes['showRank'] ) ? $attributes['showRank'] : true;
		$limit = isset( $attributes['limit'] ) ? $attributes['limit'] : 10;
		$show_header = isset( $attributes['showHeader'] ) ? $attributes['showHeader'] : true;
		$layout = apply_filters( 'rawp_rating_list_layout', isset( $attributes['layout'] ) ? $attributes['layout'] : 'star-rating' );
		$primary_color = isset( $attributes['primaryColor'] ) ? $attributes['primaryColor'] : '#04AA6D';
		$result_type = isset( $attributes['resultType'] ) ? $attributes['resultType'] : 'score';

		// validate block can display
		if ( $subject_type === '' || $form_id === null ) {
			// show notice in Gutenberg only
			if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
				return '<div class="components-notice is-warning is-dismissible"><div class="components-notice__content">' . __( 'Block cannot display because block settings are incomplete.', 'ratingwp' ) . '</div></div>';
			} else {
				return; // do not show...
			}
		}

		$subject_header = rawp_get_subject_type_name( $subject_type, $subject_sub_type );

		$subject_form_rating_list = rawp_get_subject_form_ratings( array( 
		    'subject_type' => $subject_type,
		    'subject_sub_type'=> $subject_sub_type,
		    'form_id' => $form_id,
			'limit' => $limit,
			'offset' => 0
		    ), true );

		if ( is_array( $subject_form_rating_list ) ) {

			foreach ( $subject_form_rating_list as &$subject_form_rating_list_item) {
				
				if ( $subject_type === 'post') {

					// TODO get name and permalink
					$subject_form_rating_list_item['permalink'] = get_permalink( $subject_form_rating_list_item['subject_id'] );

				}
			}
		} else {
			$subject_form_rating_list = array();
		}

		ob_start();
		rawp_get_template_part( 'rating-list-table', $layout, true, array(
			'subject_form_rating_list' => $subject_form_rating_list,
			'fixed_width_table_cells' => $fixed_width_table_cells,
			'default_style' => $default_style,
			'show_rank' => $show_rank,
			'subject_sub_type' => $subject_type,
			'show_header' => $show_header,
			'primary_color' => $primary_color,
			'subject_header' => $subject_header,
			'result_type' => $result_type
		) );
		$html = ob_get_contents();
		ob_end_clean();

		$html = apply_filters( 'rawp_template_html', $html );

		return $html;
	}


	/*
	 * Registers post meta fields with REST API visibility
	 */
	public function register_post_meta() {

	}

}