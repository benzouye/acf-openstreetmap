<?php
if( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists('acf_field_openstreetmap') ) :

class acf_field_openstreetmap extends acf_field {
	
	var $settings, $defaults;
	
	function __construct( $settings ) {
		$this->name = 'openstreetmap';
		$this->label = 'OpenStreetMap';
		$this->category = __('Content','acf'); // Basic, Content, Choice, etc
		$this->defaults = array(
			'tiles'			=> 'osm_basic',
			'height'		=> 500,
			'center_lat'	=> 45.38350155204992,
			'center_lng'	=> 4.501991271972657,
			'zoom'			=> 11,
			'min_zoom'		=> 2,
			'max_zoom'		=> 18
		);
		
		parent::__construct();
		
		$this->settings = $settings;
	}
	
	function create_options( $field ) {
		// key is needed in the field names to correctly save the data
		$key = $field['name'];
		
		
		// Create Field Options HTML
?>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Tiles",'acf-openstreetmap'); ?></label>
				<p><?php _e("Map tiles type",'acf-openstreetmap'); ?></p>
			</td>
			<td>
<?php 
				do_action('acf/create_field', array(
					'type'		=>	'select',
					'name'		=>	'fields[' .$key.'][tiles]',
					'value'		=>	$field['tiles'],
					'choices'	=>	$this->settings['layers_select'],
				));
?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Map height",'acf-openstreetmap'); ?></label>
				<p><?php _e("Height size of the map in pixels (px)",'acf-openstreetmap'); ?></p>
			</td>
			<td>
<?php 
				do_action('acf/create_field', array(
					'type'	=>	'number',
					'step'	=>	1,
					'name'	=>	'fields[' .$key.'][height]',
					'value'	=>	$field['height'],
				));
?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Zoom level",'acf-openstreetmap'); ?></label>
				<p><?php _e("Default zoom level on map opening (from 0 to 20)",'acf-openstreetmap'); ?></p>
			</td>
			<td>
<?php 
				do_action('acf/create_field', array(
					'type'	=>	'number',
					'step'	=>	1,
					'name'	=>	'fields[' .$key.'][zoom]',
					'value'	=>	$field['zoom'],
				));
?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Minimum zoom level",'acf-openstreetmap'); ?></label>
				<p><?php _e("Minimum possible zoom level (from 0 to 20)",'acf-openstreetmap'); ?></p>
			</td>
			<td>
<?php 
				do_action('acf/create_field', array(
					'type'	=>	'number',
					'step'	=>	1,
					'name'	=>	'fields[' .$key.'][min_zoom]',
					'value'	=>	$field['min_zoom'],
				));
?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Maximum zoom level",'acf-openstreetmap'); ?></label>
				<p><?php _e("Maximum possible zoom level (from 0 to 20)",'acf-openstreetmap'); ?></p>
			</td>
			<td>
<?php 
				do_action('acf/create_field', array(
					'type'	=>	'number',
					'step'	=>	1,
					'name'	=>	'fields[' .$key.'][max_zoom]',
					'value'	=>	$field['max_zoom'],
				));
?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Default map center latitude",'acf-openstreetmap'); ?></label>
			</td>
			<td>
<?php 
				do_action('acf/create_field', array(
					'type'	=>	'number',
					'step'	=>	1,
					'name'	=>	'fields[' .$key.'][center_lat]',
					'value'	=>	$field['center_lat'],
				));
?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Default map center longitude",'acf-openstreetmap'); ?></label>
			</td>
			<td>
<?php 
				do_action('acf/create_field', array(
					'type'	=>	'number',
					'step'	=>	1,
					'name'	=>	'fields[' .$key.'][center_lng]',
					'value'	=>	$field['center_lng'],
				));
?>
			</td>
		</tr>
<?php
	}
	
	function create_field( $field ) {
		$lat = $field['value'] ? $field['value']['center_lat'] : $field['center_lat'];
		$lng = $field['value'] ? $field['value']['center_lng'] : $field['center_lng'];
		$tiles = $this->settings['layers'][$field['tiles']];
?>
		<div class="acf-openstreetmap">
			<input id="acf-openstreetmap-lat" type="hidden" name="<?php echo esc_attr($field['name']); ?>[center_lat]" value="<?php echo $lat; ?>" />
			<input id="acf-openstreetmap-lng" type="hidden" name="<?php echo esc_attr($field['name']); ?>[center_lng]" value="<?php echo $lng; ?>" />
			
			<div id="leaflet-<?php echo $field['key']; ?>" style="height: <?php echo $field['height']; ?>px"></div>
			<script>window.onload = function() {
				L.Icon.Default.imagePath = '<?php echo $this->settings['url'].'assets/images/'; ?>';
				var acfLeafletMap = L.map( 'leaflet-<?php echo $field['key']; ?>' ).setView([<?php echo $lat; ?>,<?php echo $lng; ?>],<?php echo $field['zoom']; ?>);
				L.tileLayer( '<?php echo $tiles['url']; ?>', {
					maxZoom: <?php echo $field['max_zoom']; ?>,
					minZoom: <?php echo $field['min_zoom']; ?>,
					attribution: '<?php echo $tiles['attribution']; ?>'
				}).addTo(acfLeafletMap);
				var marker = L.marker([<?php echo $lat; ?>,<?php echo $lng; ?>]).addTo(acfLeafletMap);
				acfLeafletMap.on( 'click', function(e) {
					acfLeafletMap.eachLayer( function(e) {
						if( e instanceof L.Marker ) {
							acfLeafletMap.removeLayer(e);
						}
					});
					var marker = L.marker([e.latlng.lat, e.latlng.lng]).addTo(acfLeafletMap);
					$("#acf-openstreetmap-lat").val( e.latlng.lat );
					$("#acf-openstreetmap-lng").val( e.latlng.lng );
				});
			};</script>
		</div>
<?php
	}
	
	function input_admin_enqueue_scripts() {
		// vars
		$url = $this->settings['url'];
		$version = $this->settings['version'];
		
		// require the leaflet JS and CSS
		wp_enqueue_style( 'acf-leaflet-css', plugin_dir_url( __FILE__ ).'../assets/css/leaflet.css' );
		wp_enqueue_script( 'acf-leaflet-js', plugin_dir_url( __FILE__ ).'../assets/js/leaflet.js' );
		
		// register & include JS
		wp_register_script('acf-openstreetmap-js', "{$url}assets/js/input.js", array('acf-input'), $version);
		wp_enqueue_script('acf-openstreetmap-js');
		
		// register & include CSS
		wp_register_style('acf-openstreetmap-css', "{$url}assets/css/input.css", array('acf-input'), $version);
		wp_enqueue_style('acf-openstreetmap-css');
	}
	
	function format_value_for_api( $value, $post_id, $field ) {
		$this->input_admin_enqueue_scripts();
		$lat = $field['value'] ? $field['value']['center_lat'] : $field['center_lat'];
		$lng = $field['value'] ? $field['value']['center_lng'] : $field['center_lng'];
		$tiles = $this->settings['layers'][$field['tiles']];
		$value = '
		<div id="leaflet-'.$field['key'].'" style="height: '.$field['height'].'px"></div>
		<script>
			window.onload = function() {
				L.Icon.Default.imagePath = "'.$this->settings['url'].'assets/images/";
				var acfLeafletMap = L.map( "leaflet-'.$field['key'].'" ).setView(['.$lat.','.$lng.'],'.$field['zoom'].');
				L.tileLayer( "'.$tiles['url'].'", {
					maxZoom: '.$field['max_zoom'].',
					minZoom: '.$field['min_zoom'].',
					attribution: \''.$tiles['attribution'].'\'
				}).addTo(acfLeafletMap);
				var marker = L.marker(['.$lat.','.$lng.']).addTo(acfLeafletMap);
			}
		</script>';
		
		return $value;
	}
}

new acf_field_openstreetmap( $this->settings );

endif;
?>
