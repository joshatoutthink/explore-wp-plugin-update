<?php
/**
 * Plugin Name:TEST Wp Automatic Plugin Update
 * Plugin URI: 
 * Description: TESTING AND LEARNING HOW TO BUILD A PLUGIN UPDATE SYSTEM
 * Version: 1.5
 * Author: josh
 * Author URI: 
 */

define('WP_UPDATE_MANAGER_PATH', plugin_dir_path(__FILE__));
define('WP_UPDATE_MANAGER_URL', plugin_dir_url(__FILE__));

//  add_action('init', function(){
//    require WP_UPDATE_MANAGER_PATH . '/classes/class-wp-update-manager.php';
//  });

define('MY_PLUGINS_SLUG', "test-wp-automatic-plugin-update");

/* 

$transient: object with keys of all plugins information
if our plugin needs to be updated we need to tell wordpress by adding it to the transient response list with our plugins information
*/
add_filter ( 'pre_set_site_transient_update_plugins', 'TEST_on_update_plugin');

function TEST_on_update_plugin($transient){

  if (empty ( $transient->checked )){
        return $transient;
  }            

  $plugin='explore-wp-plugin-update/'.MY_PLUGINS_SLUG.'.php';
  $new_version='7.0'; // this should be dynamic: wp_remote_get()
  $plugin_info = get_site_transient('update_plugins');
  $current_version = $plugin_info->checked[$plugin];

  // make sure we do nothing when the current version seems to be greater than the "new" one
  if (version_compare ( $current_version, $new_version, '>' )) {
        return $transient;
  }
  // create our response
  $obj = new \stdClass ();
  $obj->plugin = $plugin;
  $obj->slug = MY_PLUGINS_SLUG;
  $obj->new_version = $new_version;
  $obj->package = 'https://raw.githubusercontent.com/joshatoutthink/explore-wp-plugin-update/main/explore-wp-plugin-update.zip';

  // here we inject our response to the given $transient
  $transient->response[$obj->plugin] = $obj;
              
  return $transient;
}


/* 
This adds the details link and the update link if our plugin should update in the modal
*/
add_filter ( 'plugins_api', 'TEST_on_plugins_api', 10, 3 );

function TEST_on_plugins_api($result, $action, $args) {

  $plugin_slug=MY_PLUGINS_SLUG;
  $plugin='explore-wp-plugin-update/'.MY_PLUGINS_SLUG.'.php';
  if ($args->slug != $plugin_slug){
    return $result;
  }

  $plugin_info = get_site_transient ( 'update_plugins' );
  $args->version = $plugin_info->checked[$plugin]; //
  
  // Create our object which should includes everything
  // see wp-admin/includes/plugin-install.php
  $obj = new \stdClass ();

  $obj->version = $args->version;
  $obj->new_version = '7.0';
  $obj->author = 'Josh Kennedy';
  $obj->slug = $plugin_slug;
  $obj->name = 'TEST Wp Automatic Plugin Update';
  $obj->plugin_name = $plugin_slug;
  $obj->description = 'This is the plugin description';
  $obj->requires = '3.0';
  $obj->tested = '4.3.1';
  $obj->last_updated = date ( 'Y-m-d' );
  $obj->download_link = 'https://raw.githubusercontent.com/joshatoutthink/explore-wp-plugin-update/main/explore-wp-plugin-update.zip';
  $obj->sections = array (
                  'description' => 'A',
                  'changelog' => '<h4>7.0</h4><ul><li>Fix - super cool fix</li><li>Tweak - super hot tweak</li></ul>',
                  'another_section' => 'C' 
      );
  
  return $obj;
}
