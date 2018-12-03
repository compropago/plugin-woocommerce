<?php
/**
 * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
 */

use CompropagoSdk\Resources\Payments\Cash;


/**
 * Pagina de configuracion Compropag
 */
function cp_config_page() {

    cp_register_styles();
    wp_register_script('config-script', plugins_url('../templates/js/config-actions.js', __FILE__));
    wp_register_script('jquery_cp', plugins_url('../templates/js/jquery.js', __FILE__));
    wp_enqueue_script('jquery_cp');
    wp_enqueue_script('config-script');

    $baseUrl = get_site_url();

    $webhook = $baseUrl . '/wp-json/compropago/webhook';
    $configUrl = $baseUrl . '/wp-json/compropago/config';

    $cash_config = get_option('woocommerce_cpcash_settings');
    $spei_config = get_option('woocommerce_cpspei_settings');

    $cash_title = !empty(get_option('compropago_cash_title'))
        ? get_option('compropago_cash_title')
        : __('Pago en efectivo');

    $spei_title = !empty(get_option('compropago_spei_title'))
        ? get_option('compropago_spei_title')
        : 'Transferencia Bancaria';

    $webhook_id     = get_option('compropago_webhook_id');
    $publickey      = get_option('compropago_publickey');
    $privatekey     = get_option('compropago_privatekey');
    $live           = get_option('compropago_live') === 'yes';
    $descripcion    = get_option('compropago_descripcion');
    $instrucciones  = get_option('compropago_instrucciones');
    $titulo         = get_option('compropago_title');
    $complete_order = get_option('compropago_completed_order');
    $initial_state  = get_option('compropago_initial_state');
    $debug          = get_option('compropago_debug') === 'yes';

    $cash_enable    = $cash_config['enabled'] === 'yes';
    $spei_enable    = $spei_config['enabled'] === 'yes';

    try {
        $all_providers = (new Cash)->getDefaultProviders();
    } catch (Exception $e) {
        $all_providers = [];
    }

    $provs_config = get_option('compropago_provallowed');
    $flag_OXXO = false;

    if (!empty($provs_config)) {
        $allowed = explode(',', $provs_config);
        $active_providers = array();
        $disabled_providers = array();

        foreach ($all_providers as $provider) {
            $flag = true;

            foreach ($allowed as $value) {
                if ($value == $provider['internal_name']) {
                    $active_providers[] = $provider;
                    $flag = false;
                    break;
                }
            }

            if ($flag) $disabled_providers[] = $provider;
        }
    } else {
        $active_providers = $all_providers;
        $disabled_providers = array();
    }

    $retro = null;
    $image_load = plugins_url('../templates/img/ajax-loader.gif', __FILE__);

    include __DIR__ . "/../templates/config-page.php";
}


/**
 * Registro de la pagina de configuracion
 */
function cp_add_admin_page() {
    $page_title = 'ComproPago Conf';
    $menu_title = 'ComproPago';
    $capanility = 'manage_options';
    $menu_slug = 'compropago-config';
    $function = 'cp_config_page';
    $icon_url = plugins_url('../templates/img/logo.png', __FILE__);
    $position = 110;

    cp_register_styles();

    add_menu_page(
        $page_title,
        $menu_title,
        $capanility,
        $menu_slug,
        $function,
        $icon_url,
        $position
    );
}
