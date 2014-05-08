<?php

abstract class Compropago_Util
{
  public static function isList($array)
  {
    if (!is_array($array))
      return false;
    // TODO: this isn't actually correct in general, but it's correct given Compropago's responses
    foreach (array_keys($array) as $k) {
      if (!is_numeric($k))
        return false;
    }
    return true;
  }

  public static function convertCompropagoObjectToArray($values)
  {
    $results = array();
    foreach ($values as $k => $v) {
      // FIXME: this is an encapsulation violation
      if (Compropago_Object::$_permanentAttributes->includes($k)) {
        continue;
      }
      if ($v instanceof Compropago_Object) {
        $results[$k] = $v->__toArray(true);
      }
      else if (is_array($v)) {
        $results[$k] = self::convertCompropagoObjectToArray($v);
      }
      else {
        $results[$k] = $v;
      }
    }
    return $results;
  }

  public static function convertToCompropagoObject($resp, $apiKey)
  {
    $types = array('charge' => 'Compropago_Charge',
		   'customer' => 'Compropago_Customer',
		   'invoice' => 'Compropago_Invoice',
		   'invoiceitem' => 'Compropago_InvoiceItem', 'event' => 'Compropago_Event',
		   'transfer' => 'Compropago_Transfer');
    if (self::isList($resp)) {
      $mapped = array();
      foreach ($resp as $i)
        array_push($mapped, self::convertToCompropagoObject($i, $apiKey));
      return $mapped;
    } else if (is_array($resp)) {
      if (isset($resp['object']) && is_string($resp['object']) && isset($types[$resp['object']]))
        $class = $types[$resp['object']];
      else
        $class = 'Compropago_Object';
      return Compropago_Object::scopedConstructFrom($class, $resp, $apiKey);
    } else {
      return $resp;
    }
  }
}
