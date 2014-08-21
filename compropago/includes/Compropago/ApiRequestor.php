<?php

class Compropago_ApiRequestor
{
  public $apiKey;

  public function __construct($apiKey=null)
  {
    $this->_apiKey = $apiKey;
  }

  public static function apiUrl($url='')
  {
    $apiBase = Compropago::$apiBase;
    return "$apiBase$url";
  }

  public static function utf8($value)
  {
    if (is_string($value))
      return utf8_encode($value);
    else
      return $value;
  }

  private static function _encodeObjects($d)
  {
    if ($d instanceof Compropago_ApiRequestor) {
      return $d->id;
    } else if ($d === true) {
      return 'true';
    } else if ($d === false) {
      return 'false';
    } else if (is_array($d)) {
      $res = array();
      foreach ($d as $k => $v)
	$res[$k] = self::_encodeObjects($v);
      return $res;
    } else {
      return $d;
    }
  }

  public static function encode($d)
  {
    return http_build_query($d, null, '&');
  }

  public function request($meth, $url, $params=null)
  {
    if (!$params)
      $params = array();
    list($rbody, $rcode, $myApiKey) = $this->_requestRaw($meth, $url, $params);
    $resp = $this->_interpretResponse($rbody, $rcode);
    return array($resp, $myApiKey);
  }

  public function handleApiError($rbody, $rcode, $resp)
  {
    if (!is_array($resp) || !isset($resp['error']))
      throw new Compropago_ApiError("Invalid response object from API: $rbody (HTTP response code was $rcode)", $rcode, $rbody, $resp);
    $error = $resp['error'];
    switch ($rcode) {
    case 400:
    case 404:
      throw new Compropago_InvalidRequestError(isset($error['message']) ? $error['message'] : null,
                                           isset($error['param']) ? $error['param'] : null,
                                           $rcode, $rbody, $resp);
    case 401:
      throw new Compropago_AuthenticationError(isset($error['message']) ? $error['message'] : null, $rcode, $rbody, $resp);
    case 402:
      throw new Compropago_CardError(isset($error['message']) ? $error['message'] : null,
                                 isset($error['param']) ? $error['param'] : null,
                                 isset($error['code']) ? $error['code'] : null,
                                 $rcode, $rbody, $resp);
    default:
      throw new Compropago_ApiError(isset($error['message']) ? $error['message'] : null, $rcode, $rbody, $resp);
    }
  }

  private function _requestRaw($meth, $url, $params)
  {
    $myApiKey = $this->_apiKey;
    if (!$myApiKey)
      $myApiKey = Compropago::$apiKey;
    if (!$myApiKey)
      throw new Compropago_AuthenticationError('No API key provided.  (HINT: set your API key using "Compropago::setApiKey(<API-KEY>)".  You can generate API keys from the Compropago web interface.  See https://compropago.com/api for details, or email support@compropago.com if you have any questions.');

    $absUrl = $this->apiUrl($url);
    $params = self::_encodeObjects($params);
    $langVersion = phpversion();
    $uname = php_uname();
    $ua = array('bindings_version' => Compropago::VERSION,
		'lang' => 'php',
		'lang_version' => $langVersion,
		'publisher' => 'compropago',
		'uname' => $uname);
    $headers = array('X-Compropago-Client-User-Agent: ' . json_encode($ua),
		     'User-Agent: Compropago/v1 PhpBindings/' . Compropago::VERSION,
                     'Authorization: Bearer ' . $myApiKey);
    list($rbody, $rcode) = $this->_curlRequest($meth, $absUrl, $headers, $params);
    return array($rbody, $rcode, $myApiKey);
  }

  private function _interpretResponse($rbody, $rcode)
  {
    try {
      $resp = json_decode($rbody, true);
    } catch (Exception $e) {
      throw new Compropago_ApiError("Invalid response body from API: $rbody (HTTP response code was $rcode)", $rcode, $rbody);
    }

    if ($rcode < 200 || $rcode >= 300) {
      $this->handleApiError($rbody, $rcode, $resp);
    }
    return $resp;
  }

  private function _curlRequest($meth, $absUrl, $headers, $params)
  {
    $curl = curl_init();
    $meth = strtolower($meth);
    $opts = array();
    if ($meth == 'get') {
      $opts[CURLOPT_HTTPGET] = 1;
      if (count($params) > 0) {
	$encoded = self::encode($params);
	$absUrl = "$absUrl?$encoded";
      }
    } else if ($meth == 'post') {
      $opts[CURLOPT_POST] = 1;
      $opts[CURLOPT_POSTFIELDS] = self::encode($params);
    } else if ($meth == 'delete')  {
      $opts[CURLOPT_CUSTOMREQUEST] = 'DELETE';
      if (count($params) > 0) {
	$encoded = self::encode($params);
	$absUrl = "$absUrl?$encoded";
      }
    } else {
      throw new Compropago_ApiError("Unrecognized method $meth");
    }

    $absUrl = self::utf8($absUrl);
    $opts[CURLOPT_URL] = $absUrl;
    $opts[CURLOPT_RETURNTRANSFER] = true;
    $opts[CURLOPT_CONNECTTIMEOUT] = 30;
    $opts[CURLOPT_TIMEOUT] = 80;
    $opts[CURLOPT_RETURNTRANSFER] = true;
    $opts[CURLOPT_HTTPHEADER] = $headers;
    if (!Compropago::$verifySslCerts)
      $opts[CURLOPT_SSL_VERIFYPEER] = false;

    curl_setopt_array($curl, $opts);
    $rbody = curl_exec($curl);

    $errno = curl_errno($curl);
    if ($errno == CURLE_SSL_CACERT ||
	$errno == CURLE_SSL_PEER_CERTIFICATE ||
	$errno == 77 // CURLE_SSL_CACERT_BADFILE (constant not defined in PHP though)
	) {
      array_push($headers, 'X-Compropago-Client-Info: {"ca":"using Compropago-supplied CA bundle"}');
      curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($curl, CURLOPT_CAINFO,
                  dirname(__FILE__) . '/../data/ca-certificates.crt');
      $rbody = curl_exec($curl);
    }

    if ($rbody === false) {
      $errno = curl_errno($curl);
      $message = curl_error($curl);
      curl_close($curl);
      $this->handleCurlError($errno, $message);
    }

    $rcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    return array($rbody, $rcode);
  }

  public function handleCurlError($errno, $message)
  {
    $apiBase = Compropago::$apiBase;
    switch ($errno) {
    case CURLE_COULDNT_CONNECT:
    case CURLE_COULDNT_RESOLVE_HOST:
    case CURLE_OPERATION_TIMEOUTED:
      $msg = "Could not connect to Compropago ($apiBase).  Please check your internet connection and try again.  If this problem persists, you should check Compropago's service status at https://twitter.com/compropagostatus, or let us know at support@compropago.com.";
      break;
    case CURLE_SSL_CACERT:
    case CURLE_SSL_PEER_CERTIFICATE:
      $msg = "Could not verify Compropago's SSL certificate.  Please make sure that your network is not intercepting certificates.  (Try going to $apiBase in your browser.)  If this problem persists, let us know at support@compropago.com.";
      break;
    default:
      $msg = "Unexpected error communicating with Compropago.  If this problem persists, let us know at support@compropago.com.";
    }

    $msg .= "\n\n(Network error [errno $errno]: $message)";
    throw new Compropago_ApiConnectionError($msg);
  }
}
