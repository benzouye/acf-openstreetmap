<?php
if( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists('acf_field_openstreetmap') ) :

class acf_field_openstreetmap extends acf_field {
	
	function __construct( $settings ) {
		
		$this->name = 'openstreetmap';
		$this->label = __('OpenStreetMap', 'acf-openstreetmap');
		$this->category = 'content';
		$this->defaults = array(
			'tiles'			=> 'osm_basic',
			'height'		=> 500,
			'center_lat'	=> 45.38350155204992,
			'center_lng'	=> 4.501991271972657,
			'zoom'			=> 11,
			'min_zoom'		=> 2,
			'max_zoom'		=> 18
		);
		$this->l10n = array(
			'error'	=> __('Error! Please enter a higher value', 'acf-openstreetmap'),
		);
		$this->settings = $settings;
		
    	parent::__construct();
			add_filter('acf/format_value', array($this, 'format_value_for_api'), 10, 3);
	}
	
	function render_field_settings( $field ) {
		
		acf_render_field_setting( $field, array(
			'label'			=> __('Tiles','acf-openstreetmap'),
			'instructions'	=> __('Map tiles type','acf-openstreetmap'),
			'type'			=> 'select',
			'name'			=> 'tiles',
			'choice'		=> $this->settings['layers_select'],
		));
		
		acf_render_field_setting( $field, array(
			'label'			=> __('Map height','acf-openstreetmap'),
			'instructions'	=> __('Height size of the map in pixels','acf-openstreetmap'),
			'type'			=> 'number',
			'name'			=> 'height',
			'prepend'		=> 'px',
		));
		
		acf_render_field_setting( $field, array(
			'label'			=> __('Zoom level','acf-openstreetmap'),
			'instructions'	=> __('Default zoom level on map opening (from 0 to 20)','acf-openstreetmap'),
			'type'			=> 'number',
			'name'			=> 'zoom',
		));
		acf_render_field_setting( $field, array(
			'label'			=> __('Default map center latitude','acf-openstreetmap'),
			'type'			=> 'number',
			'name'			=> 'center_lat',
		));
		
		acf_render_field_setting( $field, array(
			'label'			=> __('Default map center latitude','acf-openstreetmap'),
			'type'			=> 'number',
			'name'			=> 'center_lng',
		));
		
		acf_render_field_setting( $field, array(
			'label'			=> __('Minimum zoom level','acf-openstreetmap'),
			'instructions'	=> __('Minimum possible zoom level (from 0 to 20)','acf-openstreetmap'),
			'type'			=> 'number',
			'name'			=> 'min_zoom',
		));
		
		acf_render_field_setting( $field, array(
			'label'			=> __('Maximum zoom level','acf-openstreetmap'),
			'instructions'	=> __('Maximum possible zoom level (from 0 to 20)','acf-openstreetmap'),
			'type'			=> 'number',
			'name'			=> 'max_zoom',
		));
		
	}
	
	function render_field( $field ) {
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
		if( $field['type']=='openstreetmap' ) {
			wp_enqueue_style( 'acf-leaflet', $this->settings['url'].'assets/css/leaflet.css' );
			wp_enqueue_script( 'acf-leaflet', $this->settings['url'].'assets/js/leaflet.js' );

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