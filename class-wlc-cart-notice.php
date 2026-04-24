<?php
defined( 'ABSPATH' ) || exit;

class WLC_Settings {

    const OPTION_KEY = 'wlc_settings';

    public static function init() {
        add_filter( 'woocommerce_get_sections_advanced', [ __CLASS__, 'add_section' ] );
        add_filter( 'woocommerce_get_settings_advanced',  [ __CLASS__, 'add_settings' ], 10, 2 );
    }

    public static function add_section( $sections ) {
        $sections['wlc_loyalty'] = __( 'Cupon Fidelitate', 'wc-loyalty-coupon' );
        return $sections;
    }

    public static function add_settings( $settings, $current_section ) {
        if ( 'wlc_loyalty' !== $current_section ) {
            return $settings;
        }

        return [
            [
                'title' => __( 'Setări Cupon Fidelitate', 'wc-loyalty-coupon' ),
                'type'  => 'title',
                'id'    => 'wlc_section_start',
            ],
            [
                'title'             => __( 'Activat', 'wc-loyalty-coupon' ),
                'desc'              => __( 'Activează generarea automată de cupoane', 'wc-loyalty-coupon' ),
                'id'                => 'wlc_enabled',
                'type'              => 'checkbox',
                'default'           => 'yes',
            ],
            [
                'title'             => __( 'Prag comandă (lei)', 'wc-loyalty-coupon' ),
                'desc'              => __( 'Valoarea minimă a coșului pentru a primi cupon. Ex: 500', 'wc-loyalty-coupon' ),
                'id'                => 'wlc_threshold',
                'type'              => 'number',
                'default'           => '500',
                'custom_attributes' => [ 'min' => '0', 'step' => '1' ],
                'css'               => 'width:100px',
            ],
            [
                'title'   => __( 'Tip reducere cupon', 'wc-loyalty-coupon' ),
                'id'      => 'wlc_coupon_type',
                'type'    => 'select',
                'default' => 'percent',
                'options' => [
                    'percent'      => __( 'Procent (%)', 'wc-loyalty-coupon' ),
                    'fixed_cart'   => __( 'Sumă fixă (lei)', 'wc-loyalty-coupon' ),
                    'free_shipping'=> __( 'Transport gratuit', 'wc-loyalty-coupon' ),
                ],
            ],
            [
                'title'             => __( 'Valoare reducere', 'wc-loyalty-coupon' ),
                'desc'              => __( 'Valoarea reducerii (procent sau sumă fixă). Ignorat pentru transport gratuit.', 'wc-loyalty-coupon' ),
                'id'                => 'wlc_coupon_amount',
                'type'              => 'number',
                'default'           => '10',
                'custom_attributes' => [ 'min' => '0', 'step' => '0.01' ],
                'css'               => 'width:100px',
            ],
            [
                'title'             => __( 'Expirare cupon (zile)', 'wc-loyalty-coupon' ),
                'desc'              => __( 'Număr de zile până la expirare. 0 = fără expirare.', 'wc-loyalty-coupon' ),
                'id'                => 'wlc_coupon_expiry_days',
                'type'              => 'number',
                'default'           => '30',
                'custom_attributes' => [ 'min' => '0', 'step' => '1' ],
                'css'               => 'width:100px',
            ],
            [
                'title'   => __( 'Mesaj coș (sub prag)', 'wc-loyalty-coupon' ),
                'desc'    => __( 'Folosește {amount} pentru suma rămasă și {threshold} pentru prag. Ex: "Mai ai {amount} lei pentru un cupon de fidelitate!"', 'wc-loyalty-coupon' ),
                'id'      => 'wlc_cart_message',
                'type'    => 'textarea',
                'default' => 'Mai ai {amount} lei pentru a primi un cupon de fidelitate!',
                'css'     => 'width:100%;min-height:60px',
            ],
            [
                'title'   => __( 'Mesaj coș (prag atins)', 'wc-loyalty-coupon' ),
                'desc'    => __( 'Mesaj afișat când clientul a atins pragul.', 'wc-loyalty-coupon' ),
                'id'      => 'wlc_cart_message_reached',
                'type'    => 'textarea',
                'default' => '🎉 Felicitări! Vei primi un cupon de fidelitate după finalizarea comenzii.',
                'css'     => 'width:100%;min-height:60px',
            ],
            [
                'type' => 'sectionend',
                'id'   => 'wlc_section_end',
            ],
        ];
    }

    /**
     * Helper: returnează o setare.
     */
    public static function get( $key, $default = '' ) {
        return get_option( $key, $default );
    }
}
