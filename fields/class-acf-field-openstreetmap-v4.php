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
			'address'		=> '',
			'tiles'			=> 'osm_basic',
			'height'		=> 500,
			'center_lat'	=> 45.38350155204992,
			'center_lng'	=> 4.501991271972657,
			'zoom'			=> 11,
			'min_zoom'		=> 2,
			'max_zoom'		=> 18,
			'front_display'	=> 'map',
		);
		
		parent::__construct();
		
		$this->settings = $settings;
		
		wp_enqueue_style( 'acf-leaflet', $this->settings['url'].'assets/css/leaflet.css' );
		wp_enqueue_script( 'acf-leaflet', $this->settings['url'].'assets/js/leaflet.js' );
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
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Front end display",'acf-openstreetmap'); ?></label>
				<p><?php _e("Wether to display Leaflet map or just values front end",'acf-openstreetmap'); ?></p>
			</td>
			<td>
<?php 
				do_action('acf/create_field', array(
					'type'		=>	'select',
					'name'		=>	'fields[' .$key.'][front_display]',
					'value'		=>	$field['front_display'],
					'choices'	=>	array( 'map' => __('Map','acf-openstreetmap'), 'values' => __('Values','acf-openstreetmap') ),
				));
?>
			</td>
		</tr>
<?php
	}
	
	function create_field( $field ) {
		$id = 'leaflet-'.$field['key'];
		$lat = !empty($field['value']['center_lat']) ? $field['value']['center_lat'] : $field['center_lat'];
		$lng = !empty($field['value']['center_lng']) ? $field['value']['center_lng'] : $field['center_lng'];
		$address = !empty($field['value']['address']) ? $field['value']['address'] : $field['address'];
		$tiles = $this->settings['layers'][$field['tiles']];
?>
		<div class="acf-openstreetmap">
			<input id="acf-openstreetmap-lat" type="hidden" name="<?php echo esc_attr($field['name']); ?>[center_lat]" value="<?php echo $lat; ?>" />
			<input id="acf-openstreetmap-lng" type="hidden" name="<?php echo esc_attr($field['name']); ?>[center_lng]" value="<?php echo $lng; ?>" />
			
			<div class="acf-input-wrap"><input id="nominatim" type="text" name="<?php echo esc_attr($field['name']); ?>[address]" value="<?php echo $address; ?>" /></div>
			
			<div id="<?php echo $id; ?>" style="height: <?php echo $field['height']; ?>px"></div>
			
			<script>window.onload = function() {
				
				L.Icon.Default.imagePath = '<?php echo $this->settings['url'].'assets/images/'; ?>';
				
				var acfLeafletMap = L.map( <?php echo $id; ?> ).setView([<?php echo $lat; ?>,<?php echo $lng; ?>],<?php echo $field['zoom']; ?>);
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
				
				/*$("#nominatim").autocomplete({
					source: 'https://benoit.maiorino.fr/geocoding.php',
					minLength: 3,
					select: function( event, ui ) {
						console.log( ui );
					}
				});*/
			};</script>
		</div>
<?php
	}
	
	function input_admin_enqueue_scripts() {
		// vars
		$url = $this->settings['url'];
		$version = $this->settings['version'];
		
		// register & include JS
		wp_register_script('acf-openstreetmap-js', "{$url}assets/js/input.js", array('acf-input'), $version);
		wp_enqueue_script('acf-openstreetmap-js');
		
		// register & include CSS
		wp_register_style('acf-openstreetmap-css', "{$url}assets/css/input.css", array('acf-input'), $version);
		wp_enqueue_style('acf-openstreetmap-css');
	}
	
	function format_value_for_api( $value, $post_id, $field ) {
		if( $field['front_display'] == 'map' ) {
			
			$id = 'leaflet-'.$field['key'].'-'.$post_id;
			$lat = $field['value'] ? $field['value']['center_lat'] : $field['center_lat'];
			$lng = $field['value'] ? $field['value']['center_lng'] : $field['center_lng'];
			$tiles = $this->settings['layers'][$field['tiles']];
			$value = '
			<div id="'.$id.'" style="height: '.$field['height'].'px"></div>
			<script>
				window.addEventListener( "load", function() {
					L.Icon.Default.imagePath = "'.$this->settings['url'].'assets/images/";
					var acfLeafletMap'.$post_id.' = L.map( "'.$id.'" ).setView(['.$lat.','.$lng.'],'.$field['zoom'].');
					L.tileLayer( "'.$tiles['url'].'", {
						maxZoom: '.$field['max_zoom'].',
						minZoom: '.$field['min_zoom'].',
						attribution: \''.$tiles['attribution'].'\'
					}).addTo(acfLeafletMap'.$post_id.');
					var marker = L.marker(['.$lat.','.$lng.']).addTo(acfLeafletMap'.$post_id.');
				});
			</script>';
		}
		
		return $value;
	}
}

new acf_field_openstreetmap( $this->settings );

endif;
?>
