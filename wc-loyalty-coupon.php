<?php
/**
 * Plugin Name: WC Loyalty Coupon
 * Plugin URI:  https://maxdev.ro
 * Description: Generează automat un cupon de fidelitate când o comandă atinge pragul configurat.
 * Version:     1.0.0
 * Author:      MaxDev
 * Text Domain: wc-loyalty-coupon
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * WC requires at least: 6.0
 */

defined( 'ABSPATH' ) || exit;

define( 'WLC_VERSION', '1.0.0' );
define( 'WLC_PATH', plugin_dir_path( __FILE__ ) );
define( 'WLC_URL',  plugin_dir_url( __FILE__ ) );

require_once WLC_PATH . 'includes/class-wlc-settings.php';
require_once WLC_PATH . 'includes/class-wlc-coupon-generator.php';
require_once WLC_PATH . 'includes/class-wlc-cart-notice.php';
require_once WLC_PATH . 'includes/class-wlc-email.php';

add_action( 'plugins_loaded', function () {
    if ( ! class_exists( 'WooCommerce' ) ) {
        add_action( 'admin_notices', function () {
            echo '<div class="notice notice-error"><p><strong>WC Loyalty Coupon</strong> necesită WooCommerce activ.</p></div>';
        } );
        return;
    }
    WLC_Settings::init();
    WLC_Coupon_Generator::init();
    WLC_Cart_Notice::init();
    WLC_Email::init();
} );
