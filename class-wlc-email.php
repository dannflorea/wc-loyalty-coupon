<?php
defined( 'ABSPATH' ) || exit;

class WLC_Cart_Notice {

    public static function init() {
        add_action( 'woocommerce_before_cart', [ __CLASS__, 'show_notice' ] );
        add_action( 'woocommerce_before_checkout_form', [ __CLASS__, 'show_notice' ] );
        add_filter( 'woocommerce_update_order_review_fragments', [ __CLASS__, 'refresh_fragment' ] ); 
        add_filter( 'woocommerce_add_to_cart_fragments', [ __CLASS__, 'refresh_fragment' ] ); 
    }

    public static function show_notice() {
        if ( 'yes' !== WLC_Settings::get( 'wlc_enabled', 'yes' ) ) {
            return;
        }

        $threshold = (float) WLC_Settings::get( 'wlc_threshold', 500 );
        //$subtotal  = (float) WC()->cart->get_subtotal();
        $subtotal = (float) (WC()->cart->get_subtotal() + WC()->cart->get_subtotal_tax());
        $remaining = $threshold - $subtotal;

        if ( $remaining > 0 ) {
            // Sub prag
            $message = WLC_Settings::get(
                'wlc_cart_message',
                'Mai ai {amount} lei pentru a primi un cupon de fidelitate!'
            );
            $message = str_replace(
                [ '{amount}', '{threshold}' ],
                [ number_format( $remaining, 2, ',', '.' ), number_format( $threshold, 0, ',', '.' ) ],
                $message
            );

            // Progress bar
            $percent = min( 100, round( ( $subtotal / $threshold ) * 100 ) );
            //echo '<div class="wlc-notice-wrapper">';
            echo '<div class="wlc-notice-wrapper wlc-notice wlc-notice--progress">';
            echo '<p>' . wp_kses_post( $message ) . '</p>';
            echo '<div class="wlc-progress-bar" role="progressbar" aria-valuenow="' . esc_attr( $percent ) . '" aria-valuemin="0" aria-valuemax="100">';
            echo '<div class="wlc-progress-bar__fill" style="width:' . esc_attr( $percent ) . '%"></div>';
            echo '</div>';
            echo '</div>';
        } else {
            // Prag atins
            $message = WLC_Settings::get(
                'wlc_cart_message_reached',
                '🎉 Felicitări! Vei primi un cupon de fidelitate după finalizarea comenzii.'
            );
            echo '<div class="wlc-notice wlc-notice--success">';
            echo '<p>' . wp_kses_post( $message ) . '</p>';
            echo '</div>';
            
        }

        // Inline CSS minim — nu necesită fișier separat
        static $css_printed = false;
        if ( ! $css_printed ) {
            $css_printed = true;
            echo '<style>
.wlc-notice{padding:12px 16px;margin-bottom:16px;border-radius:4px;border-left:4px solid #e0a800;background:#fffbea;font-size:.95em}
.wlc-notice--success{border-left-color:#28a745;background:#eafaf1}
.wlc-progress-bar{height:8px;background:#e0e0e0;border-radius:4px;overflow:hidden;margin-top:8px}
.wlc-progress-bar__fill{height:100%;background:#e0a800;transition:width .4s ease;border-radius:4px}
</style>';
        }
    }
    public static function refresh_fragment( $fragments ) {
    ob_start();
    self::show_notice();
    $html = ob_get_clean();

    $fragments['div.wlc-notice-wrapper'] = '<div class="wlc-notice-wrapper">' . $html . '</div>';
    return $fragments;
}
}
