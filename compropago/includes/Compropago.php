<?php

// Tested on PHP 5.2, 5.3

// This snippet (and some of the curl code) due to the Facebook SDK.
if (!function_exists('curl_init')) {
  throw new Exception('Compropago needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
  throw new Exception('Compropago needs the JSON PHP extension.');
}


abstract class Compropago
{
  public static $apiKey;
  public static $apiBase = 'https://api.compropago.com/v1';
  public static $verifySslCerts = true;
  const VERSION = '1.7.2';

  public static function getApiKey()
  {
    return self::$apiKey;
  }

  public static function setApiKey($apiKey)
  {
    self::$apiKey = $apiKey;
  }

  public static function getVerifySslCerts() {
    return self::$verifySslCerts;
  }

  public static function setVerifySslCerts($verify) {
    self::$verifySslCerts = $verify;
  }
}


// Utilities
require(dirname(__FILE__) . '/Compropago/Util.php');
require(dirname(__FILE__) . '/Compropago/Util/Set.php');

// Errors
require(dirname(__FILE__) . '/Compropago/Error.php');
require(dirname(__FILE__) . '/Compropago/ApiError.php');
require(dirname(__FILE__) . '/Compropago/ApiConnectionError.php');
require(dirname(__FILE__) . '/Compropago/AuthenticationError.php');
require(dirname(__FILE__) . '/Compropago/CardError.php');
require(dirname(__FILE__) . '/Compropago/InvalidRequestError.php');

// Plumbing
require(dirname(__FILE__) . '/Compropago/Object.php');
require(dirname(__FILE__) . '/Compropago/ApiRequestor.php');
require(dirname(__FILE__) . '/Compropago/ApiResource.php');

// Compropago API Resources
require(dirname(__FILE__) . '/Compropago/Charge.php');
require(dirname(__FILE__) . '/Compropago/Customer.php');
require(dirname(__FILE__) . '/Compropago/Invoice.php');
require(dirname(__FILE__) . '/Compropago/InvoiceItem.php');
require(dirname(__FILE__) . '/Compropago/Plan.php');
require(dirname(__FILE__) . '/Compropago/Token.php');
require(dirname(__FILE__) . '/Compropago/Coupon.php');
require(dirname(__FILE__) . '/Compropago/Event.php');
require(dirname(__FILE__) . '/Compropago/Transfer.php');
