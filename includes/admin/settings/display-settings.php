<?php
/**
 * Admin Options Page
 *
 * @package     RatingWP 
 * @subpackage  Admin/Settings
 * @copyright   Copyright (c) 2021, RatingWP
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * General settings section
 */
function rawp_section_general_desc() {
	?>
	<?php
}

/**
 * Strings settings section
 */
function rawp_section_strings_desc() {
	?>
	<?php
}

/**
 * Styles settings section
 */
function rawp_section_styles_desc() {
	?>
	<?php
}

/**
 * Field input setting
 */
function rawp_field_input( $args ) {
	$settings = (array) get_option( $args['option_name' ] );
	$class = isset( $args['class'] ) ? $args['class'] : 'regular-text';
	$type = isset( $args['type'] ) ? $args['type'] : 'text';
	$min = isset( $args['min'] ) && is_numeric( $args['min'] ) ? intval( $args['min'] ) : null;
	$max = isset( $args['max'] ) && is_numeric( $args['max'] ) ? intval( $args['max'] ) : null;
	$step = isset( $args['step'] ) && is_numeric( $args['step'] ) ? floatval( $args['step'] ) : null;
	$readonly = isset( $args['readonly'] ) && $args['readonly'] ? ' readonly' : '';
	$placeholder = isset( $args['placeholder'] ) ? $args['placeholder'] : '';
	$required = isset( $args['required'] ) && $args['required'] === true ? 'required' : '';
	?>
	<input class="<?php echo esc_attr( $class ); ?>" type="<?php echo esc_attr( $type ); ?>" name="<?php echo esc_attr( $args['option_name'] ); ?>[<?php echo esc_attr( $args['setting_id'] ); ?>]"
			value="<?php echo esc_attr( $settings[$args['setting_id']] ); ?>" <?php if ( $min !== null ) { echo ' min="' . esc_attr( $min  ). '"'; } ?>
			<?php if ( $max !== null) { echo ' max="' . esc_attr( $max ) . '"'; } echo $readonly; ?>
			<?php if ( $step !== null ) { echo ' step="' . esc_attr( $step ). '"'; } ?>
			placeholder="<?php echo $placeholder; ?>" <?php echo esc_attr( $required ); ?> />
	<?php
	if ( isset( $args['label'] ) ) { ?>
		<label><?php echo esc_html( $args['label'] ); ?></label>
	<?php }
}



/**
 * Color picker field
 *
 * @param unknown $args
 */
function rawp_field_select( $args ) {
	$settings = (array) get_option( $args['option_name' ] );
	$value = $settings[$args['setting_id']];
	?>
	<select name="<?php echo esc_attr( $args['option_name'] ); ?>[<?php echo esc_attr( $args['setting_id'] ); ?>]">
		<?php
		foreach ( $args['select_options'] as $option_value => $option_label ) {
			$selected = '';
			if ( $value == $option_value ) {
				$selected = 'selected="selected"';
			}
			echo '<option value="' . esc_attr( $option_value ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $option_label ) . '</option>';
		}
		?>
	</select>
	<?php
	if ( isset( $args['label'] ) ) { ?>
		<label><?php echo esc_html( $args['label'] ); ?></label>
	<?php }
}


/**
 * Color picker field
 *
 * @param unknown $args
 */
function rawp_field_color_picker( $args ) {
	$settings = (array) get_option( $args['option_name' ] );
	?>
	<input type="text" class="color-picker" name="<?php echo esc_attr( $args['option_name'] ); ?>[<?php echo esc_attr( $args['setting_id'] ); ?>]" value="<?php echo esc_attr( $settings[$args['setting_id']] ); ?>" />
	<?php if ( isset( $args['label' ] ) ) { ?>
		<p><?php echo esc_html( $args['label'] ); ?></p>
	<?php }
}


/**
 * Checkbox setting
 */
function rawp_field_checkbox( $args ) {
	$settings = (array) get_option( $args['option_name' ] );
	?>
	<input type="checkbox" name="<?php echo esc_attr( $args['option_name'] ); ?>[<?php echo esc_attr( $args['setting_id'] ); ?>]" value="true" <?php checked( true, isset( $settings[$args['setting_id']] ) ? esc_attr( $settings[$args['setting_id']] ) : false , true ); ?> />
	<?php
	if ( isset( $args['label'] ) ) { ?>
		<label><?php echo esc_html( $args['label'] ); ?></label>
	<?php }
}


/**
 * Checkboxes field
 *
 * @param unknown $args
 */
function rawp_field_checkboxes( $args ) {
	$settings = (array) get_option( $args['option_name' ] );
	$value = $settings[$args['setting_id']];

	foreach ( $args['checkboxes'] as $checkbox ) {

		$checked = '';
		if ( is_array( $value ) ) {
			if ( in_array( $checkbox['name'], $value ) ) {
				$checked = 'checked="checked"';
			}
		} else if ( $checkbox['name'] == $value ) {
			$checked = 'checked="checked"';
		}

		?>
		<input type="checkbox" name="<?php echo esc_attr( $args['option_name'] ); ?>[<?php echo esc_attr( $args['setting_id'] ); ?>][]" value="<?php echo esc_attr( $checkbox['name'] ); ?>" <?php echo esc_attr( $checked ); ?> />
		<label class="checkbox-label"><?php echo esc_html( $checkbox['label'] ); ?></label>
		<?php
	}

	if ( isset( $args['description'] ) ) {
		?>
		<p><?php echo esc_html( $args['description'] ); ?></p>
		<?php
	}
}


/**
 * Field radio buttons
 */
function rawp_field_radio_buttons( $args ) {
	$settings = (array) get_option( $args['option_name' ] );
	foreach ( $args['radio_buttons'] as $radio_button ) {
		?>
		<input type="radio" name="<?php echo esc_attr( $args['option_name'] ); ?>[<?php echo esc_attr( $args['setting_id'] ); ?>]" value="<?php echo esc_attr( $radio_button['value'] ); ?>" <?php checked( $radio_button['value'], esc_attr( $settings[$args['setting_id']] ), true); ?> />
		<label><?php echo esc_html( $radio_button['label'] ); ?></label><br />
		<?php
	}
	if ( isset( $args['label'] ) ) { ?>
		<br />
		<label><?php echo esc_html( $args['label'] ); ?></label>
	<?php }
}
