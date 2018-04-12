<?php
/**
 * Uninstall - removes all options from DB when user deletes the plugin via WordPress backend.
 * @author Rodrigo Ayala <rodrigo@compropago.com>
 * @since 2.0
 *
 */

if ( !defined('WP_UNINSTALL_PLUGIN') ) {
	exit();
}

delete_option('woocommerce_cpcash_settings');
delete_option('woocommerce_cpspei_settings');

delete_option('compropago_publickey');
delete_option('compropago_privatekey');
delete_option('compropago_live');
delete_option('compropago_provallowed');
delete_option('compropago_debug');
delete_option('compropago_initial_state');
delete_option('compropago_completed_order');

delete_option('compropago_cash_title');
delete_option('compropago_spei_title');