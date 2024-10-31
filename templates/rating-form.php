<?php
/**
 *
 */
$is_star_rating_submit = 
	is_array( $form['criteria_items'] ) && 
	count( $form['criteria_items'] ) === 1 && 
	$form['criteria_items'][array_keys($form['criteria_items'])[0]]['type'] === 'star-rating';

if ( $form ) {
	?>
	<form class="rawp-rating-form">

		<input type="hidden" name="form-id" value="<?php echo esc_attr( $form['id'] ); ?>" />
		<input type="hidden" name="subject-type" value="<?php echo esc_attr( $subject_type ); ?>" />
		<input type="hidden" name="subject-sub-type" value="<?php echo esc_attr( $subject_sub_type ); ?>" />
		<input type="hidden" name="subject-id" value="<?php echo esc_attr( $subject_id ); ?>" />

		<div class="rawp-criteria-wrapper">

			<?php foreach ( $form['criteria_items'] as $criteria) { ?>
				<label><?php echo esc_html( $criteria['label'] ); ?></label>

				<?php
				if ( $criteria['type'] === 'numeric' ) { // numeric
					
					if ( $criteria['display'] === 'number') {
						?>
						<div class="rawp-number">
						<input type="number" id="<?php echo esc_attr( $criteria['id'] ); ?>" min="<?php echo esc_attr( $criteria['min'] ); ?>" max="<?php echo esc_attr( $criteria['max'] ); ?>" value="<?php echo esc_attr( $criteria['default'] ); ?>" />
						</div>
						<?php
					} else { // range
						?>
						<div class="rawp-range">
							<input type="range" id="<?php echo esc_attr( $criteria['id'] ); ?>" min="<?php echo esc_attr( $criteria['min'] ); ?>" max="<?php echo esc_attr( $criteria['max'] ); ?>" value="<?php echo esc_attr( $criteria['default'] ); ?>" />
							<span class="rawp-slider-text"><?php echo esc_html( $criteria['default'] ); ?></span>
						</div>
						<?php
					}

				} else if ( $criteria['type'] === 'lookup' ) { // lookup

					$lookup_options = $criteria['lookup_options'];

					if ( $criteria['display'] === 'select') {
						?>
						<div class="rawp-select">
							<select id="<?php echo esc_attr( $criteria['id'] ); ?>">
								<?php
								foreach ($lookup_options as $lookup_option) {
									?>
									<option value="<?php echo esc_attr( $lookup_option['id'] ); ?>" <?php if ( $lookup_option['is_default'] ) { echo 'selected'; } ?>><?php echo esc_html( $lookup_option['option_text'] ); ?></option>
									<?php
								}
								?>
							</select>
						</div>
						<?php

					} else {
					
						?>
						<div class="rawp-radio">
							<?php
							$count = count( $lookup_options );
							foreach ($lookup_options as $lookup_option) {
								?>
								<input type="radio" id="<?php echo esc_attr( $criteria['id'] ); ?>" name="<?php echo esc_attr( $criteria['id'] ); ?>" value="<?php echo esc_attr( $lookup_option['id'] ); ?>" <?php if ( $lookup_option['is_default'] ) { echo 'checked'; } ?> />
								<label><?php echo esc_html( $lookup_option['option_text'] ); ?></label>
								<?php
								if (--$count > 0) {
									echo '<br />';
								}
							} ?>
						</div>
						<?php
					}

				} else { // star rating
					?>
					<div class="rawp-stars <?php if ( $is_star_rating_submit ) { echo 'rawp-star-rating-submit'; } ?>" id="<?php echo esc_attr( $criteria['id'] ); ?>" style="color: <?php echo esc_attr( $primary_color ); ?>;">
						<?php
						$index=0;
						for ($index; $index < $criteria['out_of']; $index++) {
							?>
							<span class="dashicons dashicons-star-empty"></span>
							<?php
						}
						?>
					</div>
				<?php
				}
			} ?>
		</div>

		<?php if ( ! $is_star_rating_submit ) {
			?>
				<div class="rawp-buttons">
					<input type="submit" value="Submit" />
				</div>
			<?php
		} ?>
	</form>
<?php 
}