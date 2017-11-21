<?php

namespace CompropagoSdk\Factory;

class Factory
{
    /**
     * Generate an instance of a class from sorce data
     * 
     * @param $class
     * @param array $data
     * @return mixed
     * @throws \Exception
     * 
     * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
     */
    public static function getInstanceOf($class, $data=array())
    {
        if (is_string($data)) {
            $data = json_decode($data, true);
        } else if (!is_array($data)) {
            throw new \Exception('Data format is not factored');
        }

        switch ($class) {
            case 'CpOrderInfo':
                return Serialize::cpOrderInfo($data);
            case 'Customer':
                return Serialize::customer($data);
            case 'EvalAuthInfo':
                return Serialize::evalAuthInfo($data);
            case 'FeeDetails':
                return Serialize::feeDetails($data);
            case 'InstructionDetails':
                return Serialize::instructionDetails($data);
            case 'Instructions':
                return Serialize::instructions($data);
            case 'NewOrderInfo':
                return Serialize::newOrderInfo($data);
            case 'OrderInfo':
                return Serialize::orderInfo($data);
            case 'PlaceOrderInfo':
                return Serialize::placeOrderInfo($data);
            case 'Provider':
                return Serialize::provider($data);
            case 'ListProviders':
                $aux = [];
                foreach ($data as $prov) {
                    $aux[] = Serialize::provider($prov);
                }
                return $aux;
            case 'SmsData':
                return Serialize::smsData($data);
            case 'SmsInfo':
                return Serialize::smsInfo($data);
            case 'SmsObject':
                return Serialize::smsObject($data);
            case 'Webhook':
                return Serialize::webhook($data);
            case 'ListWebhooks':
                $aux = [];
                foreach ($data as $web) {
                    $aux[] = Serialize::webhook($web);
                }
                return $aux;
            default:
                throw new \Exception('Object not in factory');
        }
    }
}