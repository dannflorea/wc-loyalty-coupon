<?php
defined( 'ABSPATH' ) || exit;

class WLC_Email {

    public static function init() {
        add_action( 'wlc_send_coupon_email', [ __CLASS__, 'append_coupon_to_order_email' ], 10, 2 );
        add_action( 'woocommerce_email_order_details', [ __CLASS__, 'inject_into_completed_email' ], 5, 4 );
    }

    /**
     * Stochează temporar codul cuponului ca să-l injectăm în emailul "order completed".
     * Hook-ul wlc_send_coupon_email se declanșează înainte de trimiterea emailului WC.
     */
    public static function append_coupon_to_order_email( WC_Order $order, string $coupon_code ) {
        // Stocăm în opțiune temporară legată de order_id
        set_transient( 'wlc_pending_coupon_' . $order->get_id(), $coupon_code, HOUR_IN_SECONDS );

        // Forțăm re-trimiterea emailului "Customer Completed Order"
        $mailer = WC()->mailer();
        $emails = $mailer->get_emails();

        if ( isset( $emails['WC_Email_Customer_Completed_Order'] ) ) {
            $emails['WC_Email_Customer_Completed_Order']->trigger( $order->get_id(), $order );
        }
    }

    /**
     * Injectează blocul cu cuponul în emailul "order completed".
     * Hookul woocommerce_email_order_details se apelează în template-ul WC pentru fiecare email.
     *
     * @param WC_Order $order
     * @param bool     $sent_to_admin
     * @param bool     $plain_text
     * @param WC_Email $email
     */
    public static function inject_into_completed_email( $order, $sent_to_admin, $plain_text, $email ) {
        // Rulăm doar pentru emailul de "order completed" trimis clientului
        if ( $sent_to_admin ) {
            return;
        }
        if ( ! $email instanceof WC_Email || 'customer_completed_order' !== $email->id ) {
            return;
        }

        $order_id    = $order->get_id();
        $coupon_code = get_transient( 'wlc_pending_coupon_' . $order_id );

        if ( ! $coupon_code ) {
            // Încearcă din post meta (dacă email-ul se retrimite manual)
            $coupon_code = get_post_meta( $order_id, '_wlc_coupon_code', true );
        }

        if ( ! $coupon_code ) {
            return;
        }

        // Obținem detalii cupon pentru afișare
        $wc_coupon   = new WC_Coupon( $coupon_code );
        $type        = $wc_coupon->get_discount_type();
        $amount      = $wc_coupon->get_amount();
        $expiry      = $wc_coupon->get_date_expires();
        $expiry_text = $expiry ? $expiry->date_i18n( get_option( 'date_format' ) ) : __( 'fără expirare', 'wc-loyalty-coupon' );

        // Formatare valoare
        if ( 'percent' === $type ) {
            $value_text = $amount . '%';
        } elseif ( 'free_shipping' === $type ) {
            $value_text = __( 'Transport gratuit', 'wc-loyalty-coupon' );
        } else {
            $value_text = wc_price( $amount );
        }

        if ( $plain_text ) {
            echo "\n\n--- " . __( 'Cuponul tău de fidelitate', 'wc-loyalty-coupon' ) . " ---\n";
            echo __( 'Cod:', 'wc-loyalty-coupon' ) . ' ' . $coupon_code . "\n";
            echo __( 'Reducere:', 'wc-loyalty-coupon' ) . ' ' . strip_tags( $value_text ) . "\n";
            echo __( 'Valabil până la:', 'wc-loyalty-coupon' ) . ' ' . $expiry_text . "\n";
            echo __( 'Valabil o singură dată.', 'wc-loyalty-coupon' ) . "\n";
        } else {
            ?>
            <table cellspacing="0" cellpadding="0" style="width:100%;margin-top:24px;margin-bottom:8px">
                <tr>
                    <td style="background:#fffbea;border:2px dashed #e0a800;border-radius:6px;padding:24px;text-align:center">
                        <p style="margin:0 0 8px;font-size:14px;color:#555;">
                            🎁 <?php esc_html_e( 'Cadou din partea noastră:', 'wc-loyalty-coupon' ); ?>
                        </p>
                        <p style="margin:0 0 12px;font-size:28px;font-weight:700;letter-spacing:4px;color:#333;">
                            <?php echo esc_html( $coupon_code ); ?>
                        </p>
                        <p style="margin:0 0 4px;font-size:13px;color:#777;">
                            <?php
                            printf(
                                esc_html__( 'Reducere: %s · Valabil până la: %s · O singură utilizare', 'wc-loyalty-coupon' ),
                                wp_kses_post( $value_text ),
                                esc_html( $expiry_text )
                            );
                            ?>
                        </p>
                    </td>
                </tr>
            </table>
            <?php
        }

        // Șterge transient-ul după injectare
        delete_transient( 'wlc_pending_coupon_' . $order_id );
    }
}
