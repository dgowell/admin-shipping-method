<?php
/**
* Plugin Name: Admin Shipping Methods
* Description: Adds table rate shipping methods to admin add order page.
* Version: 0.1
* Author: TapaCode
* Author URI: http://www.tapacode.com
*/

// To prevent direct access data leaks
if ( ! defined( 'ABSPATH' ) ) {
exit; // Exit if accessed directly
}

if (!function_exists('write_log')) {
    function write_log($log) {
        if (true === WP_DEBUG) {
            if (is_array($log) || is_object($log)) {
                error_log(print_r($log, true));
            } else {
                error_log($log);
            }
        }
    }
}

// Test to see if WooCommerce is active (including network activated).
$plugin_path = trailingslashit( WP_PLUGIN_DIR ) . 'woocommerce/woocommerce.php';

if (
    in_array( $plugin_path, wp_get_active_and_valid_plugins() )
    || in_array( $plugin_path, wp_get_active_network_plugins() )
) {
    // Custom code here. WooCommerce is active, however it has not
    // necessarily initialized (when that is important, consider
    // using the `woocommerce_init` action).

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
    function add_order_shipping() {
        //check_ajax_referer( 'order-item', 'security' );

        if ( ! current_user_can( 'edit_shop_orders' ) ) {
            wp_die( -1 );
        }
        global $woocommerce;

        $response = array();

        try {

            // set the data from the ajax call
            $order_id = isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : 0;
            $order = wc_get_order( $order_id );
            if ( ! $order ) {
                throw new Exception( __( 'Invalid order', 'woocommerce' ) );
            }

            $active_methods = array();
            $values = array (
                'address' => $order->get_shipping_address_1(),
                'address_2' => $order->get_shipping_address_2(),
                'city' => $order->get_shipping_city(),
                'state' => $order->get_shipping_state(),
                'postcode' => $order->get_shipping_postcode(),
                'country' => $order->get_shipping_country(),
                'total' => $order->get_total(),
            );

            //ensure the cart is empty before adiding anything
            $woocommerce->cart->empty_cart();

            foreach ( $order->get_items() as $item_id => $item ) {
                //add each item in order to cart
                $woocommerce->cart->add_to_cart($item->get_product_id());
            }

            WC()->shipping->calculate_shipping(get_shipping_packages($values));
            $shipping_methods = WC()->shipping->packages;

            foreach ($shipping_methods[0]['rates'] as $id => $shipping_method) {
                $active_methods[] = array( 'id' => $shipping_method->method_id,
                'type' => $shipping_method->method_id,
                'provider' => $shipping_method->method_id,
                'name' => $shipping_method->label,
                'price' => number_format($shipping_method->cost, 2, '.', ''));
            }

            $item = new WC_Order_Item_Shipping();
            $item->set_shipping_rate( new WC_Shipping_Rate(
                $active_methods[0]['id'],
                $active_methods[0]['name'],
                $active_methods[0]['price'],
                $active_methods[0]['id']
                ));
            $item->set_order_id( $order_id );
            $item_id = $item->save();

       } catch ( Exception $e ) {
            wp_send_json_error( array( 'error' => $e->getMessage() ) );
        }
        wp_send_json_success( $item );
    }

    /*
    * Packages array for storing 'carts'
    */
    function get_shipping_packages($value) {
        $packages = array();
        $packages[0]['contents'] = WC()->cart->cart_contents;
        $packages[0]['contents_cost'] = $value['total'];
        $packages[0]['applied_coupons'] = WC()->session->applied_coupon;
        $packages[0]['destination']['country'] = $value['country'];
        $packages[0]['destination']['state'] = $value['state'];
        $packages[0]['destination']['postcode'] = $value['postcode'];
        $packages[0]['destination']['city'] = $value['city'];
        $packages[0]['destination']['address'] = $value['address'];
        $packages[0]['destination']['address_2']= $value['address_2'];
        return apply_filters('woocommerce_cart_shipping_packages', $packages);
    }
    /**
    * hook to add the ajax callback
    */
    add_action( 'wp_ajax_add_order_shipping', 'add_order_shipping' );
}
?>