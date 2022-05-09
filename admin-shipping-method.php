<?php

/**
* Plugin Name: Admin Shipping Methods
* Description: Adds table rate shipping methods to admin add order page.
* Version: 0.1
* Author: TapaCode
* Author URI: http://www.tapacode.com
*/

// Hook button into order interface
add_action( 'woocommerce_order_item_add_action_buttons', 'add_shipping_method_button', 10, 1);

// Create button
function add_shipping_method_button( $order ) {
    echo '<button id="add_shipping_method" type="button" class="button generate-items"
        data-order_id="'. esc_attr($order->get_id())  .'">' . __( 'Add Shipping Method', 'hungred' ) . '</button>';
};

/*
* Add Javascript
*/

function add_admin_shipping_method_script() {
    wp_enqueue_script( 'admin_shipping_method_script', plugin_dir_url(__FILE__) ."/assets/admin-shipping-method.js", array('jquery'), NULL, true
);
    // send the admin ajax url to the script
    wp_localize_script( 'admin_shipping_method_script', 'tapa_shipping_var', array( 'ajaxurl' => admin_url(
    'admin-ajax.php' ) ) );
}
/**
* hook to add the javascript file
*/
add_action( 'admin_enqueue_scripts', 'add_admin_shipping_method_script' );


/**
* Ajax callback
*/
function get_shipping_methods() {

  global $woocommerce;

  $state ='ME';
  $postcode = '04101';
  $city = 'Portland';


  $bh_packages = $woocommerce->cart->get_shipping_packages();
  $bh_packages[0]['destination']['state'] = $state;
  $bh_packages[0]['destination']['postcode'] = $postcode;
  $bh_packages[0]['destination']['city'] = $cityl;

  //Calculate costs for passed packages
  $bh_shipping_methods = array();

  foreach( $bh_packages as $bh_package_key => $bh_package ) {
    $bh_shipping_methods[$bh_package_key] = $woocommerce->shipping->calculate_shipping_for_package($bh_package,
    $bh_package_key);
  }
  $shippingArr = $bh_shipping_methods[0]['rates'];

  if(!empty($shippingArr)) {
    $response = array();
    foreach ($shippingArr as $value) {
        $shipping['label'] = $value->label;
        $shipping['cost'] = $value->cost;
        $response['shipping'][] = $shipping;
    }
  }


    wp_send_json( $response );
}



/**
* hook to add the ajax callback
*/
add_action( 'wp_ajax_get_shipping_methods', 'get_shipping_methods' );

?>