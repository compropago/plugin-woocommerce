# Plugin para WooCommerce

Este modulo provee el servicio de ComproPago para poder generar intensiones de pago dentro de la plataforma WooCommerce.

* [Instalación](#install)
* [¿Cómo trabaja el modulo?](#howto)
* [Configuración](#setup)
* [Sincronización con los webhooks](#webhook)


<a name="install"></a>
## Instalación:

1. Subir los archivos del plugin hacia el folder “wp-content/plugins/“, o bien usando el instalador de plugins de Wordpress: Plugins -> Añadir nuevo.
2. Activar el plugin.

---

<a name="howto"></a>
## ¿Cómo trabaja el modulo?
Una vez que el cliente sabe que comprar y continua con el proceso de compra entrará a la opción de elegir metodo de pago justo aqui aparece la opción de pagar con ComproPago.

Una vez que el cliente completa su orden de compra iniciara el proceso para generar su intensión de pago, el cliente selecciona el establecimiento y recibe las instrucciones para realizar el pago. 

Una vez que el cliente genero su intención de pago, dentro del panel de control de ComproPago la orden se muestra como "PENDIENTE" esto significa que el usuario esta por ir a hacer el deposito.

---
<a name="setup"></a>
## Configurar el plugin

1. Navegar hacia: WooCommerce -> Settings -> Payment Gateways, elegir ComproPago llenar los campos Public_key and Private_key.
2. Agregar la URL de exito y fallido, para este caso se cambiara en ambos casos solo el nombre de dominio y el directorio raíz donde se instalo Wordpress. Note: La dirección estandar es [direcciondetusitio.com]/index.php/checkout/onepage/success/

---

<a name="webhook"></a>
## Sincronización con la notificación Webhook
1. Ir al area de Webhooks en ComproPago https://compropago.com/panel/webhooks
2. Introducir la dirección: [direcciondetusitio.com]/index.php/mpexpress/webhook/ 
3. Dar click en el botón "Probar" y verificamos que el servidor de la tienda esta respondiendo, debera aparecer el mensaje de "Order not valid" 

Una vez completados estos pasos el proceso de instalación queda completado.
