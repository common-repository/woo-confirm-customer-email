<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class WooCommerce_Confirm_Customer_Email {
	private $woocce_dir;
	private $woocce_file;
	private $woocce_assets_dir;
	private $woocce_assets_url;

	public function __construct( $woocce_file ) {
		$this->woocce_dir = dirname( $woocce_file );
		$this->woocce_file = $woocce_file;
		$this->woocce_assets_dir = trailingslashit( $this->woocce_dir ) . 'assets';
		$this->woocce_assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $woocce_file ) ) );

		add_filter( 'woocommerce_billing_fields', array( $this, 'woocce_add_billing_field' ) );

		add_filter( 'default_checkout_woocce_customer_confirm_email', array( $this, 'woocce_default_field_value' ), 10, 2 );

		add_filter( 'woocommerce_process_checkout_field_woocce_customer_confirm_email', array( $this, 'woocce_validate_email_address' ) );

	}

	public function woocce_add_billing_field( $woocce_fields = array() ) {

		$woocce_return_fields = array();

		foreach( $woocce_fields as $woocce_field_key => $woocce_field_data ) {

			$woocce_return_fields[ $woocce_field_key ] = $woocce_field_data;

			if( 'billing_email' == $woocce_field_key ) {

				$woocce_return_fields['billing_email']['class'] = apply_filters( 'woocommerce_original_email_field_class', array( 'form-row-first' ) );

				$woocce_return_fields['woocce_customer_confirm_email'] = array(
					'label' 			=> __( 'Confirm Email Address', 'woocommerce-confirm-customer-email' ),
					'placeholder' 			=> _x( 'Email Address', 'placeholder', 'woocommerce-confirm-customer-email' ),
					'required' 			=> true,
					'class' 			=> apply_filters( 'woocommerce_confirm_email_field_class', array( 'form-row-wide' ) ),
					'clear'				=> true,
					'validate'			=> array( 'email' ),
					
					// add priority value to second email address field (for WC 3.5+) 
					'priority'			=> isset($woocce_return_fields['billing_email']['priority']) ? $woocce_return_fields['billing_email']['priority'] + 1 : 0,
				);
			}

		}

		if( apply_filters( 'woocommerce_confirm_email_wide_phone_field', true ) ) {
			$woocce_return_fields['billing_phone']['class'] = array( 'form-row-wide' );
		}

		return $woocce_return_fields;

	}

	public function woocce_default_field_value( $woocce_value = null, $woocce_field = 'woocce_customer_confirm_email' ) {
		if ( is_user_logged_in() ) {
			global $current_user;
			$woocce_value = $current_user->user_email;
		}
		return $woocce_value;
	}

	public function woocce_validate_email_address( $woocce_confirm_email = '' ) {
		global $woocommerce;

		// Use new checkout object for WC 3.0+
		if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
			$woocce_billing_email = $woocommerce->checkout->posted['billing_email'];
		} else {
			$woocce_checkout = new WC_Checkout;
			$woocce_billing_email = $checkout->get_value('billing_email');	
		}

		if( strtolower( $woocce_confirm_email ) != strtolower( $woocce_billing_email ) ) {

			$woocce_notice = sprintf( __( '%1$sEmail addresses%2$s do not match.' , 'woocommerce-confirm-customer-email' ) , '<strong>' , '</strong>' );

			if ( version_compare( WC_VERSION, '2.3', '<' ) ) {
				$woocommerce->add_error( $woocce_notice );
			} else {
				wc_add_notice( $woocce_notice, 'error' );
			}

		}

		return $woocce_confirm_email;
	}

}
