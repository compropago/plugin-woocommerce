<?php
/**
 * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
 */

use CompropagoSdk\Client;

/**
 * Pagina de configuracion Compropago
 */
function compropago_config_page(){
    register_styles();

    wp_register_script('config-script', plugins_url('../templates/js/config-actions.js', __FILE__));
    wp_register_script('jquery_cp', plugins_url('../templates/js/jquery.js', __FILE__));
    wp_enqueue_script('jquery_cp');
    wp_enqueue_script('config-script');

    $webhook = get_site_url() . '/wp-json/compropago/webhook';

    $cash_config = get_option('woocommerce_cpcash_settings');
    $spei_config = get_option('woocommerce_cpspei_settings');

    $cash_title     = !empty(get_option('compropago_cash_title')) ? get_option('compropago_cash_title') : 'Pago en efectivo';
    $spei_title     = !empty(get_option('compropago_spei_title')) ? get_option('compropago_spei_title') : 'Transferencia SPEI';

    $publickey      = get_option('compropago_publickey');
    $privatekey     = get_option('compropago_privatekey');
    $live           = get_option('compropago_live') === 'yes';
    $descripcion    = get_option('compropago_descripcion');
    $instrucciones  = get_option('compropago_instrucciones');
    $titulo         = get_option('compropago_title');
    $complete_order = get_option('compropago_completed_order');
    $initial_state  = get_option('compropago_initial_state');
    $debug          = get_option('compropago_debug');

    $cash_enable    = $cash_config['enabled'] === 'yes';
    $spei_enable    = $spei_config['enabled'] === 'yes';

    $client = new Client($publickey, $privatekey, $live);

    $all_providers = $client->api->listDefaultProviders();
    $provs_config = get_option('compropago_provallowed');
    $flag_OXXO = false;

    if (!empty($provs_config)) {
        $allowed = explode(',', $provs_config);
        $active_providers = array();
        $disabled_providers = array();

        foreach ($all_providers as $provider) {
            $flag = true;

            foreach ($allowed as $value) {
                if ($value == $provider->internal_name) {
                    $active_providers[] = $provider;
                    $flag = false;
                    break;
                }
            }

            if ($flag) { $disabled_providers[] = $provider; }
        }
    } else {
        $active_providers = $all_providers;
        $disabled_providers = array();
    }

    $retro = Utils::retroalimentacion(
        $publickey,
        $privatekey,
        $live,
        array(
            'cash' => $cash_config,
            'spei' => $spei_config
        )
    );
    $image_load = plugins_url('../templates/img/ajax-loader.gif', __FILE__);

    include __DIR__ . "/../templates/config-page.php";
}


/**
 * Registro de la pagina de configuracion
 */
function compropago_add_admin_page(){
    add_menu_page(
        'ComproPago Config',
        'ComproPago',
        'manage_options',
        'compropago-config',
        'compropago_config_page',
        plugins_url('../templates/img/logo.png', __FILE__) , // custom icon
        110  // position menu
    );
}