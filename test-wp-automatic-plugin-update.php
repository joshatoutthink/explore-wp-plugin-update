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

add_filter('plugins_api', 'TEST_plugin_info', 20,3);
function TEST_plugin_info($res, $action, $args){
  if('plugin_information' !== $action ){
    return false;
  }

  $plugin_slug = MY_PLUGINS_SLUG;

  if($plugin_slug !== $args->slug){
    return false;
  }

  $remote = get_transient('update_tester_update'. $plugin_slug);
  if($remote == false){

    $remote = wp_remote_get('https://github.com/joshatoutthink/explore-wp-plugin-update/raw/main/info.json', [
      'timeout'=>10,
      'headers'=> [
        'Accept' => 'application/json',
      ]
    ]);

    if(! is_wp_error($remote) && isset($remote['response']['code']) && $remote['response']['code'] == 200 && !empty($remote['body'])) {
      set_transient('update_tester_update'.$plugin_slug, $remote, 43200);
    }
  }

  if(! is_wp_error($remote) && isset($remote['response']['code']) && $remote['response']['code'] == 200 && !empty($remote['body'])) {
    $json = json_decode($remote['body']);
    $res = new stdClass();

    $res->name = $json->name;
    $res->slug = $plugin_slug;
    $res->version = $json->version;
    $res->tested = $json->tested;
    $res->requires = $json->requires;
    $res->download_link = $json->download_url;
    $res->trunk = $json->download_url;
    $res->requires_php = '5.3';
    $res->last_updated = $json->last_updated;

    return $res;  
  }

  return false;
  
}

add_filter('site_transient_update_plugins', 'TEST_push_update');
function TEST_push_update($transient){
  
  if(empty($transient->checked)){
    return $transient;
  }

  $remote = get_transient('update_tester_upgrade'. MY_PLUGINS_SLUG);
  if($remote==false){
    $remote = wp_remote_get('https://github.com/joshatoutthink/explore-wp-plugin-update/raw/main/info.json',[
			'timeout' => 10,
			'headers' => [
				'Accept' => 'application/json'
			] 
    ]);
 
		if ( !is_wp_error( $remote ) && isset( $remote['response']['code'] ) && $remote['response']['code'] == 200 && !empty( $remote['body'] ) ) {
			set_transient( 'update_tester_upgrade'. MY_PLUGINS_SLUG, $remote, 43200 ); // 12 hours cache
		}

    if($remote){
      $json = json_decode($remote['body']);

      if($json && version_compare('1.0', $json->version, '<') && version_compare($remote->requires, get_bloginfo('version'), '<')){

        $res = new stdClass();
        
        $res->slug = $json->slug;
        $res->plugin = 'explore-wp-plugin-update/'.MY_PLUGINS_SLUG;
        $res->new_version = $json->version;
        $res->tested = $json->tested;
        $res->package = $json->download_url;
        
        $transient->response[$res->plugin] = $res;
      }
    }
 
	}

  return $transient;
}
