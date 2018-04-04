<?php
/**
 * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
 */

require_once ABSPATH . 'wp-admin/includes/upgrade.php';

/**
 * @param $prefix string Database tables prefix
 * @return array
 */
function sql_drop_tables($prefix) {
    return array(
        'DROP TABLE IF EXISTS `' . $prefix . 'compropago_orders`;',
        'DROP TABLE IF EXISTS `' . $prefix . 'compropago_transactions`;'
    );
}

/**
 * @param $prefix string Database tables prefix
 * @return array
 */
function sql_create_tables($prefix) {
    return array(
        'CREATE TABLE `' . $prefix . 'compropago_orders` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `date` int(11) NOT NULL,
          `modified` int(11) NOT NULL,
          `type` varchar(50) NOT NULL,
          `compropagoId` varchar(50) NOT NULL,
          `compropagoStatus`varchar(50) NOT NULL,
          `storeCartId` varchar(255) NOT NULL,
          `storeOrderId` varchar(255) NOT NULL,
          `storeExtra` varchar(255) NOT NULL,
          `ioIn` mediumtext,
          `ioOut` mediumtext,
          PRIMARY KEY (`id`), UNIQUE KEY (`compropagoId`)
          )ENGINE=MyISAM DEFAULT CHARSET=utf8  DEFAULT COLLATE utf8_general_ci  AUTO_INCREMENT=1 ;',
        'CREATE TABLE `' . $prefix . 'compropago_transactions` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `orderId` int(11) NOT NULL,
          `date` int(11) NOT NULL,
          `compropagoId` varchar(50) NOT NULL,
          `compropagoStatus` varchar(50) NOT NULL,
          `compropagoStatusLast` varchar(50) NOT NULL,
          `ioIn` mediumtext,
          `ioOut` mediumtext,
          PRIMARY KEY (`id`)
          )ENGINE=MyISAM DEFAULT CHARSET=utf8  DEFAULT COLLATE utf8_general_ci  AUTO_INCREMENT=1 ;'
    );
}

/**
 * Rutina de instalacion para tabla de transacciones
 *
 * @throws Exception
 */
function compropago_active(){
    global $wpdb;

    $queries = sql_drop_tables($wpdb->prefix);

    foreach ($queries as $drop) {
        dbDelta($drop);
    }

    $queries = sql_create_tables($wpdb->prefix);

    foreach ($queries as $create) {
        if(!dbDelta($create)) {
            throw new Exception('Unable to Create ComproPago Tables, module cant be installed');
        }
    }
}