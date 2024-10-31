<?php
/**
 * https://grid.layoutit.com/
 */
?>
<div class="rawp-rating-summary-details">
	
	<?php
	if ( $subject_form_rating_summary ) { ?>
	
		<div class="rawp-criteria-details">
			<?php

			if ( isset( $subject_form_rating_summary['form_submissions'] ) ) { 

				foreach ( $form['criteria_items'] as $criteria ) {
					
					$column_name = 'c_' . $criteria['id'] . '_avg';

					if ( $criteria['value'] > 0) {

						?><label><?php echo esc_html( $criteria['label'] ); ?></label>

						<?php	
						if ( $result_type !== 'star-rating' ) { 
						
							$percentage = ( $subject_form_rating_summary[$column_name] / $criteria['value'] ) * 100;
							?>
							<div class="rawp-horizontal-bar">
						    	<div class="rawp-horizontal-bar-left">
							   		<div class="rawp-horizontal-bar-container">
							      		<div style="width: <?php echo esc_attr( $percentage ); ?>%; height: 1em; background-color: <?php echo esc_attr( $primary_color ); ?>;"></div>
							  		</div>
								</div>

								<div class="rawp-horizontal-bar-right">
									<?php
										
									if ( $result_type === 'score' ) {
										
										$score = $subject_form_rating_summary[$column_name];

										echo number_format( $score, 1 ) . ' ' . __( 'out of', 'ratingwp' ) . ' ' . intval( $criteria['value'] );
										
									} else { // percentage

										if ( $percentage == 100 ) {
											echo number_format( $percentage, 0 ) . '%';
										} else {
											echo number_format( $percentage, 1 ) . '%'; 
										}
									} 
									?>
								</div>
							</div>
						<?php
						
						} else {
							?>
							<div class="rawp-star-rating">
								<?php

								$stars = ( $subject_form_rating_summary[$column_name] / $criteria['value'] ) * 5;
						
								rawp_get_template_part( 'star-rating', null, true, array(
										'stars' => round( $stars * 2 ) / 2,
										'primary_color' => $primary_color
								) ); 
										
								?>
								<span style="vertical-align: middle;"><?php echo number_format( $stars, 1 ) . ' ' . __( 'out of 5', 'ratingwp' ); ?></span>
							</div>
							<?php
						} ?>
					<?php }
				}
			} ?>
		</div>
	
	<?php
	} else {
		_e( 'No ratings yet', 'ratingwp' );
	} ?>
	
</div>