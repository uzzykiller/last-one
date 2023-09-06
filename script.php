<?php
// public enqueue scripts 
function public_lat_one_plugin_script() 
{
    wp_enqueue_style( 'last_one_style', plugin_dir_url( __FILE__ ) . 'public/assets/css/public-style.css');
    wp_enqueue_script( 'last_one_js', plugin_dir_url( __FILE__ ) . 'public/assets/js/public-lastone.js',array('jquery'), '', true);
}
add_action( 'wp_enqueue_scripts', 'public_lat_one_plugin_script' );

// admin enqueue script 

function register_my_plugin_scripts()
{
    wp_enqueue_style('my_custom_css', plugin_dir_url(__FILE__) . 'admin/assets/css/last-one.css');
    wp_enqueue_script('my_custom_js', plugin_dir_url(__FILE__) . 'admin/assets/js/last-one.js', array('jquery'), '', true);
    // Enqueue jQuery UI in the admin area
    wp_enqueue_script('jquery-ui', 'https://code.jquery.com/ui/1.12.1/jquery-ui.min.js', array('jquery'), '1.12.1', true);
}
add_action('admin_enqueue_scripts', 'register_my_plugin_scripts');
