<?php
/**
 * Uninstall
 * 
 * Removes all options from DB when user deletes the plugin via WordPress backend.
 * @author Rodrigo Ayala <rodrigo@compropago.com>
 * @author Jose Beltran <jose.beltran@compropago.com>
 * @since 2.0
 *
 */

if ( !defined('WP_UNINSTALL_PLUGIN') ) {
	exit();
}

$wp_options = [
	'woocommerce_cpcash_settings',
	'woocommerce_cpspei_settings',
	'compropago_publickey',
	'compropago_privatekey',
	'compropago_live',
	'compropago_provallowed',
	'compropago_debug',
	'compropago_initial_state',
	'compropago_completed_order',
	'compropago_cash_title',
	'compropago_spei_title',
	'compropago_webhook_id'
];

foreach ($wp_options as $key) {
	delete_option($key);
}
