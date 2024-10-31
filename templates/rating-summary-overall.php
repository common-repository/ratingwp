<?php
/**
 * Rating summary overall layout
 */
?>
<div class="rawp-rating-summary-overall" style="text-align: <?php echo esc_attr( $text_align ); ?>">

	<?php
	if ( $subject_form_rating_summary ) {

		if ( $result_type === 'score' ) { // score
			?>
			
			<<?php echo esc_attr( $header ); ?>>
				<?php echo number_format( $subject_form_rating_summary['rating_score'], 1 ); ?> out of <?php echo intval( $subject_form_rating_summary['max_rating_score'] ); ?>
			</<?php echo esc_attr( $header ); ?>>
			
			<?php
		} else if ( $result_type === 'star-rating' ) { // star rating

			$value = ( $subject_form_rating_summary['rating_score'] / $subject_form_rating_summary['max_rating_score'] ) * 5;
			
			?>
			<<?php echo esc_attr( $header ); ?>>
				<?php echo number_format( $value, 1 ) . ' ' . __( 'out of', 'ratingwp' ) . ' 5'; ?>
			</<?php echo esc_attr($header ); ?>>

			<?php 
			rawp_get_template_part( 'star-rating', null, true, array(
					'stars' => round( $value * 2 ) / 2,
					'primary_color' => $primary_color
			) );

		} else { // percentage

			$percentage = ( $subject_form_rating_summary['rating_score'] / $subject_form_rating_summary['max_rating_score'] ) * 100;
			?>
			<<?php echo esc_attr( $header ); ?>>
				<?php 
				if ( $percentage == 100 ) {
					echo number_format( $percentage, 0 ). '%'; 
				} else {
					echo number_format( $percentage, 1 ). '%'; 
				}?>
			</<?php echo esc_attr( $header ); ?>>
			<?php
		}
		?>

		<span class="rawp-submissions">
			<?php echo intval( $subject_form_rating_summary['form_submissions'] ) . ' ' . __( 'submissions', 'ratingwp' ); ?>
		</span>
		
	<?php 
	} else {
		_e( 'No ratings yet', 'ratingwp' );
	} ?>
</div>
