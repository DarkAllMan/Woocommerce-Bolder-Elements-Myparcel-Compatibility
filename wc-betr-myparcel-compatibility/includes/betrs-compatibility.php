<?php

/**
 * Main plugin functions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( 'DAM_BC_BETRS' ) ) :

class DAM_BC_BETRS {
	
	function __construct()	{
		// add column to the table of rates
		add_filter( 'betrs_shipping_table_columns', array ( $this, 'betrs_add_package_type' ), 10, 1 );
		// add form field to custom column in the table of rates
		add_filter( 'betrs_shipping_table_column_packagetype', array ( $this, 'betrs_add_package_type_data' ), 10, 2 );
		// save form field from custom column in the table of rates
		add_filter( 'betrs_shipping_table_save_row', array ( $this, 'betrs_save_package_type_data' ), 10, 3 );
		// find the custom field value when calculating shipping
		add_filter( 'betrs_processed_rate', array ( $this, 'betrs_add_custom_data_package_type_array' ), 10, 4 );
		// add the custom field to the meta data
		add_filter( 'betrs_shipping_rate_meta', array ( $this, 'betrs_modify_package_type_rate_meta' ), 10, 2 );
	}

	/**
	 * Add a column to the Table of Rates
	 *
	 * @return array
	 */
	public function betrs_add_package_type( $columns ) {

		// temporarily unset the last column (sort)
		unset( $columns['sort'] );

		// setup new column
		$columns['packagetype'] = __( 'MyParcel Package Type', 'be-table-ship' );

		// add sort column back
		$columns['sort'] = '';

		return $columns;
	}

	/**
	 * Add form field to custom column in the Table of Rates
	 *
	 * @return string
	 */
	public function betrs_add_package_type_data( $content, $item ) {
		// find previously saved value
		$saved = ( isset( $item['package_type'] ) ) ? sanitize_text_field( $item['package_type'] ) : '';
		
		// get allowed shipment types from MyParcel
		$package_types = array_combine(WCMP_Data::getPackageTypes(), WCMP_Data::getPackageTypesHuman());

		// setup form field
		$content = '<select name="package_type[' . $item['option_ID'] . '][' . $item['row_ID'] . ']">';
		$content .= '<option value="">-- SELECT MYPARCEL PACKAGE TYPE --</option>';
		foreach( $package_types as $value => $name ) {
			$content .= '<option ' . selected( $value, $saved, false ) . ' value="' . $value . '">' . $name . '</option>';
		}

		$content .= '</select>';

		return $content;
	}

	/**
	 * Save form field from custom column in the Table of Rates
	 *
	 * @return string
	 */
	function betrs_save_package_type_data( $save_data, $option_ID, $row_ID ) {
		// sanitize arguments
		$option_ID = intval( $option_ID );
		$row_ID = intval( $row_ID );

		// get selected value
		$selection = ( isset( $_POST['package_type'][ $option_ID ][ $row_ID ] ) ) ? sanitize_text_field( $_POST['package_type'][ $option_ID ][ $row_ID ] ) : '';

		// add value to saved data array
		$save_data['package_type'] = $selection;

		return $save_data;
	}
	
	/**
	 * Find the custom column selection when calculating shipping
	 *
	 * @return string
	 */
	function betrs_add_custom_data_package_type_array( $shipping_options, $option, $option_ID, $row_ID ) {

		// find selected value from table
		$selection = ( isset( $option['rows'][ $row_ID ]['package_type'] ) ) ? sanitize_text_field( $option['rows'][ $row_ID ]['package_type'] ) : '';

		// add to shipping option
		if( $selection != '' ) {
			$shipping_options['package_type'] = $selection;
		}

		return $shipping_options;
	}

	/**
	 * Add provider to meta data if selected.
	 *
	 * @return void
	 */
	function betrs_modify_package_type_rate_meta( $meta_data, $rate ) {
		// check if the provider has been selected
		if( ! empty( $rate['package_type'] ) ) {
			$meta_data['package_type'] = sanitize_text_field( $rate['package_type'] );
		}

		return $meta_data;
	}

}

endif; // class_exists

return new DAM_BC_BETRS();