<?php
/*
Plugin Name: ComproPago
Plugin URI: https://www.compropago.com/documentacion/plugins
Description: Con ComproPago puedes recibir pagos en OXXO, 7Eleven y muchas tiendas más en todo México.
Version: 4.0.0
Author: ComproPago
Licence: Apache-2
*/
/**
 * Copyright 2015 Compropago.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
/**
 * Compropago plugin-woocommerce
 * @author Eduardo Aguilar <eduardo.aguilar@compropago.com>
 */
require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/controllers/Utils.php";


use CompropagoSdk\Client;
use CompropagoSdk\Extern\TransactTables;


/**
 * Estilos generales
 */
function register_styles(){
  wp_register_style( 'prefix-style', plugins_url('templates/css/foundation.css', __FILE__) );
  wp_enqueue_style( 'prefix-style' );
}

/**
 * Pagina de configuracion Compropago
 */
function compropago_config_page(){
  register_styles();

  wp_register_script( 'config-script', plugins_url('templates/js/config-actions.js', __FILE__) );
  wp_register_script( 'jquery_cp', plugins_url('templates/js/jquery.js', __FILE__) );
  wp_enqueue_script( 'jquery_cp' );
  wp_enqueue_script( 'config-script' );

  $def_webhook_url = plugins_url( 'webhook.php', __FILE__ );

  $aux_live       = get_option('compropago_live');
  $aux_logo       = get_option('compropago_showlogo');
  $aux_webh       = get_option('compropago_webhook');
  $config         = get_option('woocommerce_compropago_settings');
  $publickey      = get_option('compropago_publickey');
  $privatekey     = get_option('compropago_privatekey');
  $live           = !empty($aux_live) ? ($aux_live == 'yes' ? true : false) : false;
  $showlogo       = !empty($aux_logo) ? ($aux_logo == 'yes' ? true : false) : false;
  $webhook        = !empty($aux_webh) ? $aux_webh : $def_webhook_url;
  $descripcion    = get_option('compropago_descripcion');
  $instrucciones  = get_option('compropago_instrucciones');
  $titulo         = get_option('compropago_title');
  $complete_order = get_option('compropago_completed_order');
  $initial_state  = get_option('compropago_initial_state');
  $debug          = get_option('compropago_debug');

  $aux            = get_option('woocommerce_compropago_settings');
  $enabled        = $aux['enabled'] === 'yes' ? true : false;
  $client = new Client($publickey,$privatekey,$live);

  $all_providers = $client->api->listDefaultProviders();
  $provs_config = get_option( 'compropago_provallowed' );
  $flag_OXXO = false;

  if (!empty($provs_config)) {
    $allowed = explode(',', $provs_config);
    $active_providers = array();
    $disabled_providers = array();

    foreach ($all_providers as $provider) {
      $flag = true;

      foreach ($allowed as $value){
        if ($value == $provider->internal_name){
            $active_providers[] = $provider;
            $flag = false;
            break;
        }
      }

      if ($flag) { $disabled_providers[] = $provider; }
    }
  }else{
    $active_providers = $all_providers;
    $disabled_providers = array();
  }

  $retro = Utils::retroalimentacion($publickey,$privatekey,$live,$config);
  $image_load = plugins_url('templates/img/ajax-loader.gif', __FILE__);

  include __DIR__ . "/templates/config-page.php";
}

/**
 * Registro de la pagina de configuracion
 */
function compropago_add_admin_page(){
  add_menu_page(
    'ComproPago Config',
    'ComproPago',
    'manage_options',
    'add-compropago',
    'compropago_config_page',
    plugins_url('templates/img/logo.png', __FILE__) , // custom icon
    110  // position menu
  );
}

/**
 * Activacion de la nueva pagina de configuracion
 */
add_action( 'admin_menu', 'compropago_add_admin_page' );

/**
 * Rutina de instalacion para tabla de transacciones
 *
 * @throws Exception
 */
function compropago_active(){
  global $wpdb;
  $dbprefix=$wpdb->prefix;

  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

  $queries = TransactTables::sqlDropTables($dbprefix);

  foreach ($queries as $drop) {
    dbDelta($drop);
  }

  $queries = TransactTables::sqlCreateTables($dbprefix);

  foreach ($queries as $create) {
    if(!dbDelta($create))
      throw new Exception('Unable to Create ComproPago Tables, module cant be installed');
  }
}

/**
 * Registro de rutina de activacion
 */
register_activation_hook( __FILE__, 'compropago_active' );


function compropago_init() {
  if ( !class_exists( 'WC_Payment_Gateway' ) ) return;
  /**
   * Localisation
   */
  load_plugin_textdomain('compropago', false, dirname( plugin_basename( __FILE__ ) ) . '/languages');

  /**
   * Gateway class include
   */
  require_once 'wc-gateway-compropago.php';

  /**
   * Add the Gateway to WooCommerce
   * @since 3.0.0
   **/
  function compropago_gateway($methods) {
    $methods[] = 'WC_Gateway_Compropago';

    wp_register_style( 'prefix-style-config', plugins_url('templates/css/foundation.css', __FILE__) );
    wp_enqueue_style( 'prefix-style-config' );

    return $methods;
  }

  add_filter('woocommerce_payment_gateways', 'compropago_gateway' );

  function comp_receipt( $order_id ) {
    global $wpdb;
    $dbprefix=$wpdb->prefix;

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    $order = new WC_Order( $order_id );

    $compropagoData = null;
    $compropagoOrder = $dbprefix . 'compropago_orders';

    if($mylink = $wpdb->get_row( "SELECT * FROM {$compropagoOrder} WHERE storeOrderId = '{$order_id}'" )){
      $receipt = file_get_contents(__DIR__."/templates/receipt.html");
      $receipt = str_replace(':cpid:', $mylink->compropagoId, $receipt);

      echo $receipt;
    }
  }

  add_action( 'woocommerce_thankyou', 'comp_receipt',1 );
}


add_action('plugins_loaded', 'compropago_init', 0);
