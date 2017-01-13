# CHANGELOG

## 2.1.0 2016-10-26
* Feature: Cambio Unificación de payloads de instrucciones, para pago referenciado, numeros de tarjeta y convenio bancario
* Feature: Mejoras en las unit test
* Deleted: CompropagoSdk\Factory\Abs\Instructions
* Deleted: CompropagoSdk\Factory\Abs\InstructionDetails
* Deleted: CompropagoSdk\Factory\V10\Instructions10
* Deleted: CompropagoSdk\Factory\V10\InstructionDetails10
* Added: CompropagoSdk\Models\Instructions
* Added: CompropagoSdk\Models\InstructionDetails 

## 2.0.0 2016-07-20
* Feature: cambio a patron Factory para manejo de versionamiénto API
* Feature: separación de vistas del SDK
* Note: renombrameinto de metodo **getProviders** por **listProviders**
* Note: renombrameinto de metodo **getWebhooks** por **listWebhooks**

## 1.2.0 (Deprecated) 2016-07-12
* Require: PHP >= 5.5
* Feature: CRUD de administracion de Webhooks
    * getWebhooks
    * createWebhook
    * updateWebhook
    * deleteWebhook
* Feature: Agregacion de caracteristicas al metodo getProviders
    * Forzar autentificación
    * Filtrado por limite de transaccion
    * Forzar recoleccion de proveedores desde base de datos

## 1.1.1 (Deprecated) 2016-04-19
* Rquire: PHP >= 5.5
* Feature: Service\sendSmsInstructions() Envio de instrucciones sms
* Feature: Service\evalAuth() Captura declarativa de error 500
* Added: Exceptions 
    * *Compropago\Sdk\Exceptions\BaseException*: Excepcion general Compropago
    * *Compropago\Sdk\Exceptions\HttpException*: Excepciones de codigo Http

## 1.1.0 (Deprecated) 2016-02-15
* Require: PHP >= 5.5
* Note: Merge Master

## 1.1.0-rc (Deprecated) 2016-02-14
* Require: PHP >= 5.5
* Marked: Cambio de namespace de Compropago a Compropago\Sdk
* Note: Code Styling PSR-2
* Note: Ajustes para optimizar PHP 5.5
* Utilizing: __DIR__ en lugar de dirname(__FILE__)

## 1.0.3 (Deprecated) 2016-01-24
* Feature: Iframe view 
* Bug Fix: Tpl errors
* Note: versión estable para plugins Woocommerce 3.0.0 y Prestashop 2.0.0

## 1.0.2 (Deprecated) 2016-01-18
* Require: PHP >= 5.3
* Bug Fix: Rest regresa correctament Headers, Body y Code del request
* Bug Fix:Services procesa el body response
* Feature: Services\evalAuth evalúa las llaves 
* Added: Utils\Store  
	* Evalúa el tipo de ejecución Client Keys vs Mode vs Compropago
	* Métodos para obtener el SQL para tablas de control de ordenes y transacciones 
* Feature: \Utils métodos para normalizar la respuesta entre diferentes versiones de API, para crear cargos y verificar cargos 
* Bug Fix : Estandarización de TPL para uso correcto con smarty
* Bug Fix: Request y Curl envía los query string de manera correcta
* Added: Se incorpora Model\DataBase para esquema

## 1.0.1 (Deprecated) 2016-01-04
Liberación de versión estable:
* Require: PHP >= 5.3
* Queda establecida base de documentación 
* Manejo de Versiones: Mayor.Menor.Patch , -dev (desarrollo) , -RC (Release Candidate)
* El ciclo de desarrollo para parches y mejoras de la actual versión menor estable (1.0) se realiza sobre el branch "master", (dev-master en composer). 
* El desarrollo de la siguiente version menor se desarrollara en 1.1.0-dev 
* Cambios en arquitectura generará cambio de versión Mayor, y se desarrollara en su branch independiente ( 2.0.0-dev) 

## 1.0.1-dev(RC) (Deprecated) 2015-12-30
* Require: PHP >= 5.3
* Note: Ajustes varios de preparación para liberación de versión estable 1.0.1
* Note: Librería estandarizada a PSR-4 http://www.php-fig.org/psr/psr-4/
	* Vendor | Namespace Compropago
* Note: Estandarización para uso con composer
* Bug fix: Ajustes de compatibilidad de desarrollo para soportar PHP >= 5.3
	* Utilizing: dirname(__FILE__) en lugar de __DIR__ 
	* Removed: GuzzleHttp\ se crea branch para futura versión con compatibilidad PHP >= 5.5 en lugar de Curl
		* Branch https://github.com/compropago/compropago-php/tree/guzzle-6-support
	* Replaced: Conexión Curl base por clases en Compropago\Http . Algunos códigos tomados de  google/google-api-php-client https://github.com/google/google-api-php-client/tree/v1-master
* Fixed: HTML y CSS para views/php/providers de selección de tienda de manera genérica, https://github.com/compropago/compropago-php/commit/f2490716d2683b2398cd1dd2cd89427a6e353897
* Added: /views y /assets para el manejo de templates base en plugins
* Marked: ajustes en clases de static methods, protected y privates para mantener encapsulamiento  
* Feature: Compropago\Controllers\Views Incorporación de Controlador para normalizar el SDK en su uso dentro de plugins
* Added: Compropago\Exception scaffolding
* Added: Compropago\Utils\Utils métodos para manejo de strings y arrays
* Updated: Soporte de User Agent, app_client_name y app_client_version
* Various: Limpieza de código, Preparación de documentación y comentarios en código para siguiente liberación de versión (base general). se agrega LICENSE y CHANGELOG.md
* Restructured: A partir de la siguiente liberación estable se estructurara el nuevo esquema de tags y branches.


## 1.0.0-dev (Deprecated) 2015-12-25
* Require: PHP >= 5.5
* Require: GuzzleHttp v^6, GuzzleHttp Library https://github.com/guzzle/guzzle
* Feature: Se incorporan una serie de funcionalidades y patrones de diseño para uso tipo SDK
	* Compropago\Client Setup y manejo de identificación con en el API v.1.2
	* Compropago\Service métodos tipo wrapper para funciones básicas del API
		* getProviders() Obtiene la lista de tiendas donde realizar el pago
		* verifyOrder( $orderId ) Verifica el estatus de una orden
		* placeOrder( $params ) Realiza una nueva orden al API
	* Compropago\Rest Static Methods para el IO con el API
* Note:  Se eliminan tags de versiones para mantener consistencia al nuevo ciclo de versiones


## 1.0.0 (Deprecated) 2015-12-21
* Deprecate: Se substituye master por dev versión y scaffolding para nuevo ciclo de versiones, https://github.com/compropago/compropago-php/commit/dea1b86d5546faf1329b32234a551e88c3d8f7cf 


## 1.0.0 (Deprecated) 2015-08-07
* Modificacion para funcionamiento con la nueva versión del API, https://github.com/compropago/compropago-php/commit/281278893e2c8dfad0dab0d8ecd1a03e7fbde99f
	
