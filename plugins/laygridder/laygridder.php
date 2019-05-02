<?php
/**
 * Plugin Name: LayGridder
 * Plugin URI: http://laygridder.com
 * Description: A Layout Plugin for WordPress developers.
 * Version: 1.1.0
 * Author: Armin Unruh, Felix Albert
 * Author URI: http://laygridder.com
 */

$layGridderVer = '1.1.0';

register_activation_hook( __FILE__, 'LayGridderSetup::die_if_laytheme_active' );
register_activation_hook( __FILE__, 'LayGridderSetup::lg_add_custom_capability' );

define( 'LG_TEST', false );
define( 'LG_PLUGIN_PATH', plugin_dir_path(__FILE__) );
define( 'LG_PLUGIN_FILE_PATH', __FILE__ );
define( 'LG_PLUGIN_URL', plugins_url( 'laygridder' ));
define( 'LG_VER', $layGridderVer);

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

require LG_PLUGIN_PATH.'qtranslatex_integration/qtranslatex_integration.php';
require LG_PLUGIN_PATH.'util/util.php';
require LG_PLUGIN_PATH.'gridder/constants.php';
require LG_PLUGIN_PATH.'setup/setup.php';
require LG_PLUGIN_PATH.'location/location.php';
require LG_PLUGIN_PATH.'options/options.php';
require LG_PLUGIN_PATH.'gridder/gridder.php';
require LG_PLUGIN_PATH.'gridder/category_gridder.php';
require LG_PLUGIN_PATH.'gridder/post_thumbnail.php';
require LG_PLUGIN_PATH.'frontend/frontend.php';
require LG_PLUGIN_PATH.'fontmanager/fontmanager.php';
require LG_PLUGIN_PATH.'formatsmanager/formatsmanager.php';
require LG_PLUGIN_PATH.'gridderdefaults/gridderdefaults.php';
require LG_PLUGIN_PATH.'templates/templates.php';
require LG_PLUGIN_PATH.'options/license_key.php';
require LG_PLUGIN_PATH.'updatejson/update_thumbnails.php';
require LG_PLUGIN_PATH.'updatejson/update_imagelinks.php';
require LG_PLUGIN_PATH.'gridder/cmb2_integration.php';

if(LG_TEST == true){
	add_theme_support( 'post-thumbnails' ); 
	require LG_PLUGIN_PATH.'tests/custom_post_type.php';
	require LG_PLUGIN_PATH.'tests/custom_taxonomy.php';
	require LG_PLUGIN_PATH.'tests/filter_for_bg_color_palette.php';
	require LG_PLUGIN_PATH.'tests/cmb2_user/user_cmb2_boxes.php';
	require LG_PLUGIN_PATH.'tests/filters.php';
}

require 'plugin_update_check.php';
$MyUpdateChecker = new PluginUpdateChecker_2_0 (
	'https://kernl.us/api/v1/updates/5a87e0f575bd673a0586df34/',
	__FILE__,
	'laygridder',
	1
);