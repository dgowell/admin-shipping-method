<?php

/**
* @function Get All WooCommerce Shipping Rates
* @how-to Get CustomizeWoo.com FREE
* @author Rodolfo Melogli
* @compatible WooCommerce 5
* @donate $9 https://businessbloomer.com/bloomer-armada/
*/


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

        // calculate the shipping cost from the orders details
        // add the cost as a line item to the order
        // save the order

        $response = array();

        try {

            // set the data from the ajax call
            $order_id = isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : 0;
            $order = wc_get_order( $order_id );
            if ( ! $order ) {
                throw new Exception( __( 'Invalid order', 'woocommerce' ) );
            }

            $order_taxes = $order->get_taxes();
            $shipping_methods = WC()->shipping() ? WC()->shipping()->load_shipping_methods() : array();

            // Add new shipping.
            $item = new WC_Order_Item_Shipping();
            //$all_rates = bbloomer_get_all_shipping_rates();

            // Fake product number to get a filled card....
            /*foreach ( $order->get_items() as $item_id => $item ) {
                //for erach item in order add to cart
                $woocommerce->cart->add_to_cart('1');
            }
            */

            $item->set_shipping_rate( new WC_Shipping_Rate() );
            $item->set_order_id( $order_id );
            $item_id = $item->save();

            ob_start();
            include __DIR__ . '/views/html-order-shipping.php';
            $response['html'] = ob_get_clean();
       } catch ( Exception $e ) {
            wp_send_json_error( array( 'error' => $e->getMessage() ) );
        }
        wp_send_json_success( $response);
    }

    /**
    * hook to add the ajax callback
    */
    add_action( 'wp_ajax_add_order_shipping', 'add_order_shipping' );
}

    /**
    * @function Get WooCommerce Shipping Zones
    * @how-to Get CustomizeWoo.com FREE
    * @author Rodolfo Melogli
    * @compatible WooCommerce 5
    * @donate $9 https://businessbloomer.com/bloomer-armada/
    */

    function bbloomer_get_all_shipping_zones() {
        $data_store = WC_Data_Store::load( 'shipping-zone' );
        $raw_zones = $data_store->get_zones();
        foreach ( $raw_zones as $raw_zone ) {
            $zones[] = new WC_Shipping_Zone( $raw_zone );
        }
        $zones[] = new WC_Shipping_Zone( 0 ); // ADD ZONE "0" MANUALLY
        return $zones;
    }

    /**
    * @function Get All WooCommerce Shipping Rates
    * @how-to Get CustomizeWoo.com FREE
    * @author Rodolfo Melogli
    * @compatible WooCommerce 5
    * @donate $9 https://businessbloomer.com/bloomer-armada/
    */

    function bbloomer_get_all_shipping_rates() {
        foreach ( bbloomer_get_all_shipping_zones() as $zone ) {
            $zone_shipping_methods = $zone->get_shipping_methods();
            foreach ( $zone_shipping_methods as $index => $method ) {
                $method_is_taxable = $method->is_taxable();
                $method_is_enabled = $method->is_enabled();
                $method_instance_id = $method->get_instance_id();
                $method_title = $method->get_method_title(); // e.g. "Flat Rate"
                $method_description = $method->get_method_description();
                $method_user_title = $method->get_title(); // e.g. whatever you renamed "Flat Rate" into
                $method_rate_id = $method->get_rate_id(); // e.g. "flat_rate:18"
            }
        //print_r( $zone_shipping_methods );
        return $zone_shipping_methods;
    }
}
?>