<?php
/*
 * Plugin Name: WooCommerce Confirm Customer Email
 * Plugin URI: http://wordpress.org/plugins/woocommerce-confirm-customer-email/
 * Author: Excellent Webworld
 * Description: Verify customer email address before order in woocommerce.
 * Version: 1.0.0
 * Author URI: https://excellentwebworld.com/
 * Requires at least: 4.0
 * Tested up to: 5.0
 * License: GPL v3
 */

if ( ! defined( 'ABSPATH' ) ) exit;

require_once( 'inc/class-woocommerce-confirm-customer-email.php' );

global $woocce_confirm_email;
$woocce_confirm_email = new WooCommerce_Confirm_Customer_Email( __FILE__ );
