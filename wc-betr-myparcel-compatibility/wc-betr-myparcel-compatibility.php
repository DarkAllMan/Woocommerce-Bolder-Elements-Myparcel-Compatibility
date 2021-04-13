<?php
/**
 * Plugin Name: Woocommerce Bolder Elements - Myparcel Compatibility 
 * Plugin URI: https://www.randall.nl
 * Description: This plugin add's an extra column to the Bolder Elements Table Rates Plugin where you can define MyParcel shipment types: Digital Stamp, Mailbox, Package etc
 * Version: 1.1
 * Author: Randall Kam
 * Author URI: https://www.randall.nl
 * Text Domain: wpo_wcbetrmp
 * Domain Path: /languages
 */

namespace DAM\WC\MyParcel

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( 'BETRS_Compatibility' ) ) :

class BETRS_Compatibility {

	public $version = '1.1';

	protected static $_instance = null;


	/**
	 * Main Plugin Instance
	 *
	 * Ensures only one instance of plugin is loaded or can be loaded.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->define( 'WPO_WC_BETR_MYPARCEL_VERSION', $this->version );
		$this->define( 'WPO_WC_BETR_MYPARCEL_BASENAME', plugin_basename( __FILE__ ) );
		
		add_action( 'init', array( $this, 'load_classes' ) );
	}

	/**
	 * Define constant if not already set
	 * @param  string $name
	 * @param  string|bool $value
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}



	/**
	 * Load the main plugin classes and functions
	 */
	public function includes() {
		// include compatibility classes
		include_once( 'includes/myparcel-compatibility.php' );
		include_once( 'includes/betrs-compatibility.php' );
	}


	/**
	 * Instantiate classes when woocommerce is activated
	 */
	public function load_classes() {
		if ( $this->get_myparcel() === false ) {
			return;
		}
		
		if ( $this->get_betrs() === false ) {
			return;
		}		

		// all systems ready - GO!
		$this->includes();
	}

	
	/**
	  * Check MyParcel Exists
	 */
	 
	public function get_myparcel() {
		// early detection (before plugin class is instantiated)
		if ( ! function_exists( 'WCMYPA' ) ) { // 4.0.0+
			add_action( 'admin_notices', array ( $this, 'need_myparcel' ) );
			return false;
		}
		return true;
	 }
	 
	/**
	  * Check BETRS Exists
	 */
	 
	public function get_betrs() {
		// early detection (before plugin class is instantiated)
		if( ! class_exists('BE_Table_Rate_WC') ){
			add_action( 'admin_notices', array ( $this, 'need_betrs' ) );
			return false;
		}
		return true;		
	 }	 
	
	/**
	 * Myparcel not active notice.
	 */
	 
	public function need_myparcel() {
		$error = sprintf( __( 'Woocommerce Bolder Elements - Myparcel Compatibility requires %sMyParcel%s to be installed & activated!' , 'wpo_wcbetrmp' ), '<a href="http://wordpress.org/extend/plugins/woocommerce-myparcel/">', '</a>' );

		$message = '<div class="error"><p>' . $error . '</p></div>';
	
		echo $message;
	}
	
	/**
	 * Bolder Elements Table Rates not active notice.
	 */
	 
	public function need_betrs() {
		$error = sprintf( __( 'Woocommerce Bolder Elements - Myparcel Compatibility requires %sBolder Elements Table Rates%s to be installed & activated!' , 'wpo_wcbetrmp' ), '<a href="https://codecanyon.net/item/table-rate-shipping-for-woocommerce/3796656/">', '</a>' );

		$message = '<div class="error"><p>' . $error . '</p></div>';
	
		echo $message;
	}	

} // class WPO_WC_BETR_MYPARCEL

endif; // class_exists

/**
 * Returns the main instance of WC to prevent the need to use globals.
 *
 * @since  2.1
 * @return WooCommerce
 */
function DAM_BETRS_Compatibility() {
	return BETRS_Compatibility::instance();
}

DAM_BETRS_Compatibility(); // load plugin
