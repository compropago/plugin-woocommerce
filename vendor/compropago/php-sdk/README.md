[![Documentation Status](https://readthedocs.org/projects/compropago-php-sdk/badge/?version=latest)](http://compropago-php-sdk.readthedocs.org/es/latest/?badge=latest)

ComproPago, PHP API client (PHP-SDK)
==============================

## Descripción
La librería de ComproPago-PHP le permite interactuar con el API de ComproPago en su aplicación.  También cuenta con los métodos necesarios para facilitarle su desarrollo por medio de los servicios y vistas más utilizados (SDK). 

Con ComproPago puede recibir pagos en OXXO, 7Eleven y muchas tiendas más en todo México.

[Registrarse en ComproPago ] (https://compropago.com)

## Índice de Contenidos
- [Ayuda y Soporte de ComproPago] (#ayuda-y-soporte-de-compropago)
- [Requerimientos] (#requerimientos)
- [Instalación ComproPago SDK] (#instalación-compropago-sdk)
- [Documentación] (#documentación)
- [Guía básica de Uso] (#guía-básica-de-uso)
- [Guía de Versiones] (#guía-de-versiones)


## Ayuda y Soporte de ComproPago

- [Centro de ayuda y soporte](https://compropago.com/ayuda-y-soporte)
- [Solicitar Integración](https://compropago.com/integracion)
- [Guía para Empezar a usar ComproPago](https://compropago.com/ayuda-y-soporte/como-comenzar-a-usar-compropago)
- [Información de Contacto](https://compropago.com/contacto)

## Requerimientos

* [PHP >= 5.5](http://www.php.net/)
* [PHP JSON extension](http://php.net/manual/en/book.json.php)
* [PHP cURL extension](http://php.net/manual/en/book.curl.php)

## Instalación ComproPago SDK

### Instalación usando Composer

La manera recomenda de instalar la SDK de ComproPago es por medio de [Composer](http://getcomposer.org).
- [Como instalar Composer?](https://getcomposer.org/doc/00-intro.md)

Para instalar la última versión **Estable de la SDK**, ejecuta el comando de Composer:

```bash
composer require compropago/php-sdk
```

Posteriormente o en caso de erro de carga de archivos, volvemos a crear el autoload:
   
```bash
composer dumpautoload -o
```
   
O agregando manualmente al archivo composer.json
```bash
"require": { 
		"compropago/php-sdk":"^1.1"
	}
```
```bash
composer install
```

Después de la instalación para poder hacer uso de la librería es **necesario incluir** el autoloader de Composer:

```php
require 'vendor/autoload.php';
```

Para actualizar el SDK de ComproPago a la última versión estable ejecutar:

 ```bash
composer update
 ```
### Instalación descargando archivo ZIP

Descargar y descomprimir el archivo de la versión a utilizar:
- [Última Estable] [compropago-estable-dl]


Para poder hacer uso de la librería es **necesario incluir** el autoloader que se encuentra dentro de la carpeta **vendor** del archivo que descomprimió:
```php
require 'vendor/autoload.php';
```
###Instalación por GitHub

Puede descargar alguna de las versiones que hemos publicado:
- [Consultar Versiones Publicadas en GitHub](https://github.com/compropago/compropago-php/releases)

O si o lo desea puede obtener el repositorio
 ```bash
 #repositorio en su estado actual (*puede no ser versón estable*)
git clone https://github.com/compropago/compropago-php.git
 ```
 Para poder hacer uso de la librería es necesario que incluya **Todos** los archivos contenidos en la carpeta **src/Compropago** 
 
## Documentación
### Documentación PHP-SDK ComproPago

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

## Guía básica de Uso
Se debe contar con una cuenta activa de ComproPago. [Registrarse en ComproPago ] (https://compropago.com)

### General

Para poder hacer uso de la librería es necesario incluir el autoloader 
```php
require 'vendor/autoload.php';
```
El Namespace a utilizar dentro de la librería es **Compropago**.
```php
use Compropago\Sdk\Client; //Configuración de datos de conexión
use Compropago\Sdk\Service; //Llamados al API
use Compropago\Sdk\Controllers\Views;  //Inclusión de vistas, ej. Mostrar template de las tiendas donde pagar
```
Los Namespaces para los métodos se pueden ocupar a su conveniencia. Para mayor información consulte la [documentación de PHP acerca de Namespaces] (http://php.net/manual/en/language.namespaces.basics.php) . ej:
```php
/* Unqualified name */
use Compropago\Sdk\Client; 
$compropagoClient= new Client($compropagoConfig);
/* Fully qualified name */
$compropagoClient= new Compropago\Sdk\Client($compropagoConfig);
```
### Configuración del Cliente 
Para poder hacer uso del SDK y llamados al API es necesario que primero configure sus Llaves de conexión y crear un instancia de Client.
*Sus llaves las encontrara en su Panel de ComproPago en el menú Configuración.*

[Consulte Aquí sus Llaves] (https://compropago.com/panel/configuracion) 

```php
$compropagoConfig= array(
				//Llave pública
				'publickey'=>'pk_test_TULLAVEPUBLICA',
				//Llave privada 
				'privatekey'=>'sk_test_TULLAVE PRIVADA',
				//Esta probando?, utilice  'live'=>false
				'live'=>true 
				
		);
// Instancia del Client
$compropagoClient= new Compropago\Sdk\Client($compropagoConfig);
```
### Uso Básico del SDK

> ###### Consulte la documentación de la librería PHP-SDK de ComproPago para conocer más de sus capacidades, configuraciones y métodos. (docs-php-sdk-link)
 

#### Llamados al los servicios por SDK 
Para utilizar los métodos se necesita tener una instancia de Service. La cual recibe de parámetro el objeto de Client. 
```php
$compropagoService= new Compropago\Sdk\Service($compropagoClient);
```
#### Métodos base del SDK
##### Crear una nueva orden de Pago
```php
//Campos Obligatorios para poder realizar una nueva orden
$data = array(
		'order_id'    	     => 'testorderid',             // string para identificar la orden
		'order_price'        => '123.45',                  // float con el monto de la operación
		'order_name'         => 'Test Order Name',         // nombre para la orden
		'customer_name'      => 'Compropago Test',         // nombre del cliente
		'customer_email'     => 'test@compropago.com',     // email del cliente
		'payment_type'       => 'OXXO'                     // identificador de la tienda donde realizar el pago
);
//Obtenemos el JSON de la respuesta 
$response = $compropagoService->placeOrder($data);

```

##### Verificar el Estatus de una orden

```php
//El número de orden que queremos verificar
$orderId= 'ch_xxxxx-xxxxx-xxxxx-xxxxx'

//Obtenemos el JSON de la respuesta 
$response = $compropagoService->verifyOrder( $orderId );

```

##### Obtener el listado de las tiendas donde se puede realizar el Pago

```php
//Obtenemos el JSON de la respuesta 
$response = $compropagoService->getProviders( );

```

##### Obtener el HTML con los logos para que el usuario seleccione donde pagar

```php
<?php
$compropagoData['providers']=$compropagoService->getProviders(); //obtenemos el listado
$compropagoData['showlogo']='yes';                              //(yes|no) logos o select
$compropagoData['description']='Plugin Descriptor compropago';  // Título a mostrar
$compropagoData['instrucciones']='Compropago Instrucciones';    // texto de instrucciones
?>
<html>
<head>
	<!-- CSS de ComproPago-->
	<link rel="stylesheet" type="text/css" href="../assets/css/compropago.css">
</head>
<body>
	<?php
		//llamamos al controlador para mostrar el template 
		Compropago\Sdk\Controllers\Views::loadView('providers',$compropagoData);
	?>
</body>
</html>
```

### Llamados directos al API 
Para conocer los servicios del API visite la documentación: [API de ComproPago] (https://compropago.com/documentacion/api)

Utilice el método estático Compropago\Http\Rest::doExecute para consumir directamente el API, su estructura es la siguiente:

```php
/**
 * @param Compropago\Client $client  // Objeto Cliente configurado
 * @param string $service            // Servicio del API a llamar
 * @param mixed$query                // Información a enviar: query string 'foo=bar' o Array Asociativo array( 'foo'=>'bar')
 * @param string $method             // método para consumir 'GET' o 'POST'
 * @return Array                    // asociativo con responseBody, responseHeaders, responseCode
 */
Compropago\Sdk\Http\Rest::doExecute(Client $client,$service=null,$query=FALSE,$method='GET');
```

Por ejemplo para realizar una nueva orden de pago llamando directamente al API.
Documentación: [API:Crear un Cargo] (https://compropago.com/documentacion/api/crear-cargo)

```php
//Campos Obligatorios para poder realizar una nueva orden
$data = array(
		'order_id'    	     => 'testorderid',             // string para identificar la orden
		'order_price'        => '123.45',                  // float con el monto de la operación
		'order_name'         => 'Test Order Name',         // nombre para la orden
		'customer_name'      => 'Compropago Test',         // nombre del cliente
		'customer_email'     => 'test@compropago.com',     // email del cliente
		'payment_type'       => 'OXXO'                     // identificador de la tienda donde realizar el pago
);

$response=Compropago\Sdk\Http\Rest::doExecute($compropagoClient,'charges/',$data,'POST'); // enviamos la información de la orden y obtenemos la respuesta del API 

$body = json_decode( $response['responseBody'] );   // El cuerpo de la respuesta, volvemos el objeto JSON para procesarlo
$headers = $response['responseHeaders'];            // Los encabezados de la respuesta
$code = $response['responseCode'];                 // el código de la respuesta, 200 = Todo OK 

/**
 * Coloque a continuación su lógica para evaluar y procesar el resultado. 
 */

```


## Guía de Versiones

| Version | Status      | Packagist            | Namespace    | PHP | Repo                      | Docs                      | 
|---------|-------------|----------------------|--------------|-----|---------------------------|---------------------------|
| 1.0.x   | Maintained  | `compropago/php-sdk` | `Compropago` | 5.3 + | [v1.0.x][compropago-repo-1-0-x] | [v1][compropago-1-docs]   | 
| 1.1.x   | Latest      | `compropago/php-sdk` | `Compropago\Sdk` | 5.5 + | [v1.1.x][compropago-repo] | [v1][compropago-1-docs]   |

[compropago-repo]: https://github.com/compropago/compropago-php
[compropago-repo-1-0-x]: https://github.com/compropago/compropago-php/tree/1.0.x
[compropago-1-docs]: https://compropago.com/documentacion/api
[compropago-estable-dl]: https://s3.amazonaws.com/compropago/libraries/php/compropago-php-sdk-1-1-0.zip
