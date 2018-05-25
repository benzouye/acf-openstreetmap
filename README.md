# acf-openstreetmap
WordPress plugin to create OpenStreetMap with Advanced Custom Fields plugin

## Install
First, ensure you have the Advanced Custom Fields (ACF) WordPress plugin installed and activated on your WordPress running site.
Then :
- Download ZIP file from GitHub repository.
- Extract files into your WordPress /wp-content/plugins/ directory
- In the WordPress extensions dashboard, activite this plugin
- From now, when creating a new custom field with ACF plugin you'll have an "OpenStreetMap" type choice in "Content" group

## Use OpenStreetMap field type
When choosing "OpenStreetMap" field type in ACF dashboard, you can define :
- Tiles : the different layer types available (check tiles.json file to discover list of layer and cutom it if necessary)
- Map height : the height the map will be displayed in content managing screen
- Zoom level : the default zoom level the map will be displayed
- Minimum zoom level : the minimum zoom level the user will be allowed to use
- Maximum zoom level : the maximum zoom level the user will be allowed to use
- Default map center latitude : the default map center latitude when it is displayed
- Default map center longitude : the default map center longitude when it is displayed
- Front end display : the way that field is displayed on front-end (Leaflet map or juste values)

## Usage
On a content management screen, if a "OpenStreetMap" type field is available, a map is displayed. The user can click on the map to change default position defined previously. This position is saved.
In your WordPress theme templates, you can use this ACF field as usual with the_field() fonction, it displays the map as intended.

##TODO
Geocoding (free as in free speech)
