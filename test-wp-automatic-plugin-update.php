<?php
/**
 * Plugin Name:TEST Wp Automatic Plugin Update
 * Plugin URI: 
 * Description: TESTING AND LEARNING HOW TO BUILD A PLUGIN UPDATE SYSTEM
 * Version: 1.0
 * Author: josh
 * Author URI: 
 */

define('WP_UPDATE_MANAGER_PATH', plugin_dir_path(__FILE__));
define('WP_UPDATE_MANAGER_URL', plugin_dir_url(__FILE__));

 add_action('init', function(){
   require WP_UPDATE_MANAGER_PATH . '/classes/class-wp-update-manager.php';
 });
