# ComproPago API - PHP SDK

[![Build Status](https://travis-ci.org/danteay/compropago-php.svg?branch=master)](https://travis-ci.org/danteay/compropago-php)

[![Run in Postman](https://run.pstmn.io/button.svg)](https://app.getpostman.com/run-collection/969668aea8f1464c3131#?env%5BComproPago%20API%5D=W3siZGVzY3JpcHRpb24iOnsiY29udGVudCI6IiIsInR5cGUiOiJ0ZXh0L3BsYWluIn0sInZhbHVlIjoiaHR0cHM6Ly9hcGkuY29tcHJvcGFnby5jb20iLCJrZXkiOiJob3N0IiwiZW5hYmxlZCI6dHJ1ZX0seyJkZXNjcmlwdGlvbiI6eyJjb250ZW50IjoiIiwidHlwZSI6InRleHQvcGxhaW4ifSwidmFsdWUiOiJwa190ZXN0XzYzOGU4YjE0MTEyNDIzYTA4NiIsImtleSI6InB1YmxpY19rZXkiLCJlbmFibGVkIjp0cnVlfSx7ImRlc2NyaXB0aW9uIjp7ImNvbnRlbnQiOiIiLCJ0eXBlIjoidGV4dC9wbGFpbiJ9LCJ2YWx1ZSI6InNrX3Rlc3RfOWM5NWUxNDk2MTQxNDI4MjJmIiwia2V5IjoicHJpdmF0ZV9rZXkiLCJlbmFibGVkIjp0cnVlfSx7InZhbHVlIjoiNTcyMjdjNzEtZDMzOS00ZDcwLTk2ZDAtMmNmY2Q1ZmY1NTBlIiwia2V5IjoiY2FzaF9vcmRlcl9pZCIsImVuYWJsZWQiOnRydWV9LHsidmFsdWUiOiJjaF9iYmI0NWQ1ZC0wZDEyLTRhMDgtYTk0Yy00MGFlNTgzMWVlNzEiLCJrZXkiOiJzcGVpX29yZGVyX2lkIiwiZW5hYmxlZCI6dHJ1ZX0seyJ2YWx1ZSI6ImIxYzEwM2U5LWFhOWEtNDFmYS1iNjFkLTA5MGVhNjg2MzQ5NSIsImtleSI6IndlYmhvb2tfaWQiLCJlbmFibGVkIjp0cnVlfSx7ImtleSI6Im9yZGVyX3RvdGFsIiwidmFsdWUiOiIwIiwiZGVzY3JpcHRpb24iOiIiLCJ0eXBlIjoidGV4dCIsImVuYWJsZWQiOnRydWV9LHsia2V5IjoiY3VycmVuY3kiLCJ2YWx1ZSI6Ik1YTiIsImRlc2NyaXB0aW9uIjoiIiwidHlwZSI6InRleHQiLCJlbmFibGVkIjp0cnVlfV0=)

La librería de `ComproPago PHP SDK` le permite interactuar con el API de ComproPago en su aplicación.
También cuenta con los métodos necesarios para facilitarle su desarrollo por medio de los servicios
más utilizados (SDK).

Con ComproPago puede recibir pagos en 7Eleven, Extra y muchas tiendas más en todo México.

## Requirements

* PHP >= 5.6
* Composer

## Installation

You need to install composer to download this package from Packagist. You can find the steps to install com poser from [here](https://getcomposer.org/download/). Once you have composer installed you add the package by running this command:

```bash
composer require compropago/php-sdk
```

Alternatively you can add the package to your `composer.json` file in the require section, and run the `composer install` command.

```json
"require": {
    "php": ">=5.6",
    "compropago/php-sdk": "^4"
},
```
