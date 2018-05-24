<?php
/*
Plugin Name: ACF OpenStreetMap custom field type
Description: Custom field type for ACF plugin to use Leaflet/OpenStreetMap
Version: 1.0.0
Author: Benzouye
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/
if( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists('acf_plugin_openstreetmap') ) :

class acf_plugin_openstreetmap {
	
	var $settings;
	
	function __construct() {
		
		// Get JSON layers info
		$filename = plugin_dir_path( __FILE__ ).'tiles.json';
		$layers = array();
		$layers_select = array();
		if( file_exists( $filename ) ) {
			$json = file_get_contents( $filename );
			$layers = json_decode( $json, true );
			foreach( $layers as $code => $args ) {
				$layers_select[$code] = $args['title'];
			}
		}
		
		$this->settings = array(
			'version'		=> '1.0.0',
			'url'			=> plugin_dir_url( __FILE__ ),
			'path'			=> plugin_dir_path( __FILE__ ),
			'layers'		=> $layers,
			'layers_select'	=> $layers_select
		);
		
		add_action('acf/include_field_types', 	array($this, 'include_field')); // v5
		add_action('acf/register_fields', 		array($this, 'include_field')); // v4
	}
	
	function include_field() {
		load_plugin_textdomain( 'acf-openstreetmap', false, plugin_basename( dirname( __FILE__ ) ) . '/lang' );
		include_once('fields/class-acf-field-openstreetmap-v' . intval( get_option('acf_version', false) ) . '.php');
	}
}

new acf_plugin_openstreetmap();

endif;
?>