<?php
/**
 * Forms admin class.
 * 
 * @author RatingWP
 *
 */
class RAWP_Forms_Admin {

	/**
	 * Constructor
	 */
	public function __construct() {

	}

	/**
	 *
	 */
	public static function ajax_add_critiera() {

		$ajax_nonce = $_GET['nonce'];
		$type =  sanitize_key( $_GET['type'] );
		$criteria = [ 
			'id' => '', 
			'type' => $type,
			'label' => __( 'My sample criteria', 'ratingwp' ),
			'value' => 20
		];

		if ( $type === 'lookup' ) { // lookup
		
			$criteria['lookup_options'] = [ 
				[
					'id' => '',
					'option_text' => __( 'Sample option', 'ratingwp' ),
					'percentage_value' => 0.5
				],
				[ 
					'id' => '',
					'option_text' => __( 'Sample option', 'ratingwp' ),
					'percentage_value' => .75
				],
				[ 
					'id' => '',
					'option_text' => __( 'Sample option', 'ratingwp' ),
					'percentage_value' => 1
				]
			];
			$criteria['display'] = 'select';
		
		} else if ( $type === 'numeric' ) {
		
			$criteria['min'] = 0;
			$criteria['max'] = 100;
			$criteria['is_ascending'] = true;
			$criteria['display'] = 'slider';
		
		} else { // star rating

			$criteria['out_of'] = 5;
		
		}
		
		if ( wp_verify_nonce( $ajax_nonce, RAWP_SLUG .'-nonce' ) && current_user_can( 'manage_options' ) ) {
			
			ob_start();
			RAWP_Forms_Admin::display_sortable_criteria( $criteria );
			$html = ob_get_contents();
			ob_end_clean();
			
			echo json_encode( array(
				'html' => $html,
				'success' => true
			) );
		}
		
		wp_die();

	}

	/**
	 *
	 */
	public function display_edit_page( $id ) {

		$form = null;
		if ( $id === null ) {
			
			$form = array(
				'name' => __( 'My sample form', 'ratingwp' ),
				'id' => null,
				'criteria_items' => []
			);

		} else {
			$form = rawp_get_form( $id );
		}

		/** 
		 * Heading
		 */
		?>
		<h1 style="display: block; float: left; width: auto;">
			<?php 
			if ( $form['id'] ) {
				printf( __( 'Edit Form #%d', 'ratingwp' ), $form['id'] ); 
			} else {
				_e( 'Add Form', 'ratingwp' );
			}
			?>
		</h1>
		<?php

		/*
		 * Form switcher
		 */
		$forms = rawp_get_forms();
		if (count( $forms ) > 0) {
			?>
			<div style="display: block; float: right; padding: 9px 0 4px 0;">
				<select id="rawp-switch-form-select">
					<?php
					foreach ( $forms as $temp_form ) { 
						?>
						<option value="<?php echo $temp_form->id; ?>" <?php if ( $temp_form->id == $form['id'] ) echo 'selected="selected"'; ?>>
							<?php echo esc_html( $temp_form->name ); ?>
						</option>
						<?php
					}
					?>
				</select>
				<input id="rawp-switch-form" type="button" class="button button-secondary" value="<?php _e( 'Switch Form', 'ratingwp' ); ?>" />
			</div>
			<?php
		} 

		/**
		 * Edit
		 */
		?>
		<form id="rawp-edit-form">

			<input type="hidden" id="form-id" value="<?php if ($form['id'] ) { echo esc_attr( $form['id'] ); } else { echo ''; } ?>" />
			
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><?php _e( 'Name', 'ratingwp' ); ?></th>
						<td>
							<input type="text" id="name" name="name" maxlength="50" value="<?php echo esc_attr( $form['name'] ); ?>" class="regular-text">
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Criteria', 'ratingwp' ); ?></th>
						<td>
							<input type="button" value="Numeric" id="rawp-add-numeric-criteria-btn" class="button button-secondary" style="margin: 0px" />
							<input type="button" value="Lookup" id="rawp-add-lookup-criteria-btn" class="button button-secondary" />
							<input type="button" value="Star Rating" id="rawp-add-star-rating-criteria-btn" class="button button-secondary" />
							<p class="description" id="tagline-description"><?php _e( 'Click to add criteria.', 'ratingwp' ); ?></p>
						</td>
					</tr>
				</tbody>
			</table>

			<div id="sortable" class="rawp-sortable-container" style="margin-top: 10px;">
				<?php
				foreach ( $form['criteria_items'] as $criteria ) {
					RAWP_Forms_Admin::display_sortable_criteria( $criteria );
				}
				?>
			</div>

			<p><input type="submit" id="rawp-save-form-btn" class="button button-primary" value="Save Changes" /></p>

		</form>
		<?php

	}

	/**
	 *
	 */
	public static function display_sortable_criteria( $criteria ) {
		$dummy_criteria_id = null;
		if ( $criteria['id'] == null ) {
			$dummy_criteria_id = rand();
		}
		?>
		<div class="rawp-sortable-item">

			<div class="rawp-sortable-item-header">
				<h2><?php echo esc_html( $criteria['label'] ); ?> <span style="color: #646970">[<?php
					if ( $criteria['type'] === 'star-rating' ) {
						_e( 'Star Rating' ,'ratingwp' );
					} else if ( $criteria['type'] === 'numeric' ) {
						_e( 'Numeric', 'ratingwp' );
					} else {
						_e( 'Lookup', 'ratingwp' );
					}

					if ( $criteria['id'] ) {
						echo sprintf( __( ' %d', 'ratingwp' ), $criteria['id'] );
					}
				?>]</span></h2>
				
				<div class="rawp-sortable-item-actions">
					<a href="#" class="rawp-delete-btn"><?php _e( 'Delete', 'ratingwp' ); ?></a>
				    <span class="dashicons dashicons-arrow-up-alt2"></span>
				    <span class="dashicons dashicons-arrow-down-alt2"></span>
				    <span class="dashicons dashicons-arrow-down"></span>
				</div>

			</div>

			<div class="rawp-sortable-item-container" style="display: none;">

				<?php if ( $criteria['id'] === "" ) {
					?>
					<input type="hidden" name="dummy-criteria-id" value=<?php echo esc_attr( $dummy_criteria_id ); ?> />
					<?php
				}
				?>
				<input type="hidden" name="criteria-id" value="<?php echo esc_attr( $criteria['id'] ); ?>" />
				<input type="hidden" name="type" value="<?php echo esc_attr( $criteria['type'] ); ?>" />

				<?php
				if ( $criteria['type'] === 'lookup' ) { // lookup

					?>
					<table class="form-table widefat">
						<tbody>
							<tr valign="top">
								<th scope="row">
									<label for="label"><?php _e( 'Label', 'ratingwp' ); ?></label>
								</th>
								<td>
									<input name="label" type="text" placeholder="<?php _e( 'Add label...', 'ratingwp' ); ?>" value="<?php echo esc_attr( $criteria['label'] ); ?>" required maxlength=255 class="regular-text" />
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label for="value"><?php _e( 'Value', 'ratingwp' ); ?></label>
								</th>
								<td>
									<input name="value" type="number" required min="0" value="<?php echo esc_attr( $criteria['value'] ); ?>" />
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label for="options"><?php _e( 'Options', 'ratingwp' ); ?></label>
								</th>
								<td>
									<table class="rawp-lookup-options widefat form-table" style="margin-top: 0px; width: auto !important;">
										<thead>
											<tr>
												<th><?php _e( 'Percentage Value' ,'ratingwp' ); ?></th>
												<th><?php _e( 'Text', 'ratingwp' ); ?></th>
												<th><?php _e( 'Is Default?', 'ratingwp' ); ?></th>
												<th><?php _e( 'Action', 'ratingwp' ); ?></th>
											</tr>
										</thead>
										<tbody>
											<?php
											$index = 0;
											foreach ( $criteria['lookup_options'] as $lookup_option ) { 
												?>
												<tr class="<?php if ( $index++ % 2 === 0 ) { echo 'alternate'; } ?>">
													<td>
														<input type="number" name="percentage-value" class="small-text" value="<?php echo esc_attr( $lookup_option["percentage_value"] ); ?>" min="0" max="1" step="0.01" /></td>
													<td>
														<input type="text" required maxlength=50 name="option-text" class="regular-text" value="<?php echo esc_attr( $lookup_option["option_text"] ); ?>" />
													</td>
													<td>
														<input type="radio" name="is-default-<?php if ( $dummy_criteria_id ) { echo esc_attr( $dummy_criteria_id ); } else { echo esc_attr( $criteria['id'] ); } ?>" value="true" <?php if ( $lookup_option['is_default'] ) { echo 'checked'; } ?> /><label><?php _e( 'Yes', 'ratingwp' ); ?></label>
													</td>
													<td>
														<span class="delete"><a class="delete-lookup-option" href="#"><?php _e( 'Delete' ,'ratingwp' ); ?></a><input type="hidden" name="lookup-option-id" value="<?php echo esc_attr( $lookup_option['id'] ); ?>" /></span>
													</td>
												</tr>
												<?php
											}
											?>
										</tbody>
									</table>
									
									<input type="button" class="button secondary-button add-lookup-option" value="<?php _e( 'Add New', 'ratingwp' ); ?>" style="margin-top: 5px; margin-bottom: 10px;">

								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label for="value"><?php _e( 'Display', 'ratingwp' ); ?></label>
								</th>
								<td>
									<select name="display">
										<option value="select" <?php if ( $criteria['display'] === 'select') echo 'selected="selected"'; ?>><?php _e( 'Select', 'ratingwp' ); ?></option>
										<option value="radio" <?php if ( $criteria['display'] === 'radio') echo 'selected="selected"'; ?>><?php _e( 'Radio', 'ratingwp' ); ?></option>
									</select>
								</td>
							</tr>
						</tbody>
					</table>
					<?php

				} else if ( $criteria['type'] === 'numeric' ) { // numeric
					
					?>
					<table class="form-table widefat">
						<tbody>
							<tr valign="top">
								<th scope="row">
									<label for="label"><?php _e( 'Label', 'ratingwp' ); ?></label>
								</th>
								<td>
									<input name="label" type="text" placeholder="<?php _e( 'Add label...', 'ratingwp' ); ?>" maxlength=255 value="<?php echo esc_attr( $criteria['label'] ); ?>" required class="regular-text" />
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label for="value"><?php _e( 'Value', 'ratingwp' ); ?></label>
								</th>
								<td>
									<input name="value" type="number" required min="0" value="<?php echo esc_attr( $criteria['value'] ); ?>" />
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label for="is-ascending"><?php _e( 'Is ascending?', 'ratingwp' ); ?></label>
								</th>
								<td>
									<input type="checkbox" name="is-ascending" value="1" <?php if ( $criteria['is_ascending'] === true ) { ?>checked="checked"<?php } ?> />
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label for="min"><?php _e( 'Min', 'ratingwp' ); ?></label>
								</th>
								<td>
									<input type="number" min="0" name="min" value="<?php echo esc_attr( $criteria['min'] ); ?>" />
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label for="max"><?php _e( 'Max', 'ratingwp' ); ?></label>
								</th>
								<td>
									<input type="number" min="0" name="max" value="<?php echo esc_attr( $criteria['max'] ); ?>" />
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label for="max"><?php _e( 'Default', 'ratingwp' ); ?></label>
								</th>
								<td>
									<input type="number" min="0" name="default" value="<?php echo esc_attr( $criteria['default'] ); ?>" />
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label for="value"><?php _e( 'Display', 'ratingwp' ); ?></label>
								</th>
								<td>
									<select name="display">
										<option value="range" <?php if ( $criteria['display'] === 'range') echo 'selected="selected"'; ?>><?php _e( 'Range', 'ratingwp' ); ?></option>
										<option value="number" <?php if ( $criteria['display'] === 'number') echo 'selected="selected"'; ?>><?php _e( 'Number', 'ratingwp' ); ?></option>
									</select>
								</td>
							</tr>
						</tbody>
					</table>
					<?php

				} else { // star rating
					
					?>
					<table class="form-table widefat">
						<tbody>
							<tr valign="top">
								<th scope="row">
									<label for="label"><?php _e( 'Label', 'ratingwp' ); ?></label>
								</th>
								<td>
									<input name="label" type="text" placeholder="<?php _e( 'Add label...', 'ratingwp' ); ?>" value="<?php echo esc_attr( $criteria['label'] ); ?>" required maxlength=255 class="regular-text" />
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label for="value"><?php _e( 'Value', 'ratingwp' ); ?></label>
								</th>
								<td>
									<input name="value" type="number" required min="0" value="<?php echo esc_attr( $criteria['value'] ); ?>" />
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label for="outOf"><?php _e( 'Out Of', 'ratingwp' ); ?></label>
								</th>
								<td>
									<input name="out-of" type="number" required min="0" max="10" value="<?php echo esc_attr( $criteria['out_of'] ); ?>" />
								</td>
							</tr>
						</tbody>
					</table>
					<?php
				}
				?>
			</div>
		</div>
		<?php
	}

	/**
	 *
	 */
	public function display_view_page() {

		$forms = rawp_get_forms();
			
		?>
		<h1><?php _e( 'Forms', 'ratingwp' ); ?> <a class="add-new-h2" href="?page=rawp_forms&id=&action=new&nonce=<?php echo wp_create_nonce(); ?>"><?php _e( 'Add New', 'ratingwp' ); ?></a></h1>
					
		<?php 
		if (count( $forms ) === 0) {
			?>
			<p><?php _e( 'No forms', 'ratingwp' ); ?></p>
			<?php
		} else {
			?>
			<table class="wp-list-table widefat fixed striped table-view-list">
			    <thead>
			        <tr>
			            <th scope="col" id="form_name" class="manage-column column-form_name column-primary">
			            	<?php _e( 'Form Name', 'ratingwp' ); ?>
			            </th>
				        <th scope="col" id="count" class="manage-column column-count">
				        	<?php _e( 'Submissions', 'ratingwp' ); ?>
				        </th>
				    </tr>
				</thead>
				<tbody id="the-list">
					<?php
					foreach ( $forms as $form ) {
						?>
					    <tr>
					        <td class="column-form_name has-row-actions column-primary" data-colname="Form Name"><?php echo esc_html( $form->name ); ?>
					            <div class="row-actions">
					              	<?php

						            /**
						             * Action links
						             */
						            $edit_link = '?page=rawp_forms&id=' . intval( $form->id ) . '&action=edit';
						            $confirm_message = "'" . __( 'Are you sure you want to delete the form?', 'ratingwp' ) . "'";
						            $delete_link = "javascript:(
										function(event){
											confirm(" . esc_html( $confirm_message ) . ")?
												(window.location='?page=rawp_forms&id=" . intval( $form->id ) . "&action=delete&nonce=" . wp_create_nonce() . "')
												:'';
											return false;
										})()";
									$clone_link = '?page=rawp_forms&id=' . intval( $form->id ) . '&action=clone&nonce=' . wp_create_nonce();
						            ?>
						            <span class="edit"><a href="<?php echo esc_url( $edit_link ); ?>"><?php _e( 'Edit', 'ratingwp' ); ?></a> | </span>
						            <span class="delete"><a href="<?php echo esc_attr( $delete_link ); ?>"><?php _e( 'Delete', 'ratingwp' ); ?></a></span>
						        </div>
						    </td>
						    <td class="column-count">
						        <?php echo esc_html( $form->form_submissions ); ?>
						        <div class="row-actions">
					              	<?php
									$view_link = '?page=rawp_submissions&form-id=' . intval( $form->id );
						            ?>
						            <span class="view"><a href="<?php echo esc_url( $view_link ); ?>"><?php _e( 'View', 'ratingwp' ); ?></a>
						        </div>
						    </td>
						</tr>
						<?php
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th scope="col" class="manage-column column-form_name column-primary"><?php _e( 'Form Name', 'ratingwp' ); ?></th>
						<th scope="col" class="manage-column column-count"><?php _e( 'Submissions', 'ratingwp' ); ?></th>
					</tr>
				</tfoot>
			</table>
			<?php
		}
	}

	/**
	 *
	 */
	public function delete_form( $id ) {
		$rows = rawp_delete_form( $id );
	}

	/**
	 *
	 */
	public static function ajax_save_form( ) {
		
		$ajax_nonce = $_POST['nonce'];
		
		if ( wp_verify_nonce( $ajax_nonce, RAWP_SLUG .'-nonce' ) && current_user_can( 'manage_options' ) ) {

			$form = array(
				'id' => is_numeric( $_POST['formId'] ) ? intval( $_POST['formId'] ) : null,
				'name' => sanitize_text_field( $_POST['name'] ),
				'criteria_items' => array()
			);
				
			$index = 0;
			foreach ( $_POST['criteriaItems'] as $temp_criteria ) {

				$type = sanitize_key( $temp_criteria['type'] );
				
				$criteria = array(
					'type' => $type,
					'label' => sanitize_text_field( $temp_criteria['label'] ),
					'id' => is_numeric( $temp_criteria['id'] ) ? intval( $temp_criteria['id'] ) : null,
					'value' => is_numeric( $temp_criteria['value'] ) ? intval( $temp_criteria['value'] ) : 20,
					'display_order' => $index
				);

				if ( $type === 'numeric' ) { // numeric
					
					$criteria['min'] = is_numeric( $temp_criteria['min'] ) ? intval( $temp_criteria['min'] ) : 0;
					$criteria['max'] = is_numeric( $temp_criteria['max'] ) ? intval( $temp_criteria['max'] ) : 10;
					$criteria['is_ascending'] = $temp_criteria['isAscending'] === 'true' ? 1 : 0;
					$criteria['default'] = is_numeric( $temp_criteria['default'] ) ? intval( $temp_criteria['default'] ) : $criteria['max'];
					$criteria['display'] = sanitize_key( $temp_criteria['display'] );

				} else if ( $type === 'lookup' ) { // lookup

					$lookup_options = array();
					$found_default = false;
					foreach ( $_POST['criteriaItems'][$index]['lookupOptions'] as $temp_lookup_option ) {

						$is_default = $temp_lookup_option['isDefault'] === 'true';
						
						array_push( $lookup_options, array(
							'id' => is_numeric( $temp_lookup_option['id'] ) ? intval( $temp_lookup_option['id'] ) : null,
							'percentage_value' => floatval( $temp_lookup_option['percentageValue'] ),
							'option_text' => sanitize_text_field( $temp_lookup_option['optionText'] ),
							'is_default' => ( $is_default && ! $found_default )
						) );

						if ( $is_default ) { // ensure only set once
							$found_default = true;
						}
					}

					$criteria['lookup_options'] = $lookup_options;
					$criteria['display'] = sanitize_key( $temp_criteria['display'] );

				} else { // star rating

					$criteria['out_of'] = is_numeric( $temp_criteria['outOf'] ) ? intval( $temp_criteria['outOf'] ) : 5;
				
				}

				array_push( $form['criteria_items'], $criteria );

				$index++;
			}

			/**
			 * Call database to create/update form
			 */
			$form = rawp_create_update_form( $form );

			echo json_encode( array(
				'html' => $html,
				'success' => true
			) );
		}

		wp_die();
	} 

}

add_action( 'wp_ajax_rawp_add_criteria', array( 'RAWP_Forms_Admin', 'ajax_add_critiera' ) );
add_action( 'wp_ajax_rawp_save_form', array( 'RAWP_Forms_Admin', 'ajax_save_form' ) );