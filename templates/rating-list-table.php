<?php
/**
 *
 */
?>
<figure class="wp-block-table rawp-rating-list-table <?php if ( $default_style === 'stripes' ) { echo 'is-style-stripes'; } else if ( $default_style === 'default' ) { echo 'is-style-regular'; } ?>">
	<table class="<?php if ( $fixed_width_table_cells ) { echo 'has-fixed-layout'; } ?>">
		<?php if ( $show_header ) { ?>
			<thead>
				<tr>
					<?php if ( $show_rank ) {
						?><th><?php _e( 'Rank', 'ratingwp' ); ?></th><?php
					} ?>
					<th><?php echo esc_html( $subject_header ); ?></th>
					<th><?php _e( 'Rating', 'ratingwp' ); ?></th>
					<th><?php _e( 'Submissions', 'ratingwp' ); ?></th>
				</tr>
			</thead>
		<?php } ?>
		<tbody>
			<?php
			$index = 1;
			foreach ( $subject_form_rating_list as $subject_form_rating_list_item ) {
				?>
				<tr>
					<?php if ( $show_rank ) {
						?><td><?php echo esc_html( $index++ ); ?></td><?php
					} ?>
					<td>
						<?php
						if ( isset( $subject_form_rating_list_item['permalink'] ) ) {
							?>
							<a href="<?php echo esc_attr( $subject_form_rating_list_item['permalink'] ); ?>">
								<?php echo esc_html( $subject_form_rating_list_item['subject_name'] ); ?>
								</a>
							<?php
						} else {
							echo esc_html( $subject_form_rating_list_item['subject_name'] ); 
						}
						?>
					</td>
					<td>
						<?php 
						if ( $result_type === 'score') {

							$score = $subject_form_rating_list_item['rating_score'];

							echo number_format( $score, 1 ) . ' ' . __( 'out of', 'ratingwp' ) . ' ' . intval( $subject_form_rating_list_item['max_rating_score'] );

						} else if ( $result_type === 'star-rating') {

							$stars = ( $subject_form_rating_list_item['rating_score'] / $subject_form_rating_list_item['max_rating_score'] ) * 5;

							rawp_get_template_part( 'star-rating', null, true, array(
									'stars' => round( $stars * 2 ) / 2,
									'primary_color' => $primary_color
							) );

							echo number_format( $stars, 1 ) . ' ' . __( 'out of 5', 'ratingwp' );

						} else {

							$percentage = ( $subject_form_rating_list_item['rating_score'] / $subject_form_rating_list_item['max_rating_score'] ) * 100;

							if ( $percentage == 100 ) {
								echo number_format( $percentage, 0 ). '%'; 
							} else {
								echo number_format( $percentage, 1 ). '%'; 
							}

						}
						?>
					</td>
					<td><?php echo esc_html( $subject_form_rating_list_item['form_submissions'] ); ?><?php if ( ! $show_header ) { _e( ' submissions', 'ratingwp' ); } ?></td>
				</tr>
				<?php
			} ?>
		</tbody>
	</table>
</figure>
