# Plugin para WooCommerce

Este modulo provee el servicio de ComproPago para poder generar intenciones de pago dentro de la plataforma WooCommerce.

* [Instalación](#install)
* [¿Cómo trabaja el modulo?](#howto)
* [Configuración](#setup)
* [Sincronización con los webhooks](#webhook)


<a name="install"></a>
## Instalación:

1. Descomprimir y subir el folder de los archivos del plugin hacia el folder “wp-content/plugins/“, o bien usando el instalador de plugins de Wordpress: Plugins -> Añadir nuevo.
2. Activar el plugin.

---

<a name="howto"></a>
## ¿Cómo trabaja el modulo?
Una vez que el cliente sabe que comprar y continua con el proceso de compra entrará a la opción de elegir metodo de pago justo aqui aparece la opción de pagar con ComproPago.

Cuando la orden de compra es completada, el cliente inicia el proceso para generar su intención de pago, selecciona el establecimiento y recibe las instrucciones para realizar el pago.

Una vez generada la intención de pago, dentro del panel de control de ComproPago la orden se muestra como "PENDIENTE" esto significa que el usuario esta por ir a hacer el deposito.

---
<a name="setup"></a>
## Configurar el plugin

1. Navegar hacia: WooCommerce -> Settings -> Payment Gateways, elegir ComproPago llenar los campos Public_key and Private_key.

---

<a name="webhook"></a>
## Sincronización con la notificación Webhook
1. Ir al área de Webhooks en ComproPago https://compropago.com/panel/webhooks
2. Introducir la dirección: [direcciondetusitio.com]/wp-content/plugins/compropago/webhook.php
3. Dar click en el botón "Probar" y verificamos que el servidor de la tienda esta respondiendo, debera aparecer el mismo objeto que se envío. 

Una vez completados estos pasos el proceso de instalación queda completado.
