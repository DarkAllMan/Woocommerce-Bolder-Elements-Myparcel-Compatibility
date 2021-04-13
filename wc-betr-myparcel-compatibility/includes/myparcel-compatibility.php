<?php
/**
 * Main plugin functions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( 'DAM_BC_MYPARCEL' ) ) :

class DAM_BC_MYPARCEL {
	
	function __construct()	{
		// USE MYPARCEL PACKAGE TYPE SET IN BETRS AS PACKAGE TYPE IN SAVED ORDER
		add_filter( 'wc_myparcel_order_delivery_options', array ( $this, 'set_Myparcel_Delivery_Options' ), 10, 2 );		
		// SHOW MYPARCEL CHECKOUT OPTIONS ONY ON PACKAGE TYPE
		add_filter( 'wc_myparcel_show_delivery_options', array ( $this, 'show_myparcel_checkout_options' ), 10, 1 );
		
		// DISPLAY CUSTOM FIELD FOR DEBUGGING
		add_action( 'woocommerce_review_order_before_submit', array ( $this, 'betrs_display_package_type' ), 10 );		
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
			foreach ( $order->get_items( 'shipping' ) as $item_id => $shipping_method ){
				$package_type = $shipping_method->get_meta('package_type');
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
		$data = [
			'package_type' => $this->getCurrentPackageTypeFromOrder($order)
		];
		$deliveryOptions = new WCMP_DeliveryOptionsFromOrderAdapter($deliveryOptions, $data);
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
	
	/**
	 * Display custom field for debugging
	 *
	 * @return void
	 */
	function betrs_display_package_type() {
		
		$package_type = $this->getCurrentPackageTypeFromCart();
		
		if( current_user_can('administrator') ) {
			echo strtolower($package_type);
		}
	}	

}

endif; // class_exists

return new DAM_BC_MYPARCEL();
