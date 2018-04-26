<?php
/*
Plugin Name: ComproPago
Plugin URI: https://www.compropago.com/documentacion/plugins
Description: Con ComproPago puedes recibir pagos en OXXO, 7Eleven y muchas tiendas más en México.
Version: 4.4.0.0
Author: <a href="https://compropago.com" target="_blank">ComproPago</a>
Licence: Apache-2
*/
/**
 * @author Eduardo Aguilar <eduardo.aguilar@compropago.com>
 * @author Alfredo Gómez <alfredo@compropago.com>
 */

require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/controllers/Utils.php";
require_once __DIR__ . '/controllers/ConfigController.php';

include_once __DIR__ . '/includes/compropago-functions.php';
include_once __DIR__ . '/includes/config-functions.php';
include_once __DIR__ . '/includes/init-functions.php';
include_once __DIR__ . '/includes/gateways/receipt.php';
include_once __DIR__ . '/includes/gateways/compropago-webhook.php';

/**
 * Activacion de la nueva pagina de configuracion
 */
add_action( 'admin_menu', 'cp_add_admin_page' );

/**
 * Registro de rutina de activacion
 */
register_activation_hook( __FILE__, function() {
    return ;
});


/**
 * Main function registration for payment gateways
 */
add_action('plugins_loaded', 'compropago_init', 0);
