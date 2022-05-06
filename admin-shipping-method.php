<?php

/**
* Plugin Name: Admin Shipping Methods
* Description: Adds table rate shipping methods to admin add order page.
* Version: 0.1
* Author: TapaCode
* Author URI: http://www.tapacode.com
*/

// defined( 'ABSPATH' ) || exit;

/*function shipping() {

    global $woocommerce;

    $active_methods   = array();
    $values = array ('country' => 'NL',
                     'amount'  => 100);


    // Fake product number to get a filled card....
    $woocommerce->cart->add_to_cart('1');

    WC()->shipping->calculate_shipping(get_shipping_packages($values));
    $shipping_methods = WC()->shipping->packages;

    foreach ($shipping_methods[0]['rates'] as $id => $shipping_method) {
        $active_methods[] = array(  'id'        => $shipping_method->method_id,
                                    'type'      => $shipping_method->method_id,
                                    'provider'  => $shipping_method->method_id,
                                    'name'      => $shipping_method->label,
                                    'price'     => number_format($shipping_method->cost, 2, '.', ''));
    }
    do_action( 'qm/debug', $active_methods );
    return $active_methods;
}


function get_shipping_packages($value) {

    // Packages array for storing 'carts'
    $packages = array();
    $packages[0]['contents']                = WC()->cart->cart_contents;
    $packages[0]['contents_cost']           = $value['amount'];
    $packages[0]['applied_coupons']         = WC()->session->applied_coupon;
    $packages[0]['destination']['country']  = $value['countries'];
    $packages[0]['destination']['state']    = '';
    $packages[0]['destination']['postcode'] = '';
    $packages[0]['destination']['city']     = '';
    $packages[0]['destination']['address']  = '';
    $packages[0]['destination']['address_2']= '';


    return apply_filters('woocommerce_cart_shipping_packages', $packages);
}

//function your_shipping_method_init() {
// Your class will go here
//}

/* ********* IMPORTANT **********
*
* to ensure it runs after the other plugins.....!!!!!
*/
//add_action( 'woocommerce_shipping_init', 'your_shipping_method_init' );


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

    $msg = "this is the callback";

    wp_send_json( $msg );
}

/**
* hook to add the ajax callback
*/
add_action( 'wp_ajax_get_shipping_methods', 'get_shipping_methods' );
?>