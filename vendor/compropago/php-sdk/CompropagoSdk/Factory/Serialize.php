<?php

namespace CompropagoSdk\Factory;

use CompropagoSdk\Client;
use CompropagoSdk\Factory\Models\CpOrderInfo;
use CompropagoSdk\Factory\Models\Customer;
use CompropagoSdk\Factory\Models\EvalAuthInfo;
use CompropagoSdk\Factory\Models\Exchange;
use CompropagoSdk\Factory\Models\FeeDetails;
use CompropagoSdk\Factory\Models\InstructionDetails;
use CompropagoSdk\Factory\Models\Instructions;
use CompropagoSdk\Factory\Models\NewOrderInfo;
use CompropagoSdk\Factory\Models\OrderInfo;
use CompropagoSdk\Factory\Models\PlaceOrderInfo;
use CompropagoSdk\Factory\Models\Provider;
use CompropagoSdk\Factory\Models\SmsData;
use CompropagoSdk\Factory\Models\SmsInfo;
use CompropagoSdk\Factory\Models\SmsObject;
use CompropagoSdk\Factory\Models\Webhook;

class Serialize
{
    public static function cpOrderInfo($data=array())
    {
        if (empty($data)) {
            return new CpOrderInfo();
        } else {
            $obj = new CpOrderInfo();

            $obj->id = $data['id'];
            $obj->type = $data['type'];
            $obj->object = $data['object'];
            $obj->created = $data['created'];
            $obj->paid = $data['paid'];
            $obj->amount = $data['amount'];
            $obj->livemode = $data['livemode'];
            $obj->currency = $data['currency'];
            $obj->refunded = $data['refunded'];
            $obj->fee = $data['fee'];
            $obj->fee_details = self::feeDetails($data['fee_details']);
            $obj->order_info = self::orderInfo($data['order_info']);
            $obj->customer = self::customer($data['customer']);
            $obj->captured = $data['captured'];
            $obj->failure_message = $data['failure_message'];
            $obj->failure_code = $data['failure_code'];
            $obj->amount_refunded = $data['amount_refunded'];
            $obj->description = $data['description'];
            $obj->dispute = $data['dispute'];
            $obj->api_version = $data['api_version'];

            return $obj;
        }
    }

    public static function customer($data=array())
    {
        if (empty($data)) {
            return new Customer();
        } else {
            $obj = new Customer();

            $obj->customer_name = $data['customer_name'];
            $obj->customer_email = $data['customer_email'];
            $obj->customer_phone = $data['customer_phone'];

            return $obj;
        }
    }

    public static function evalAuthInfo($data=array())
    {
        if (empty($data)) {
            return new EvalAuthInfo();
        } else {
            $obj = new EvalAuthInfo();

            $obj->type = $data['type'];
            $obj->livemode = $data['livemode'];
            $obj->mode_key = $data['mode_key'];
            $obj->message = $data['message'];
            $obj->code = $data['code'];

            return $obj;
        }
    }

    public static function feeDetails($data=array())
    {
        if (empty($data)) {
            return new FeeDetails();
        } else {
            $obj = new FeeDetails();

            $obj->amount = isset($data['amount']) ? $data['amount'] : null;
            $obj->currency = isset($data['currency']) ? $data['currency'] : null;
            $obj->type = isset($data['type']) ? $data['type'] : null;
            $obj->description = isset($data['description']) ? $data['description'] : null;
            $obj->application = isset($data['application']) ? $data['application'] : null;
            $obj->tax_percent = isset($data['tax_percent']) ? $data['tax_percent'] : null;
            $obj->amount_refunded = isset($data['amount_refunded']) ? $data['amount_refunded'] : null;
            $obj->tax = isset($data['tax']) ? $data['tax'] : null;

            return $obj;
        }
    }

    public static function instructionDetails($data=array())
    {
        if (empty($data)) {
            return new InstructionDetails();
        } else {
            $obj = new InstructionDetails();

            $obj->amount = $data['amount'];
            $obj->store = $data['store'];
            $obj->payment_amount = $data['payment_amount'];
            $obj->payment_store = $data['payment_store'];
            $obj->bank_account_holder_name = $data['bank_account_holder_name'];
            $obj->bank_account_number = $data['bank_account_number'];
            $obj->bank_reference = $data['bank_reference'];
            $obj->company_reference_name = $data['company_reference_name'];
            $obj->company_reference_number = $data['company_reference_number'];
            $obj->company_bank_number = $data['company_bank_number'];
            $obj->order_reference_number = $data['order_reference_number'];
            $obj->bank_name = $data['bank_name'];

            return $obj;
        }
    }

    public static function instructions($data=array())
    {
        if (empty($data)) {
            return new Instructions();
        } else {
            $obj = new Instructions();

            $obj->description = $data['description'];
            $obj->step_1 = $data['step_1'];
            $obj->step_2 = $data['step_2'];
            $obj->step_3 = $data['step_3'];
            $obj->note_extra_comition = $data['note_extra_comition'];
            $obj->note_expiration_date = $data['note_expiration_date'];
            $obj->note_confirmation = $data['note_confirmation'];
            $obj->details = self::instructionDetails($data['details']);

            return $obj;
        }
    }

    public static function newOrderInfo($data=array())
    {
        if (empty($data)) {
            return new NewOrderInfo();
        } else {
            $obj = new NewOrderInfo();

            $obj->id = $data['id'];
            $obj->short_id = $data['short_id'];
            $obj->object = $data['object'];
            $obj->status = $data['status'];
            $obj->created = $data['created'];
            $obj->exp_date = $data['exp_date'];
            $obj->live_mode = $data['live_mode'];
            $obj->order_info = self::orderInfo($data['order_info']);
            $obj->fee_details = self::feeDetails($data['fee_details']);
            $obj->instructions = self::instructions($data['instructions']);
            $obj->api_version = $data['api_version'];

            return $obj;
        }
    }

    public static function exchange($data=array())
    {
        if (empty($data)) {
            return new Exchange();
        } else {
            $obj = new Exchange();

            $obj->rate = $data['rate'];
            $obj->request = $data['request'];
            $obj->origin_amount = $data['origin_amount'];
            $obj->final_amount = $data['final_amount'];
            $obj->origin_currency = $data['origin_currency'];
            $obj->final_currency = $data['final_currency'];
            $obj->exchange_id = $data['exchange_id'];

            return $obj;
        }
    }

    public static function orderInfo($data=array())
    {
        if (empty($data)) {
            return new OrderInfo();
        } else {
            $obj = new OrderInfo();

            $obj->order_id = isset($data['order_id']) ? $data['order_id'] : null;
            $obj->order_name = isset($data['order_name']) ? $data['order_name'] : null;
            $obj->order_price = isset($data['order_price']) ? $data['order_price'] : null;
            $obj->image_url = isset($data['image_url']) ? $data['image_url'] : null;
            $obj->exchage = self::exchange($data['exchange']);

            return $obj;
        }
    }

    public static function placeOrderInfo($data=array())
    {
        if (empty($data)) {
            return new PlaceOrderInfo(null, null, null, null, null);
        } else {
            return new PlaceOrderInfo(
                $data['order_id'],
                $data['order_name'],
                $data['order_price'],
                $data['customer_name'],
                $data['customer_email'],
                empty($data['payment_type']) ? 'OXXO' : $data['payment_type'],
                empty($data['currency']) ? 'MXN' : $data['currency'],
                empty($data['expiration_time']) ? null : $data['expiration_time'],
                empty($data['image_url']) ? '': $data['image_url'],
                empty($data['app_client_name']) ? 'php-sdk' : $data['app_client_name'],
                empty($data['app_client_version']) ? Client::VERSION : $data['app_client_version'],
                empty($data['latitude']) ? '' : $data['latitude'],
                empty($data['longitude']) ? '' : $data['longitude'],
                empty($data['cp']) ? '' : $data['cp']
            );
        }
    }

    public static function provider($data=array())
    {
        if (empty($data)) {
            return new Provider();
        } else {
            $obj = new Provider();

            $obj->name = $data['name'];
            $obj->store_image = $data['store_image'];
            $obj->availability = $data['availability'];
            $obj->is_active = $data['is_active'];
            $obj->internal_name = $data['internal_name'];
            $obj->image_small = $data['image_small'];
            $obj->image_medium = $data['image_medium'];
            $obj->image_large = $data['image_large'];
            $obj->transaction_limit = $data['transaction_limit'];
            $obj->rank = $data['rank'];

            return $obj;
        }
    }

    public static function smsData($data=array())
    {
        if (empty($data)) {
            return new SmsData();
        } else {
            $obj = new SmsData();

            $obj->object = self::smsObject($data['object']);

            return $obj;
        }
    }

    public static function smsInfo($data=array())
    {
        if (empty($data)) {
            return new SmsInfo();
        } else {
            $obj = new SmsInfo();

            $obj->type = $data['type'];
            $obj->object = $data['object'];
            $obj->data = self::smsData($data['data']);

            return $obj;
        }
    }

    public static function smsObject($data=array())
    {
        if (empty($data)) {
            return new SmsObject();
        } else {
            $obj = new SmsObject();

            $obj->id = $data['id'];
            $obj->short_id = $data['short_id'];
            $obj->object = $data['object'];

            return $obj;
        }
    }

    public static function webhook($data=array())
    {
        if (empty($data)) {
            return new Webhook();
        } else {
            $obj = new Webhook();

            $obj->id = isset($data['id']) ? $data['id'] : null;
            $obj->url = isset($data['url']) ? $data['url'] : null;
            $obj->mode = isset($data['mode']) ? $data['mode'] : null;
            $obj->status = isset($data['status']) ? $data['status'] : null;

            return $obj;
        }
    }
}