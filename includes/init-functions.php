<?php
/**
 * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
 */

function compropago_init() {
    if ( !class_exists( 'WC_Payment_Gateway' ) ) return;

    load_plugin_textdomain('compropago', false, dirname( plugin_basename( __FILE__ ) ) . '/languages');

    /* ========= Gateways includes ========= */
    include_once __DIR__ . '/gateways/wc-gateway-compropago-cash.php';
    include_once __DIR__ . '/gateways/wc-gateway-compropago-spei.php';
}