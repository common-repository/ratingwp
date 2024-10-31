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
/**
 * Helper function which returns page type
 */
function rawp_get_page_type() {
    global $wp_query;
    $page_type = null;

    if ( $wp_query->is_page ) {
        $page_type = is_front_page() ? 'front' : 'page';
    } elseif ( $wp_query->is_home ) {
        $page_type = 'home';
    } elseif ( $wp_query->is_single ) {
        $page_type = ( $wp_query->is_attachment ) ? 'attachment' : 'single';
    } elseif ( $wp_query->is_category ) {
        $page_type = 'category';
    } elseif ( $wp_query->is_tag ) {
        $page_type = 'tag';
    } elseif ( $wp_query->is_tax ) {
        $page_type = 'tax';
    } elseif ( $wp_query->is_archive ) {
        if ( $wp_query->is_day ) {
            $page_type = 'day';
        } elseif ( $wp_query->is_month ) {
            $page_type = 'month';
        } elseif ( $wp_query->is_year ) {
            $page_type = 'year';
        } elseif ( $wp_query->is_author ) {
            $page_type = 'author';
        } else {
            $page_type = 'archive';
        }
    } elseif ( $wp_query->is_search ) {
        $page_type = 'search';
    } elseif ( $wp_query->is_404 ) {
        $page_type = 'notfound';
    }

    return $page_type;
}


/**
 * Returns IP address of user
 */
function rawp_get_user_ip_address() {
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                        return $ip;
                    }
                }
            }
    }
}


/**
 * Checks for duplicate form entry submissions based on browser cookies and
 * hashed IP address methods
 */
function rawp_duplicate_check( $form_entry ) {

    $general_settings = (array) get_option( 'rawp_general_settings' );

    $cookie_name = 'ratingwp-' . intval( $form_entry['form_id'] ) . '-' . $form_entry['subject_type'] 
        . '-' . $form_entry['subject_id'] . '-' . intval( $form_entry['user_id'] );

    if ( in_array( 'cookie', $general_settings['duplicate_check_methods'] ) && isset( $_COOKIE[$cookie_name] ) ) {
        return false;
    }

    if ( in_array( 'hashed_ip_address', $general_settings['duplicate_check_methods'] ) && rawp_ip_address_duplicate_check( $form_entry ) ) {
        return false;
    }

    return true;
}


/**
 * Returns name of subject type
 */
function rawp_get_subject_type_name( $subject_type, $subject_sub_type ) {
    
    $subject_type_name = __( 'Subject', 'ratingwp' ); 

    if ( $subject_type === 'post' ) {
    
        $post_type = get_post_type_object( $subject_sub_type );
        $subject_type_name = $post_type->labels->singular_name;
    
    } else if ( $subject_type === 'taxonomy' ) {
    
        $taxonomy = get_taxonomy( $subject_sub_type );
        $subject_type_name = $taxonomy->labels->singular_name;
    
    } else if ( $subject_type === 'user' ) {
    
        $subject_type_name = __( 'User', 'ratingwp' );
    
    }

    return apply_filters( 'rawp_subject_type_name', $subject_type_name, $subject_type, $subject_sub_type );
}