<?php
use WPO\WC\MyParcel\Compatibility\WC_Core as WCX;

/**
 * Main plugin functions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( 'WPO_WC_MYPARCEL_Compatibility' ) ) :

class WPO_WC_MYPARCEL_Compatibility {
	
	function __construct()	{
		// USE MYPARCEL PACKAGE TYPE SET IN BETRS AS PACKAGE TYPE IN SAVED ORDER
		add_filter( 'wc_myparcel_order_delivery_options', array ( $this, 'set_Myparcel_Delivery_Options' ), 10, 2 );		
		// SHOW MYPARCEL CHECKOUT OPTIONS ONY ON PACKAGE TYPE
		add_filter( 'wc_myparcel_show_delivery_options', array ( $this, 'show_myparcel_checkout_options' ), 10, 1 );
	}
	
	
	/**
	 * CHECKOUT FORM FUNCTION TO RETURN CHECK TYPE .
	 *
	 * @return string
	 */
	public function getCurrentPackageTypeFromCart(){
		// The chosen shipping method (string) - Output the Shipping method rate ID
		$chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' )[0];

		// The array of shipping methods enabled for the current shipping zone:
		$shipping_methods = WC()->session->get('shipping_for_package_0')['rates'];
		
		$method = $shipping_methods[$chosen_shipping_methods];
		$meta_data = $method->get_meta_data();
		$package_type = (array_key_exists('package_type',$meta_data)) ? $meta_data['package_type'] : false;
		return $package_type;
	}

	/**
	 * ORDER FUNCTION TO RETURN CHECK TYPE .
	 *
	 * @return string
	 */
	public function getCurrentPackageTypeFromOrder($order){
		// default package type
		$package_type = 'package';

		$shipping_methods = $order->get_items( 'shipping' );
		foreach ( $shipping_methods as $method_id => $shipping_rate ){
			$meta_data = $shipping_rate->get_meta_data();
			foreach($meta_data AS $data){
				$meta = $data->get_data();
				foreach($meta AS $key => $value){
					if($key=='key' && $value=='package_type'){
						$package_type = $meta['value'];
					}
				}			
			}
		}
		return $package_type;
	}

	/**
	 * Function to manipulate MyParce delivery options
	 *
	 * @return array
	 */
	public function set_Myparcel_Delivery_Options( $deliveryOptions, $order ){	
		$deliveryOptions->package_type = $this->getCurrentPackageTypeFromOrder($order);
		return $deliveryOptions;
	}

	/**
	 * ORDER FUNCTION TO RETURN CHECK TYPE .
	 *
	 * @return bool
	 */
	public function show_myparcel_checkout_options($show){
		if($this->getCurrentPackageTypeFromCart()!='package'){
			$show = false;
		}
		return $show;
	}

}

endif; // class_exists

return new WPO_WC_MYPARCEL_Compatibility();
