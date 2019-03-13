<?php
/**
 * @package	CMB2\Field_Link
 * @author 	scottsawyer
 * @copyright	Copyright (c) scottsawyer
 *
 * Plugin Name: CMB2 Field Type: Link
 * Plugin URI: https://github.com/scottsawyer/cmb2-field-link
 * Github Plugin URI: https://github.com/scottsawyer/cmb2-field-link
 * Description: CMB2 field type to create a link.
 * Version: 1.0
 * Author: scottsawyer
 * Author URI: https://www.scottsawyerconsulting.com
 * License: GPLv2+
 */

if ( !defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'CMB2_Field_Link' ) ) {
  /**
   * Class CMB2_Field_Link
   */
  class CMB2_Field_Link  {
    
    /**
     * Current version number
     */
    const VERSION = '1.0.0';

    /**
     * Initialize the plugin
     */
    public function __construct() {
      add_action( 'cmb2_render_link', [$this, 'render_link'], 10, 5 );
      add_filter( 'cmb2_sanitize_link', [$this, 'maybe_save_split_values'], 12, 4 );
      add_filter( 'cmb2_sanitize_link', [$this, 'sanitize_link'], 10, 5 );
      add_filter( 'cmb2_types_esc_link', [$this, 'escape_link'], 10, 4 );
    }
    
    public function render_link( $field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object ) {
    
      // the properties of the fields.

      $field_escaped_value = wp_parse_args( $field_escaped_value, [
        'href'	=> '',
        'text'	=> '',
        'class'	=> '',
        'rel'	=> '',
        'title' => '',
        'target' => '',
      ] );

      ?>
      <div style="overflow: hidden; display: inline-block; width: 49%;">
        <h4 style="margin: 0 0 8px 0;"><label for="<?= $field_type_object->_id( '_text' ); ?>"><?= esc_html( 'Text'  ); ?></label></h4>
        <?= $field_type_object->input( [
          'type' => 'text',
          'name' => $field_type_object->_name( '[text]' ),
          'id' => $field_type_object->_id( '_text' ),
          'value' => $field_escaped_value['text'],
          'desc' => '',
        ] ); ?>
        <p style="margin-top: 5px; padding-top: 0;"><small>The Link Text.</small></p>
      </div>
      <div style="overflow: hidden; display: inline-block; width: 49%;">
        <h4 style="margin: 0 0 8px 0;"><label for="<?= $field_type_object->_id( '_href' ); ?>"><?= esc_html( 'URL' ); ?></label></h4>
        <?= $field_type_object->input( [
          'type' => 'text_url',
          'name' => $field_type_object->_name( '[href]' ),
          'id' => $field_type_object->_id( '_href' ),
          'value' => $field_escaped_value['href'],
          'desc' => '',
        ] ); ?>
        <p style="margin-top: 5px; padding-top: 0;"><small>The URL of the link.</small></p>
      </div>
      <div style="overflow: hidden; display: inline-block; width: 49%;">
        <h4 style="margin: 0 0 8px 0;"><label for="<?= $field_type_object->_id( '_class' ); ?>"><?= esc_html( 'Classes' ); ?></label></h4>
        <?= $field_type_object->input( [
          'type' => 'text',
          'name' => $field_type_object->_name( '[class]' ),
          'id' => $field_type_object->_id( '_class' ),
          'value' => $field_escaped_value['class'],
          'desc' => '', 
        ] ); ?>
        <p style="margin-top: 5px; padding-top: 0;"><small>CSS classes, separated with a space.</small></p>
      </div>
      <div style="overflow: hidden; display: inline-block; width: 49%;">
        <h4 style="margin: 0 0 8px 0;"><label for="<?= $field_type_object->_id( '_rel' ); ?>"><?= esc_html( 'Rel' ); ?></label></h4>
        <?= $field_type_object->input( [
          'type' => 'text',
          'name' => $field_type_object->_name( '[rel]' ),
          'id' => $field_type_object->_id( '_rel' ),
          'value' => $field_escaped_value['rel'],
          'desc' => '',
        ] ); ?>
        <p style="margin-top: 5px; padding-top: 0;"><small>The rel property ( "nofollow" ).</small></p>
      </div>
      <div style="overflow: hidden; display: inline-block; width: 49%;">
        <h4 style="margin: 0 0 8px 0;"><label for="<?= $field_type_object->_id( '_title' ); ?>"><?= esc_html( 'Title' ); ?></label></h4>
        <?= $field_type_object->input( [
          'type' => 'text',
          'name' => $field_type_object->_name( '[title]' ),
          'id' => $field_type_object->_id( '_title' ),
          'value' => $field_escaped_value['title'],
          'desc' => '',
        ] ); ?>
        <p style="margin-top: 5px; padding-top: 0;"><small>The title property ( Shown on hover ).</small></p>
      </div>
      <div style="overflow: hidden; display: inline-block; width: 49%;">
        <h4 style="margin: 0 0 8px 0;"><label for="<?= $field_type_object->_id( '_target' ); ?>"><?= esc_html( 'Target' ); ?></label></h4>
        <?= $field_type_object->input( [
          'type' => 'text',
          'name' => $field_type_object->_name( '[target]' ),
          'id' => $field_type_object->_id( '_target' ),
          'value' => $field_escaped_value['target'],
          'desc' => '',
        ] ); ?>
        <p style="margin-top: 5px; padding-top: 0;"><small>Defaults to "_self"</small></p>
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

      $link_keys = ['href', 'text', 'class', 'rel', 'title', 'target'];

      foreach ( $link_keys as $key ) {
        if ( ! empty( $value[ $key ] ) ) {
          update_post_meta( $object_id, $field_args['id'] . 'link_'. $key, sanitize_text_field( $value[ $key ] ) );
        }
      }
      remove_filter( 'cmb2_sanitize_link', [$this, 'sanitize'], 10, 5 );

      return true;
    }
    /**
     * Santize Field.
     */
    public static function sanitize_link( $check, $meta_value, $object_id, $field_args, $sanitize_object ) {

      if ( !is_array( $meta_value ) || !( array_key_exists('repeatable', $field_args ) && $field_args['repeatable'] == TRUE ) ) {
        return $check;
      }
      foreach ( $meta_value as $key => $val ) {

        $meta_value[$key] = array_filter( array_map( 'sanitize_text_field', $val ) );

      }

      return array_filter( $meta_value );
    }
    /**
     * Escape Field.
     */
    public static function escape_link( $check, $meta_value, $field_args, $field_object ) {
     

      if ( !is_array( $meta_value ) || ! $field_args['repeatable'] ) {
        return $check;
      }
      foreach ( $meta_value as $key => $val ) {
        $meta_value[$key] = array_filter( array_map( 'esc_attr', $val ) );
      }
      return array_filter( $meta_value );
    }

  }
  $cmb2_field_link = new CMB2_Field_Link();
}
?>
