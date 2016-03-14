> ## Si llego buscando el archivo de instalación para su tienda [Descargue la última versión dando click Aquí] [compropago-3-0-x]

Plugin para WooCommerce - ComproPago
===================================
## Descripción
Este modulo provee el servicio de ComproPago para poder generar intenciones de pago dentro de la plataforma WooCommerce. 

Con ComproPago puede recibir pagos en OXXO, 7Eleven y muchas tiendas más en todo México.

[Registrarse en ComproPago ] (https://compropago.com)


## Ayuda y Soporte de ComproPago

- [Centro de ayuda y soporte](https://compropago.com/ayuda-y-soporte)
- [Solicitar Integración](https://compropago.com/integracion)
- [Guía para Empezar a usar ComproPago](https://compropago.com/ayuda-y-soporte/como-comenzar-a-usar-compropago)
- [Información de Contacto](https://compropago.com/contacto)

## Requerimientos
* [WooCommerce 2.5.0 +] (https://www.woothemes.com/woocommerce/)
* [WordPress 4.4.1 +] (https://wordpress.org/download/)
* ComproPago PHP Sdk 1.0.x
* [PHP >= 5.4](http://www.php.net/)
* [PHP JSON extension](http://php.net/manual/en/book.json.php)
* [PHP cURL extension](http://php.net/manual/en/book.curl.php)

## Instalación:

1. Descomprimir y subir el folder de los archivos del plugin hacia el folder “wp-content/plugins/“, o bien usando el instalador de plugins de Wordpress: Plugins -> Añadir nuevo.
2. Activar el plugin.


## ¿Cómo trabaja el modulo?
Una vez que el cliente sabe que comprar y continua con el proceso de compra entrará a la opción de elegir metodo de pago justo aqui aparece la opción de pagar con ComproPago.

Cuando la orden de compra es completada, el cliente inicia el proceso para generar su intención de pago, selecciona el establecimiento y recibe las instrucciones para realizar el pago.

Una vez generada la intención de pago, dentro del panel de control de ComproPago la orden se muestra como "PENDIENTE" esto significa que el usuario esta por ir a hacer el deposito.

---

## Configurar el plugin

1. Navegar hacia: WooCommerce -> Settings -> Payment Gateways, elegir ComproPago llenar los campos Public_key and Private_key.

---

## Sincronización con la notificación Webhook
1. Ir al área de Webhooks en ComproPago https://compropago.com/panel/webhooks
2. Introducir la dirección: [direcciondetusitio.com]/wp-content/plugins/compropago/webhook.php
3. Dar click en el botón "Probar" y verificamos que el servidor de la tienda esta respondiendo, debera aparecer el mismo objeto que se envío. 

Una vez completados estos pasos el proceso de instalación queda completado.

## Documentación
### Documentación ComproPago Plugin WooCommerce

### Documentación de ComproPago
**[API de ComproPago] (https://compropago.com/documentacion/api)**

ComproPago te ofrece un API tipo REST para integrar pagos en efectivo en tu comercio electrónico o tus aplicaciones.


**[General] (https://compropago.com/documentacion)**

Información de Comisiones y Horarios, como Transferir tu dinero y la Seguridad que proporciona ComproPAgo


**[Herramientas] (https://compropago.com/documentacion/boton-pago)**
* Botón de pago
* Modo de pruebas/activo
* WebHooks
* Librerías y Plugins
* Shopify

## Guía de Versiones

| Version | Status      |  WordPress    |  WooCommerce  | PHP     | Archivo                    | 
|---------|-------------|---------------|---------------|---------|----------------------------|
| 2.4.0   | EOL			| <= 4.4.0 		| <2.5			| 5.2 +   | [v2.4.0][compropago-2-4-0] |
| 3.0.0   | EOL		    | 4.4.1 + 		| 2.5.0 + 		| 5.3 +   | [v3.0.x][compropago-3-0-0] |
| 3.0.x   | Latest      | 4.4.1 + 		| 2.5.0 + 		| 5.5 +   | [v3.0.x][compropago-3-0-x] |


[compropago-3-0-x]: https://s3.amazonaws.com/compropago/plugins/woocommerce/compropago-wc-3-0-2.zip
[compropago-3-0-0]: https://s3.amazonaws.com/compropago/plugins/woocommerce/compropago-wc-3-0-0.zip
[compropago-2-4-0]: https://s3.amazonaws.com/compropago/plugins/woocommerce/compropago-wc-2-4-0.zip
