<div class="rawp-stars" style="color: <?php echo esc_attr( $primary_color ); ?>;">
	<?php
	$index=0;

	for ($index; $index < 5; $index++) {
		if ( ( $index + .5 ) === $stars ) {
			?>
			<span class="dashicons dashicons-star-half"></span>
			<?php
		} else if ( $index < $stars ) {
			?>
			<span class="dashicons dashicons-star-filled"></span>
			<?php
		} else {
			?>
			<span class="dashicons dashicons-star-empty"></span>
			<?php
		}
	}
	?>
</div>