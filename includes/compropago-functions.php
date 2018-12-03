<?php
/**
 * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
 */


/**
 * Estilos generales
 */
function cp_register_styles(){
    wp_register_style( 'prefix-style', plugins_url('../templates/css/foundation.css', __FILE__) );
    wp_enqueue_style( 'prefix-style' );
}
