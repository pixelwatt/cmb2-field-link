<?php
/**
 * @package CMB2\Field_Link
 * @author  scottsawyer
 * @copyright   Copyright (c) scottsawyer
 *
 * Plugin Name: CMB2 Field Type: Link
 * Plugin URI: https://github.com/pixelwatt/cmb2-field-link
 * Github Plugin URI: https://github.com/pixelwatt/cmb2-field-link
 * Description: CMB2 field type to create a link.
 * Version: 1.0.1
 * Author: Rob Clark (forked from scottsawyer)
 * Author URI: https://robclark.io
 * License: GPLv2+
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'CMB2_Field_Link' ) ) {
	/**
	 * Class CMB2_Field_Link
	 */
	class CMB2_Field_Link {

		/**
		 * Current version number
		 */
		const VERSION = '1.0.1';

		/**
		 * Initialize the plugin
		 */
		public function __construct() {
			add_action( 'cmb2_render_link', array( $this, 'render_link' ), 10, 5 );
			add_filter( 'cmb2_sanitize_link', array( $this, 'maybe_save_split_values' ), 12, 4 );
			add_filter( 'cmb2_sanitize_link', array( $this, 'sanitize_link' ), 10, 5 );
			add_filter( 'cmb2_types_esc_link', array( $this, 'escape_link' ), 10, 4 );
		}

		public function render_link( $field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object ) {

			// the properties of the fields.

			$field_escaped_value = wp_parse_args(
				$field_escaped_value,
				array(
					'href'   => '',
					'text'   => '',
					'class'  => '',
					'rel'    => '',
					'title'  => '',
					'target' => '',
				)
			);

			?>
	  <style>
		.cmb2-field-link-col {
			display: inline-block;
			width: 45%;
			margin-right: 3%;
		}
		.cmb2-field-link-col p {
			margin-top: 5px;
			padding-top: 0;
			font-weight: 400;
			line-height: 1.2;
			color: #6c757d;
		}
		.cmb2-field-link-col label {
			margin-bottom: 5px;
			display: block;
			font-weight: 600;
			font-size: 0.8125rem;
			color: #343a40;
		}
		.cmb2-field-link-col .cmb2-field-link-url {
			padding: 0 8px;
			line-height: 2;
			min-height: 30px;
			box-shadow: 0 0 0 transparent;
			border-radius: 4px;
			border: 1px solid #7e8993;
			background-color: #fff;
			color: #32373c;
		}
	  </style>
	  <div class="cmb2-field-link-col">
		<label for="<?php echo $field_type_object->_id( '_text' ); ?>"><?php echo esc_html( 'Text:' ); ?></label>
			<?php
				echo $field_type_object->input(
					array(
						'type'  => 'text',
						'name'  => $field_type_object->_name( '[text]' ),
						'id'    => $field_type_object->_id( '_text' ),
						'value' => $field_escaped_value['text'],
						'desc'  => '',
					)
				);
			?>
		<p><small>The Link Text.</small></p>
	  </div>
	  <div class="cmb2-field-link-col">
		<label for="<?php echo $field_type_object->_id( '_href' ); ?>"><?php echo esc_html( 'URL:' ); ?></label>
			<?php
				echo $field_type_object->input(
					array(
						'type'  => 'text_url',
						'name'  => $field_type_object->_name( '[href]' ),
						'id'    => $field_type_object->_id( '_href' ),
						'value' => $field_escaped_value['href'],
						'desc'  => '',
						'class' => 'regular-text cmb2-field-link-url',
					)
				);
			?>
		<p><small>The URL of the link.</small></p>
	  </div>
	  <div class="cmb2-field-link-col">
		<label for="<?php echo $field_type_object->_id( '_class' ); ?>"><?php echo esc_html( 'Classes:' ); ?></label>
			<?php
				echo $field_type_object->input(
					array(
						'type'  => 'text',
						'name'  => $field_type_object->_name( '[class]' ),
						'id'    => $field_type_object->_id( '_class' ),
						'value' => $field_escaped_value['class'],
						'desc'  => '',
					)
				);
			?>
		<p><small>CSS classes, separated with a space.</small></p>
	  </div>
	  <div class="cmb2-field-link-col">
		<label for="<?php echo $field_type_object->_id( '_rel' ); ?>"><?php echo esc_html( 'Rel:' ); ?></label>
			<?php
				echo $field_type_object->input(
					array(
						'type'  => 'text',
						'name'  => $field_type_object->_name( '[rel]' ),
						'id'    => $field_type_object->_id( '_rel' ),
						'value' => $field_escaped_value['rel'],
						'desc'  => '',
					)
				);
			?>
		<p><small>The rel property ( "nofollow" ).</small></p>
	  </div>
	  <div class="cmb2-field-link-col">
		<label for="<?php echo $field_type_object->_id( '_title' ); ?>"><?php echo esc_html( 'Title:' ); ?></label>
			<?php
				echo $field_type_object->input(
					array(
						'type'  => 'text',
						'name'  => $field_type_object->_name( '[title]' ),
						'id'    => $field_type_object->_id( '_title' ),
						'value' => $field_escaped_value['title'],
						'desc'  => '',
					)
				);
			?>
		<p><small>The title property (shown on hover).</small></p>
	  </div>
	  <div class="cmb2-field-link-col">
		<label for="<?php echo $field_type_object->_id( '_target' ); ?>"><?php echo esc_html( 'Target:' ); ?></label>
			<?php
				echo $field_type_object->input(
					array(
						'type'  => 'text',
						'name'  => $field_type_object->_name( '[target]' ),
						'id'    => $field_type_object->_id( '_target' ),
						'value' => $field_escaped_value['target'],
						'desc'  => '',
						'placeholder' => '_self',
					)
				);
			?>
		<p><small>Change to "_blank" to open in new window.</small></p>
	  </div>
			<?php
			echo $field_type_object->_desc( true );
			//return $this->rendered( ob_get_clean() );

		}
		/**
		 * Maybe split values.
		 */
		public static function maybe_save_split_values( $override_value, $value, $object_id, $field_args ) {
			if ( ! isset( $field_args['split_values'] ) || ! $field_args['split_values'] ) {
				return $override_value;
			}

			$link_keys = array( 'href', 'text', 'class', 'rel', 'title', 'target' );

			foreach ( $link_keys as $key ) {
				if ( ! empty( $value[ $key ] ) ) {
					update_post_meta( $object_id, $field_args['id'] . 'link_' . $key, sanitize_text_field( $value[ $key ] ) );
				}
			}
			remove_filter( 'cmb2_sanitize_link', array( $this, 'sanitize' ), 10, 5 );

			return true;
		}
		/**
		 * Santize Field.
		 */
		public static function sanitize_link( $check, $meta_value, $object_id, $field_args, $sanitize_object ) {

			if ( ! is_array( $meta_value ) || ! ( array_key_exists( 'repeatable', $field_args ) && true == $field_args['repeatable'] ) ) {
				return $check;
			}
			foreach ( $meta_value as $key => $val ) {

				$meta_value[ $key ] = array_filter( array_map( 'sanitize_text_field', $val ) );

			}

			return array_filter( $meta_value );
		}
		/**
		 * Escape Field.
		 */
		public static function escape_link( $check, $meta_value, $field_args, $field_object ) {

			if ( ! is_array( $meta_value ) || ! $field_args['repeatable'] ) {
				return $check;
			}
			foreach ( $meta_value as $key => $val ) {
				$meta_value[ $key ] = array_filter( array_map( 'esc_attr', $val ) );
			}
			return array_filter( $meta_value );
		}

	}
	$cmb2_field_link = new CMB2_Field_Link();
}
?>
