<?php
/*
Plugin Name: ComproPago
Plugin URI: http://demo.compropago.com/plugins/woocommerce
Description: Con ComproPago puede recibir pagos vía SPEI y en efectivo.
Version: 5.0.0.3
Author: <a href="https://compropago.com" target="_blank">ComproPago</a>
Licence: GPL-3.0-or-later
*/

/**
 * @author Eduardo Aguilar <eduardo.aguilar@compropago.com>
 * @author Alfredo Gómez <alfredo@compropago.com>
 * @author José Beltrán <jose.beltran@compropago.com>
 */

require_once __DIR__ . "/vendor/autoload.php";

require_once __DIR__ . '/controllers/ConfigController.php';

include_once __DIR__ . '/includes/compropago-functions.php';
include_once __DIR__ . '/includes/config-functions.php';
include_once __DIR__ . '/includes/init-functions.php';
include_once __DIR__ . '/includes/gateways/receipt.php';
include_once __DIR__ . '/includes/gateways/compropago-webhook.php';

/**
 * Activación de la nueva página de configuración
 */
add_action( 'admin_menu', 'cp_add_admin_page' );

/**
 * Registro de rutina de activación
 */
register_activation_hook( __FILE__, function() {
    return;
});

/**
 * Main function registration for payment gateways
 */
add_action('plugins_loaded', 'compropago_init', 0);
