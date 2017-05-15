<?php
/**
 * @package FitPress
 */
/*
Plugin Name: FitPress Mandrill
Plugin URI: http://fitpress.co.za
Description: Integrates FitPress to send mails via Mandrill.
Version: 1.0
Author: Leaps + Bounds
Author URI: https://leapsandbounds.io
License: GPLv2 or later
Text Domain: fitpress-payfast
*/

if ( ! defined( 'ABSPATH' ) ) :
	exit; // Exit if accessed directly
endif;

function is_fitpress_active_for_mandrill(){

	/**
	 * Check if WooCommerce is active, and if it isn't, disable Subscriptions.
	 *
	 * @since 1.0
	 */
	if ( !is_plugin_active( 'fitpress/fitpress.php' ) ) {
		add_action( 'admin_notices', 'FP_Mandrill::fitpress_inactive_notice' );
		deactivate_plugins( plugin_basename( __FILE__ ) );
	}

}

add_action( 'admin_init', 'is_fitpress_active_for_mandrill' );

class FP_Mandrill {
	
	/**
	 * @var FitPress The single instance of the class
	 * @since 1.0
	 */
	protected static $_instance = null;

	protected $mandrill_username;
	protected $mandrill_api_key;

	/**
	 * Main FitPress Instance
	 *
	 * Ensures only one instance of FitPress is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 * @see WC()
	 * @return FitPress - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {

		$this->mandrill_username = 'Flicker Leap';
		$this->mandrill_api_key = 'BkEZ2yJepLkakAIEz9cB-Q';
		// $this->mandrill_api_key = 'ZcqqrTPutE26yd6fgjyKUA'; // Test key

		add_action( 'phpmailer_init', array( $this, 'use_mandrill' ) );

	}

	public function use_mandrill( $phpmailer ) {

		$phpmailer->isSMTP();
		$phpmailer->SMTPAuth = true;
		$phpmailer->SMTPSecure = "tls";
		 
		$phpmailer->Host = "smtp.mandrillapp.com";
		$phpmailer->Port = "587";

		$phpmailer->Username = $this->mandrill_username;
		$phpmailer->Password = $this->mandrill_api_key;

		$phpmailer->AddCustomHeader( sprintf( '%1$s: %2$s', 'X-MC-ReturnPathDomain', 'track.fitpress.co.za' ) );
		$phpmailer->AddCustomHeader( sprintf( '%1$s: %2$s', 'X-MC-Subaccount', 'FitPress' ) );

	}

}



/**
 * Extension main function
 */
function __fp_mandrill_main() {
	FP_Mandrill::instance();
}

// Initialize plugin when plugins are loaded
add_action( 'plugins_loaded', '__fp_mandrill_main' );
