# ComproPago PHP SDK

[![stable](http://badges.github.io/stability-badges/dist/stable.svg)](http://github.com/badges/stability-badges) 

## Descripción

La librería de `ComproPago PHP SDK` le permite interactuar con el API de ComproPago en su aplicación.
También cuenta con los métodos necesarios para facilitarle su desarrollo por medio de los servicios 
más utilizados (SDK).

Con ComproPago puede recibir pagos en 7Eleven, Extra y muchas tiendas más en todo México.

[Registrarse en ComproPago](https://compropago.com)

## Índice de Contenidos

- [Ayuda y Soporte de ComproPago](#ayuda-y-soporte-de-compropago)
- [Requerimientos](#requerimientos)
- [Instalación ComproPago SDK](#instalación-compropago-sdk)
- [Documentación](#documentación)
- [Guía básica de Uso](#guía-básica-de-uso)
- [Guía de Versiones](#guía-de-versiones)


## Ayuda y Soporte de ComproPago

- [Centro de ayuda y soporte](https://compropago.com/ayuda-y-soporte)
- [Solicitar Integración](https://compropago.com/integracion)
- [Guía para Empezar a usar ComproPago](https://compropago.com/ayuda-y-soporte/como-comenzar-a-usar-compropago)
- [Información de Contacto](https://compropago.com/contacto)

## Requerimientos

* Composer
* PHP >= 5.5
* CURL Extension
* JSON Extension


## Instalación ComproPago PHP SDK

### Instalación por GitHub

Puede descargar alguna de las versiones que hemos publicado:

- [Consultar Versiones Publicadas en GitHub](https://github.com/compropago/compropago-php/releases)

O si o lo desea puede obtener el repositorio

```bash
#repositorio en su estado actual (*puede no ser versón estable*)
git clone https://github.com/compropago/compropago-php.git
```

Despues debara de incluir en su proyecto el archivo `CompropagoSdk\Client.php`, el cual contiene la funcion
**register_autoload** que cargara automaticamente todos los archivos del SDK.


```php
<?php
require_once 'path_to/CompropagoSdk/Client.php';

use CompropagoSdk\Client;
Client::register_autoload();

use CompropagoSdk\Factory\Factory;
```

### Instalación pro Composer

Puede descargar el SDK directamente desde el repositorio de composer con el siguiente comando:

```bash
composer require compropago/php-sdk
```

O si lo prefiere puede incluirlo directamente en su archivo composer.json

```json
{
  "require": {
    "compropago/php-sdk": "*"
  }
}
```

Para poder hacer uso de la librería es necesario incluir el archivo principal del SDK

```php
<?php

require 'vendor/autoload.php';

use CompropagoSdk\Client;
use CompropagoSdk\Factory\Factory;
```

## Guía básica de Uso

Se debe contar con una cuenta activa de ComproPago. [Registrarse en ComproPago](https://compropago.com)

### Configuración del Cliente

Para poder hacer uso de la gema y llamados al API es necesario que primero configure sus Llaves de conexión y crear 
un instancia de Client.
*Sus llaves las encontrara en su Panel de ComproPago en el menú Configuración.*

[Consulte Aquí sus Llaves](https://compropago.com/panel/configuracion)

```php
<?php
# @param string publickey     Llave publica correspondiente al modo de la tienda
# @param string privatekey    Llave privada correspondiente al modo de la tienda
# @param bool   live          Modo de la tienda (false = Test | true = Live)
$client = new Client(
    'pk_test_5989d8209974e2d62',  # publickey
    'sk_test_6ff4e982253c44c42',  # privatekey
    false                         # live
);
```

### Uso Básico de la Libreria

#### Llamados al los servicios por SDK

Para poder hacer uso de los servicos de ComproPago, solo debe de llamar a los metodos contenidos en la propiedad **api**
de la variable **client** como se muestra a continuación.


#### Métodos base del SDK

##### Crear una nueva orden de Pago


```php
<?php

# Se genera el objeto con la informacion de la orden
/**
 * @param string order_id          Id de la orden
 * @param string order_name        Nombre del producto o productos de la orden
 * @param float  order_price       Monto total de la orden
 * @param string customer_name     Nombre completo del cliente
 * @param string customer_email    Correo electronico del cliente
 * @param string payment_type      (default = SEVEN_ELEVEN) Valor del atributo internal_name' de un objeto 'Provider'
 * @param string currency          (default = MXN) Codigo de la moneda con la que se esta creando el cargo
 * @param int    expiration_time   (default = null) Fecha en formato Epoch la cual indica la fecha de expiración de la orden
 */
$order_info = [
    'order_id' => 12,
    'order_name' => 'M4 php sdk',
    'order_price' => 123.45,
    'customer_name' => 'Eduardo',
    'customer_email' => 'asd@asd.com',
    'payment_type' => 'SEVEN_ELEVEN',
    'currency' => 'MXN',
    'expiration_time' => 1484799158
];
$order = Factory::getInstanceOf('PlaceOrderInfo', $order_info);


# Llamada al metodo 'place_order' del API para generar la orden
# @param [PlaceOrderInfo] order
# @return [NewOrderInfo]
$neworder = $client->api->placeOrder($order);
```

###### Prototipo del metodo placeOrder()

```php
<?php
/**
 * @param $neworder 
 * @return \CompropagoSdk\Factory\Models\NewOrderInfo
 * @throws \Exception
 */
public function placeOrder($neworder){}
```

##### Verificar el Estatus de una orden

Para verificar el estatus de una orden generada es necesario llamar al metodo **verifyOrder** que provee el atributo
**api** del objeto **Client** y el cual regresa una instancia **CpOrderInfo**. este metodo recibe como parametro el ID
generado por ComproPago para cada orden. Tambien puede obtener este ID desde un objeto **NewOrderInfo** accediendo al 
atributo **id**.

```php
<?php
# Guardar el ID de la orden
$order_id = "ch_xxxx_xxx_xxx_xxxx";

# U obtenerlo de un objetdo NewOrderInfo
$order_id = $neworder->id;


# Se manda llamar al metodo del API para recuperar la informacion de la orden
$info = $client->api->verifyOrder($order_id);
```

###### Prototipo del metodo verifyOrder()

```php
<?php
/**
 * @param $orderId
 * @return \CompropagoSdk\Factory\Models\CpOrderInfo
 * @throws \Exception
 */
public function verifyOrder( $orderId ){}
```


##### Obtener el listado de las tiendas donde se puede realizar el Pago

Para obtener el listado de Proveedores disponibles para realizar el pago de las ordenes es necesario consutar el metodo
**listProviders** que se encuentra alojado en el atributo **api** del objeto **Client** y el cual regresa una instancia
de tipo **Array** la cual contendra objetos de tipo **Provider**

```php
<?php
$providers = $client->api->listProviders();
```

###### Prototipo del metodo listProviders()

```php
<?php
/**
 * @param $limit
 * @param $currency (Default="MXN") Supported Currencies "USD", "EUR" & "GBP"
 * @return array
 * @throws \Exception
 */
public function listProviders($limit = 0, $currency = 'MXN'){}
```

##### Envio de instrucciones SMS

Para realizar el el envio de las instrucciones de compra via SMS es necesario llamar al metodo **sendSmsInstructions**
que se encuentra alojado en el atributo **api** del objeto **Client** y el cual regresa una instancia de tipo **SmsInfo**

```php
<?php
# Numero al cual se enviaran las instrucciones
$phone_number = "55xxxxxxxx";

# Id de la orden de compra de cual se enviaran las instrucciones
$order_id = "ch_xxxxx-xxxxx-xxxxx-xxxxx";

# Llamada al metodo del API para envio de las instrucciones
$smsinfo = $client->api->sendSmsInstructions($phone_number , $order_id);
```

###### Prototipo del metodo sendSmsInstructions()

```php
<?php
/**
 * @param $number
 * @param $orderId
 * @return \CompropagoSdk\Factory\Models\SmsInfo
 * @throws \Exception
 */
public function sendSmsInstructions($number,$orderId){}
```

#### Webhooks

Los webhooks son de suma importancia para el proceso las ordenes de ComproPago, ya que estos se encargaran de recibir
las notificaciones de el cambio en los estatus de las ordenes de compra generadas, tambien deberan contener parte de la
logica de aprobacion en su tienda en linea. El proceso que siguenes el siguiente.

1. Cuando una orden cambia su estatus, nuestra plataforma le notificara a cada una de las rutas registradas, dicho
   cambio con la informacion de la orden modificada en formato JSON
2. Debera de recuperar este JSON en una cadena de texto para posterior mente convertirla a un objeto de todpo
   **CpOrderInfo** haciendo uso de la clase Factory que proporciona el SDK de la siguiente forma:

```php
<?php
# $cadena_obtenida es un String
$info = Factory::getInstanceOf('CpOrderInfo', $cadena_obtenida);
```

3. Generar la logica de aprovacion correspondiente al estatus de la orden.

##### Crear un nuevo Webhook

Para crear un nuevo Webhook en la cuenta, se debe de llamar al metodo **createWebhook** que se encuentra alojado en el
atributo **api** del objeto **Client** y el cual regresa una instancia de tipo **Webhook**

```php
<?php
# Se pasa como paramtro la URL al webhook
$webhook = $client->api->createWebhook('http://sitio.com/webhook');
```

###### Prototipo del metodo createWebhook()

```php
<?php
/**
 * @param $url
 * @return \CompropagoSdk\Factory\Models\Webhook
 * @throws \Exception
 */
public function createWebhook($url){}
```

##### Actualizar un Webhook

Para actualizar la url de un webhoo, se debe de llamar al metodo **updateWebhook** que se encuentra alojado en el
atributo **api** del objeto **Client** y el cual regresa una instancia de tipo **Webhook**

```php
<?php
$updated_webhook = $client->api->updateWebhook($webhook->id, 'http://sitio.com/nuevo_webhook');
```

###### Prototipo del metodo updateWebhook()

```php
<?php
/**
 * @param $webhookId
 * @param $url
 * @return \CompropagoSdk\Factory\Models\Webhook
 * @throws \Exception
 */
public function updateWebhook($webhookId, $url){}
```

##### Eliminar un Webhook

Para eliminar un webhook, se debe de llamar al metodo **deleteWebhook** que se encuentra alojado en el atributo **api**
del objeto **Client** y el cual regresa una instancia de tipo **Webhook**

```php
<?php
$deleted_webhook = $client->api->deleteWebhook( $webhook->getId() );
```

###### Prototipo del metodo deleteWebhook()

```php
<?php
/**
 * @param $webhookId
 * @return \CompropagoSdk\Factory\Models\Webhook
 * @throws \Exception
 */
public function deleteWebhook($webhookId){}
```

##### Obtener listado de Webhooks registrados

Para obtener la lista de webhooks registrados den una cuenta, se debe de llamar al metodo **listWebhook** que se
encuentra alojado en el atributo **api** del objeto **Client** y el cual regresa una instancia de tipo **Array** la cual
contiene objetos de tipo **Webhook**

```php
<?php
$all_webhooks = $client->api->listWebhooks();
```

###### Prototipo del metodo listWebhook()

```php
<?php
/**
 * @return array
 * @throws \Exception
 */
public function listWebhooks(){}
```
