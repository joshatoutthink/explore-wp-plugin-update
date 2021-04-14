<?php
if(!class_exists( 'WP_Update_Manager' )){
  class WP_Update_Manager{

    function __construct(){
      add_action('wp_enqueue_scripts', array($this, 'enqueue_all'));
    }
    public function enqueue_all(){
      wp_enqueue_script('main-script', WP_UPDATE_MANAGER_URL . '/dist/main.js', array(), true);
      wp_enqueue_style('main-styles', WP_UPDATE_MANAGER_URL . '/dist/main.css', '1.00' , all);
    }
  }
  new WP_Update_Manager();

}