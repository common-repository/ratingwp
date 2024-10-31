<?php
/**
 * Get forms
 */
function rawp_get_forms() {
    
    global $wpdb;
    $query = 'SELECT 
        f.id, 
        f.name, 
        ( 
            SELECT 
                COUNT(re.id) 
            FROM ' . $wpdb->prefix . 'rawp_form_entry re 
            WHERE re.form_id = f.id 
        )  AS form_submissions 
        FROM ' . $wpdb->prefix . 'rawp_form f';

    return $wpdb->get_results( $query );
}


/**
 * Create form
 */
function rawp_get_form( $id ) {

    if ($id === null) {
        return null;
    }
    
    global $wpdb;

    $query = $wpdb->prepare( 'SELECT id, name FROM ' . $wpdb->prefix . 'rawp_form WHERE id = %d', $id );
    
    $row = $wpdb->get_row( $query );

    if ( $row === null ) {
        return null;
    }

    $form = array(
        'id' => intval( $id ),
        'name' => $row->name,
        'criteria_items' => array()
    );

    $query = $wpdb->prepare( 'SELECT c.id, c.label, c.type, c.value, c.display FROM ' . $wpdb->prefix . 'rawp_form f INNER JOIN ' . $wpdb->prefix . 'rawp_form_criteria fc ON f.id = fc.form_id INNER JOIN ' . $wpdb->prefix . 'rawp_criteria c ON c.id = fc.criteria_id WHERE f.id = %d ORDER BY c.display_order ASC', $id );

    $results = $wpdb->get_results( $query );

    foreach ( $results as $row ) {
        
        $criteria = array(
            'id' => intval( $row->id ),
            'label' => $row->label,
            'value' => $row->value,
            'type' => $row->type,
            'display' => $row->display
        );

        if ( $criteria['type'] === 'lookup' ) { // lookup

            $query = $wpdb->prepare( 'SELECT id, option_text, percentage_value, is_default_option FROM ' . $wpdb->prefix . 'rawp_criteria_lookup WHERE criteria_id = %d', $row->id );
    
            $lookup_options_results = $wpdb->get_results( $query );

            $lookup_options = array();
            foreach ( $lookup_options_results as $lookup_option_row ) {

                // set array index = lookup option id for easy lookup
                $lookup_options[$lookup_option_row->id] = array( 
                    'id' => $lookup_option_row->id,
                    'option_text'=> $lookup_option_row->option_text,
                    'percentage_value' => $lookup_option_row->percentage_value,
                    'is_default' => intval( $lookup_option_row->is_default_option ) === 1 ? true : false
                );

            }

            $criteria = array_merge( $criteria, array( 
                'lookup_options' => $lookup_options
            ));

        } else if ( $criteria['type'] === 'numeric' ) { // numeric

            $query = $wpdb->prepare( 'SELECT is_ascending, min, max, default_input FROM ' . $wpdb->prefix . 'rawp_criteria_numeric WHERE criteria_id = %d', $row->id );
    
            $numeric_row = $wpdb->get_row( $query );
            
            $criteria = array_merge( $criteria, array( 
                'min' => $numeric_row->min,
                'max' => $numeric_row->max,
                'default' => $numeric_row->default_input,
                'is_ascending' => intval( $numeric_row->is_ascending ) === 1 ? true : false,
            ));

        } else {

            $query = $wpdb->prepare( 'SELECT out_of FROM ' . $wpdb->prefix . 'rawp_criteria_star_rating WHERE criteria_id = %d', $row->id );
    
            $numeric_row = $wpdb->get_row( $query );
            
            $criteria = array_merge( $criteria, array( 
                'out_of' => $numeric_row->out_of
            ));

        }

        // set array index = criteria id for easy lookup
        $form['criteria_items'][$row->id] = $criteria;
    }

    return $form;
}

/**
 * Delete form
 */
function rawp_delete_form( $id = null ) {
    
    global $wpdb;

    $results = $wpdb->delete(  
            $wpdb->prefix . 'rawp_form', array( 'id' => $id ), array( '%d' ) );
    return $results;
}


/**
 * Updates form
 */
function rawp_create_update_form( $form ) {

    global $wpdb, $charset_collate;

    $old_form = array();
    $old_criteria_items_not_updated = array();
    $criteria_added = false;

    if ( $form['id'] === null ) {

        $results = $wpdb->insert(  
            $wpdb->prefix . 'rawp_form', 
            array( 'name' => $form['name'] ), 
            array( '%s' ) 
        );

        $form['id'] = intval( $wpdb->insert_id );

    } else {
        
        $old_form = rawp_get_form( $form['id'] );
        $old_criteria_items_not_updated = $old_form['criteria_items']; // keep for later :)

        if ( $form['name'] !== $old_form['name']) {
        
            $data = [ 'name' => $form['name'] ];
            $format = [ '%s' ];
            $where = [ 'id' => $form['id'] ];
            $where_format = [ '%d' ];

            $results = $wpdb->update(  
                $wpdb->prefix . 'rawp_form', 
                $data,
                $where,
                $format,
                $where_format
            );
        }
    }

    /**
     * Criteria items
     */
    foreach ( $form['criteria_items'] as &$criteria ) { // pass by reference so we can add critiria id for later if added
        
        /**
         * Add criteria
         */
        if ( $criteria['id'] === null) {

            $wpdb->insert( 
                $wpdb->prefix . 'rawp_criteria', 
                array( 
                    'label' => $criteria['label'], 
                    'type' => $criteria['type'], 
                    'value' => $criteria['value'], 
                    'display_order' => $criteria['display_order'], 
                    'display' => isset( $criteria['display'] ) ? $criteria['display'] : null
                ),
                array( '%s', '%s', '%d', '%d', '%s' ) 
            );

            $criteria_id = intval( $wpdb->insert_id );
            $criteria['id'] = $criteria_id;

            if ( $criteria['type'] === 'numeric' ) { // Numeric

                $wpdb->insert( 
                    $wpdb->prefix . 'rawp_criteria_numeric', 
                    array( 
                        'criteria_id' => $criteria_id, 
                        'min' => $criteria['min'], 
                        'max' => $criteria['max'], 
                        'default_input' => $criteria['default'],
                        'is_ascending' => $criteria['is_ascending'] 
                    ),
                    array( '%d', '%d', '%d', '%d' ) 
                );

            } else if ( $criteria['type'] === 'lookup' ) { // Lookup

                foreach ( $criteria['lookup_options'] as $lookup_option ) {
                    
                    $wpdb->insert( 
                        $wpdb->prefix . 'rawp_criteria_lookup', 
                            array( 
                                'criteria_id' => $criteria_id, 
                                'option_text' => $lookup_option['option_text'], 
                                'percentage_value' => $lookup_option['percentage_value'] ,
                                'is_default_option' => $lookup_option['is_default']
                            ),
                        array( '%d', '%s', '%f' ) 
                    );
                }

            } else { // star rating

                $wpdb->insert( 
                    $wpdb->prefix . 'rawp_criteria_star_rating', 
                    array( 
                        'criteria_id' => $criteria_id, 
                        'out_of' => $criteria['out_of'], 
                    ),
                    array( '%d', '%d' ) 
                );

            }

            $wpdb->insert( 
                $wpdb->prefix . 'rawp_form_criteria', 
                array( 'form_id' => $form['id'], 'criteria_id' => $criteria_id ),
                array( '%d', '%d' ) 
            );

            $criteria_added = true;

        } else { 

            /** 
             * Update criteria
             */
            $criteria_id = $criteria['id'];

            $data = [ 
                'label' => $criteria['label'], 
                'value' => $criteria['value'], 
                'display_order' => $criteria['display_order'], 
                'display' => $criteria['display'] 
            ];
            $format = [ '%s', '%d', '%d', '%s' ];
            $where = [ 'id' => $criteria_id ];
            $where_format = [ '%d' ];

            $results = $wpdb->update(  
                $wpdb->prefix . 'rawp_criteria', 
                $data,
                $where,
                $format,
                $where_format
            );

            if ( $criteria['type'] === 'numeric' ) { // Numeric

                $data = [ 
                    'min' => $criteria['min'], 
                    'max' => $criteria['max'], 
                    'default_input' => $criteria['default'],
                    'is_ascending' => $criteria['is_ascending'] ];
                $format = [ '%d', '%d', '%d', '%d' ];
                $where = [ 'criteria_id' => $criteria_id ];
                $where_format = [ '%d' ];

                $results = $wpdb->update(  
                    $wpdb->prefix . 'rawp_criteria_numeric', 
                    $data,
                    $where,
                    $format,
                    $where_format
                );

            } else if ( $criteria['type'] === 'lookup' ) { // Lookup

                $old_lookup_options_not_updated = $old_form['criteria_items'][$criteria_id]['lookup_options'];

                foreach ( $criteria['lookup_options'] as $lookup_option ) {
                    
                    if ( $lookup_option['id'] === null ) { // Add

                        $wpdb->insert( 
                            $wpdb->prefix . 'rawp_criteria_lookup', 
                            array( 
                                'criteria_id' => $criteria_id, 
                                'option_text' => $lookup_option['option_text'], 
                                'percentage_value' => $lookup_option['percentage_value'],
                                'is_default_option' => $lookup_option['is_default'] 
                            ),
                            array( '%d', '%s', '%f', '%d' ) 
                        );

                    } else { // Update

                        $data = [ 
                            'option_text' => $lookup_option['option_text'], 
                            'percentage_value' => $lookup_option['percentage_value'],
                            'is_default_option' => $lookup_option['is_default']
                        ];
                        $format = [ '%s', '%f', '%d' ];
                        $where = [ 'id' => $lookup_option['id'], 'criteria_id' => $criteria_id ];
                        $where_format = [ '%d', '%d' ];

                        $results = $wpdb->update(  
                            $wpdb->prefix . 'rawp_criteria_lookup', 
                            $data,
                            $where,
                            $format,
                            $where_format
                        );

                        unset( $old_lookup_options_not_updated[$lookup_option['id']] );
                    }
                }

                /**
                 * Any remaining lookup options not updated must have been deleted
                 */
                if ( count( $old_lookup_options_not_updated ) > 0) {
                    $query = 'DELETE FROM ' . $wpdb->prefix . 'rawp_criteria_lookup WHERE id IN ( ' . implode( ',', array_keys( $old_lookup_options_not_updated ) ) . ' )';
                    $wpdb->query( $query );
                }

            } else { // star rating

                $data = [ 'out_of' => $criteria['out_of'] ];
                $format = [ '%d' ];
                $where = [ 'criteria_id' => $criteria_id ];
                $where_format = [ '%d' ];

                $results = $wpdb->update(  
                    $wpdb->prefix . 'rawp_criteria_star_rating', 
                    $data,
                    $where,
                    $format,
                    $where_format
                );

            } 

            unset( $old_criteria_items_not_updated[$criteria_id]);
        }

    }
    /**
     * Any remaining criteria items not updated must have been deleted
     */
    if ( count( $old_criteria_items_not_updated ) > 0) {
        $query = 'DELETE FROM ' . $wpdb->prefix . 'rawp_form_criteria WHERE criteria_id IN ( ' . implode( ',', array_keys( $old_criteria_items_not_updated ) ) . ' )';
        $wpdb->query( $query );
    }

    /**
     * Update form entry with any new criteria added
     */
    if ( $criteria_added ) {

        $criteria_columns = '';
        foreach ( $form['criteria_items'] as $temp_criteria ) {
            
            $temp_criteria_id = intval( $temp_criteria['id'] );
            $criteria_columns .= '
                    c_' . $temp_criteria_id . ' int(11) NOT NULL,';
        }

        $query = 'CREATE TABLE ' . $wpdb->prefix . 'rawp_form_entry  (
                id int(11) NOT NULL AUTO_INCREMENT,
                form_id int(11) NOT NULL,
                subject_type varchar(20) NOT NULL,
                subject_id int(11) NOT NULL,
                entry_date date NOT NULL,
                user_id int(11) NOT NULL,
                hashed_ip_address varchar(256) NOT NULL,' . $criteria_columns . '
                PRIMARY KEY  (id)
            ) ' . $charset_collate;

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $query );
    }
}


/**
 * Save form entry
 */
function rawp_save_form_entry( $form_entry ) {

    global $wpdb;

    $data = array( 
        'form_id' => $form_entry['form_id'], 
        'subject_type' => $form_entry['subject_type'], 
        'subject_id' => intval( $form_entry['subject_id'] ), 
        'user_id' => $form_entry['user_id'], 
        'entry_date' => $form_entry['entry_date'],
        'hashed_ip_address' => $form_entry['hashed_ip_address']
    );
    $format = array( '%d', '%s', '%d', '%d', '%s', '%s' );

    foreach ( $form_entry['criteria_items'] as $criteria ) {
        $criteria_id = intval( $criteria['id'] );
        $column_name = 'c_' . $criteria_id;
        $data = array_merge( $data, array( $column_name => $criteria['value'] ) );
        $format = array_merge( $format, array( '%d' ) );
    }

    $results = $wpdb->insert(  
        $wpdb->prefix . 'rawp_form_entry', 
        $data, 
        $format 
    );

    return intval( $wpdb->insert_id );
}


/**
 * Returns average value for subject form entry criteria
 *
 * @param criteria
 * @param is_aggregate
 */
function rawp_get_criteria_avg_query( $criteria, $is_aggregate ) {
    
    $criteria_id = intval( $criteria['id'] );
    $value = intval( $criteria['value'] );

    $query= '';

    if ( $criteria['type'] === 'lookup') {

        $query .= '
            AVG( 
                CASE WHEN ( fe.c_' . $criteria_id . ' < 0 ) THEN ';

        if ( $is_aggregate ) {
            $query .= 'NULL';
        } else {
            $query .= '0';
        }

        foreach ( $criteria['lookup_options'] as $lookup_option ) {
                    $query .= '
                WHEN fe.c_' . $criteria_id . ' = ' . intval( $lookup_option['id'] ) . ' THEN ' . $value . ' * ' . floatval( $lookup_option['percentage_value'] );
        }
        $query .= ' 
                END 
            )';
                
    } else if ( $criteria['type'] === 'numeric' ) {

        $min = intval( $criteria['min'] );
        $max = intval( $criteria['max'] );

        if ( $criteria['is_ascending'] ) {

            $query .= '
            AVG(
                IF ( ( fe.c_' . $criteria_id . ' < 0 ), ';

                if ( $is_aggregate ) {
                    $query .= 'NULL, ';
                } else {
                    $query .= '0, ';
                }

                $query .= '
                    IF ( ( fe.c_' . $criteria_id . ' - ' . $min . ' ) = 0 , 0,
                        IF ( fe.c_' . $criteria_id . ' >= ' . $max . ', ' . $value . ', ( ( fe.c_' . $criteria_id . ' - ' . $min . ' ) / ' . $max . ' - ' . $min . ' ) * ' . $value . ' ) 
                    )
                )
            )';

        } else {

            $query .= '
            (
                IF ( ( fe.c_' . $criteria_id . ' < 0 ), ';

                if ( $is_aggregate ) {
                    $query .= 'NULL, ';
                } else {
                    $query .= '0, ';
                }

                $query .= '
                    IF ( ( fe.c_' . $criteria_id . ' - ' . $min . ' ) <= 0 , ' . $value . ',
                        IF ( fe.c_' . $criteria_id . ' >= ' . $max . ', 0, ( 1 - ( ( fe.c_' . $criteria_id . ' - ' . $min . ' ) / ' . $max . ' - ' . $min . ' ) ) * ' . $value . ' ) 
                    )
                )
            )';
        }

    } else { // star rating

        $out_of = intval( $criteria['out_of'] );

        $query .= '
            AVG(
                IF ( fe.c_' . $criteria_id . ' < 0, ';

                if ( $is_aggregate ) {
                    $query .= 'NULL, ';
                } else {
                    $query .= '0, ';
                }

                $query .= ' ( fe.c_' . $criteria_id . ' * ' . ( $criteria['value'] / $criteria['out_of'] ) . ' )
                ) 
            )';

    }

    return $query;
}

/**
 * Retrieves subject ratings either as individual subject entries or aggregated by subject
 *
 * @param params containing form_id and subject_type. subject_id is optional.
 * @param is_aggregate if true, results are aggregated by subject
 */
function rawp_get_subject_form_ratings( $params = array(), $is_aggregate = false ) {

    $form_id = isset( $params['form_id'] ) ? intval( $params['form_id'] ) : null;
    $subject_id = isset( $params['subject_id'] ) ? intval( $params['subject_id'] ) : null;
    $subject_type = isset( $params['subject_type'] ) ? esc_sql ( $params['subject_type'] ) : null;
    $subject_sub_type = isset( $params['subject_sub_type'] ) ? esc_sql( $params['subject_sub_type'] ) : null;
    $limit = isset( $params['limit'] ) ? intval( $params['limit'] ) : 10;
    $offset = isset( $params['offset'] ) ? intval( $params['offset'] ) : 0;

    $form = rawp_get_form( $form_id );

    if ( $form === null || $subject_type === null) {
        return null;
    }

    global $wpdb;
    
    $query = 'SELECT ';

    if ( $is_aggregate === false ) {
        $query .= '
    fe.id AS form_entry_id,';
    }

    $query .='
    f.name AS form_name,
    f.id AS form_id,
    fe.subject_id AS subject_id,
    fe.subject_type AS subject_type,';

    if ( $subject_type === 'post' ) {
        $query .= '
    p.post_title AS subject_name, p.post_type AS subject_sub_type,';
    } else if ( $subject_type === 'user' ) {
        $query .= '
    u.display_name AS subject_name, "" AS subject_sub_type,';
    } else if ( $subject_type === 'taxonomy' ) {
        $query .= '
    t.name AS subject_name, tt.taxonomy AS subject_sub_type,';
    }

    if ( $is_aggregate === false ) {
        $query .= '
        fe.entry_date AS form_entry_date,
        fe.user_id AS user_id,';
    } else {
        $query .= '
        COUNT(fe.id) AS form_submissions,';
    }

    if ( count($form['criteria_items']) > 0 && $is_aggregate ) {
        $query .= '
        ( ';
    }

    $max_rating_score = 0;
    $count = count( $form['criteria_items'] );
    foreach ( $form['criteria_items'] as $criteria ) {

        $query .= rawp_get_criteria_avg_query( $criteria, $is_aggregate );

        $max_rating_score += intval( $criteria['value'] );
        
        if ( --$count > 0) {
            $query .= ' + ';
        }
    }

    if ( count($form['criteria_items']) > 0 && $is_aggregate ) {
        $query .= ' ) ';
    }

    $query .= ' AS rating_score, ' . intval( $max_rating_score ) . ' AS max_rating_score ';

    $count = count( $form['criteria_items'] );

    if ( $count > 0 ) {
        $query .= ',
    ';
    }

    foreach ( $form['criteria_items'] as $criteria ) {

        if ( $is_aggregate ) {
            $query .= rawp_get_criteria_avg_query( $criteria, $is_aggregate ) .' AS c_' . intval( $criteria['id'] ) . '_avg';
        } else {
            $query .= '
    fe.c_' . intval( $criteria['id'] );
        }

        if (--$count > 0) {
            $query .= ', ';
        }
    }

    $query .= ' FROM ' . $wpdb->prefix . 'rawp_form_entry fe 
INNER JOIN ' . $wpdb->prefix . 'rawp_form f ON fe.form_id = f.id';
   
    if ( $subject_type === 'post' ) {
        $query .= '
INNER JOIN ' . $wpdb->posts . ' p ON fe.subject_id = p.id';
    } else if ( $subject_type === 'user') {
        $query .= '
INNER JOIN ' . $wpdb->users . ' u ON fe.subject_id = u.id';
    } else if ( $subject_type === 'taxonomy') {
        $query .= '
INNER JOIN ' . $wpdb->prefix . 'term_taxonomy tt ON tt.term_id = fe.subject_id
INNER JOIN ' . $wpdb->prefix . 'terms t ON t.term_id = tt.term_id';
    }

    $query .= '
WHERE fe.form_id = ' . intval( $form_id ) . ' AND fe.subject_type = "' . esc_sql( $subject_type ) . '"';

    if ( $subject_sub_type) {

        if ( $subject_type === 'post' ) {
            $query .= '
        AND p.post_type = "' . esc_sql( $subject_sub_type ) . '"';
        } else if ( $subject_type === 'taxonomy' ) {
            $query .= '
        AND tt.taxonomy = "' . esc_sql( $subject_sub_type ) . '"';
        }

    }

    if ( $subject_id ) {
        $query .= '
        AND fe.subject_id = ' . intval( $subject_id );
    }

    if ( $is_aggregate ) {
        $query .= ' GROUP BY fe.subject_id';
    } else {
        $query .= ' GROUP BY fe.id';
    }

    $query .= ' ORDER BY rating_score DESC';

    $query .= ' LIMIT ' . intval( $limit ). ' OFFSET ' . intval( $offset );

    if ( $subject_id ) {
        return $wpdb->get_row( $query, ARRAY_A);
    }

    return $wpdb->get_results( $query, ARRAY_A );
}


/**
 * Returns a breakdown of star ratings
 */
function rawp_get_criteria_breakdown( $criteria ) {

    global $wpdb;

    if ( $criteria['type'] === 'star-rating' ) {
        
        $query = 'SELECT 
    COUNT(CASE WHEN c_1 = 1 then 1 ELSE NULL END) as "1_star",
    COUNT(CASE WHEN c_1 = 2 then 1 ELSE NULL END) as "2_star",
    COUNT(CASE WHEN c_1 = 3 then 1 ELSE NULL END) as "3_star",
    COUNT(CASE WHEN c_1 = 4 then 1 ELSE NULL END) as "4_star",
    COUNT(CASE WHEN c_1 = 5 then 1 ELSE NULL END) as "5_star"
FROM ' . $wpdb->prefix . 'rawp_form_entry';

        return $wpdb->get_row( $query, ARRAY_A);
    
    } else if ( $criteria['type'] === 'lookup' ) {

        $count = count( $criteria['lookup_options']);
        
        if ( $count > 0) {
        
            $query = 'SELECT '
            ;

            foreach ( $criteria['lookup_options'] as $lookup_option ) {
                $query .= ' COUNT(CASE WHEN c_' . intval( $criteria['id'] ) . ' = ' . intval( $lookup_option['id'] ) . ' then 1 ELSE NULL END) as "' . esc_sql( $lookup_option['option_text'] ) . '"
                ';

                if (--$count > 0) {
                    $query .= ',';
                }
            }

            $query .= 'FROM ' . $wpdb->prefix . 'rawp_form_entry';

            return $wpdb->get_row( $query, ARRAY_A);
        }

        return null; // NA
    }

}


/**
 * Returns true if a form entry already exists for a specified form id, 
 * subject type, subject id and user id
 */
function rawp_user_form_entry_exists( $form_entry ) {

    global $wpdb;

    $query = $wpdb->prepare( 'SELECT id FROM ' . $wpdb->prefix . 'rawp_form_entry WHERE form_id = %d AND subject_type = %s 
        AND subject_id = %d AND user_id = %d', 
        $form_entry['form_id'], $form_entry['subject_type'], $form_entry['subject_id'],  $form_entry['user_id'] );
    
    $row = $wpdb->get_row( $query );

    if ( $row === null ) {
        return false;
    }

    return true;

}


/**
 * Returns true if an existing form entry exists based on a hashed IP address 
 * and user id
 *
 * @param form_entry
 */
function rawp_ip_address_duplicate_check( $form_entry ) {

    global $wpdb;

    $query = $wpdb->prepare( 'SELECT id FROM ' . $wpdb->prefix . 'rawp_form_entry WHERE form_id = %d AND subject_type = %s 
        AND subject_id = %d AND hashed_ip_address = %s AND user_id = %d', $form_entry['form_id'], 
        $form_entry['subject_type'], $form_entry['subject_id'], $form_entry['hashed_ip_address'], $form_entry['user_id'] );
    
    $row = $wpdb->get_row( $query );

    if ( $row === null ) {
        return false;
    }

    return true;
}