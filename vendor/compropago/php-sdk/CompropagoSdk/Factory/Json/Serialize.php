<?php
/**
 * Copyright 2015 Compropago.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
/**
 * Compropago php-sdk
 * @author Eduardo Aguilar <eduardo.aguilar@compropago.com>
 */


namespace CompropagoSdk\Factory\Json;


use CompropagoSdk\Factory\V10\CpOrderInfo10;
use CompropagoSdk\Factory\V10\EvalAuthInfo10;
use CompropagoSdk\Factory\V10\NewOrderInfo10;
use CompropagoSdk\Factory\V10\SmsInfo10;
use CompropagoSdk\Factory\V11\CpOrderInfo11;
use CompropagoSdk\Factory\V11\EvalAuthInfo11;
use CompropagoSdk\Factory\V11\NewOrderInfo11;
use CompropagoSdk\Factory\V11\SmsInfo11;


/**
 * Class Serialize Clase que convierte estandariza las diferentes respuestas en objetos similares
 * @package CompropagoSdk\Factory\Json
 */
class Serialize
{

    /**
     * @param $source
     * @return CpOrderInfo11
     */
    public static function cpOrderInfo11($source)
    {
        $res = new CpOrderInfo11();
        $obj = json_decode($source);

        $res->id = $obj->id;
        $res->type = $obj->type;
        $res->object = $obj->object;
        $res->created = $obj->created;
        $res->paid = $obj->paid;
        $res->amount = $obj->amount;
        $res->livemode = $obj->livemode;
        $res->currency = $obj->currency;
        $res->refunded = $obj->refunded;
        $res->fee = $obj->fee;

        $res->fee_details->amount = $obj->fee_details->amount;
        $res->fee_details->currency = $obj->fee_details->currency;
        $res->fee_details->type = $obj->fee_details->type;
        $res->fee_details->description = $obj->fee_details->description;
        $res->fee_details->application = $obj->fee_details->application;
        $res->fee_details->amount_refunded = $obj->fee_details->amount_refunded;
        $res->fee_details->tax = $obj->fee_details->tax;

        $res->order_info->order_id = $obj->order_info->order_id;
        $res->order_info->order_price = $obj->order_info->order_price;
        $res->order_info->order_name = $obj->order_info->order_name;
        $res->order_info->payment_method = $obj->order_info->payment_method;
        $res->order_info->store = $obj->order_info->store;
        $res->order_info->country = $obj->order_info->country;
        $res->order_info->image_url = $obj->order_info->image_url;
        $res->order_info->success_url = $obj->order_info->success_url;

        $res->customer->customer_name = $obj->customer->customer_name;
        $res->customer->customer_email = $obj->customer->customer_email;
        $res->customer->customer_phone = $obj->customer->customer_phone;

        $res->captured = $obj->captured;
        $res->failure_message = $obj->failure_message;
        $res->failure_code = $obj->failure_code;
        $res->amount_refunded = $obj->amount_refunded;
        $res->description = $obj->description;
        $res->dispute = $obj->dispute;

        return $res;
    }

    /**
     * @param $source
     * @return CpOrderInfo10
     */
    public static function cpOrderInfo10($source)
    {
        $res = new CpOrderInfo10();
        $obj = json_decode($source);

        $res->type = $obj->type;
        $res->object = $obj->object;

        $res->data->object->id = $obj->data->object->id;
        $res->data->object->object = $obj->data->object->object;
        $res->data->object->created_at = $obj->data->object->created_at;
        $res->data->object->paid = $obj->data->object->paid;
        $res->data->object->amount = $obj->data->object->amount;
        $res->data->object->currency = $obj->data->object->currency;
        $res->data->object->refunded = $obj->data->object->refunded;
        $res->data->object->fee = $obj->data->object->fee;

        $res->data->object->fee_details->amount = $obj->data->object->fee_details->amount;
        $res->data->object->fee_details->currency = $obj->data->object->fee_details->currency;
        $res->data->object->fee_details->type = $obj->data->object->fee_details->type;
        $res->data->object->fee_details->description = $obj->data->object->fee_details->description;
        $res->data->object->fee_details->application = $obj->data->object->fee_details->application;
        $res->data->object->fee_details->amount_refunded = $obj->data->object->fee_details->amount_refunded;

        $res->data->object->payment_details->object = $obj->data->object->payment_details->object;
        $res->data->object->payment_details->store = $obj->data->object->payment_details->store;
        $res->data->object->payment_details->country = $obj->data->object->payment_details->country;
        $res->data->object->payment_details->product_id = $obj->data->object->payment_details->product_id;
        $res->data->object->payment_details->product_price = $obj->data->object->payment_details->product_price;
        $res->data->object->payment_details->product_name = $obj->data->object->payment_details->product_name;
        $res->data->object->payment_details->image_url = $obj->data->object->payment_details->image_url;
        $res->data->object->payment_details->success_url = $obj->data->object->payment_details->success_url;
        $res->data->object->payment_details->customer_name = $obj->data->object->payment_details->customer_name;
        $res->data->object->payment_details->customer_email = $obj->data->object->payment_details->customer_email;
        $res->data->object->payment_details->customer_phone = $obj->data->object->payment_details->customer_phone;

        $res->data->object->captured = $obj->data->object->captured;
        $res->data->object->failure_message = $obj->data->object->failure_message;
        $res->data->object->failure_code = $obj->data->object->failure_code;
        $res->data->object->amount_refunded = $obj->data->object->amount_refunded;
        $res->data->object->description = $obj->data->object->description;
        $res->data->object->dispute = $obj->data->object->dispute;

        return $res;
    }

    /**
     * @param $source
     * @return NewOrderInfo10
     */
    public static function newOrderInfo10($source)
    {
        $res = new NewOrderInfo10();
        $obj = json_decode($source);

        $res->payment_id = $obj->payment_id;
        $res->short_payment_id = $obj->short_payment_id;
        $res->payment_status = $obj->payment_status;
        $res->creation_date = $obj->creation_date;
        $res->expiration_date = $obj->expiration_date;

        $res->product_information->product_id = $obj->product_information->product_id;
        $res->product_information->product_name = $obj->product_information->product_name;
        $res->product_information->product_price = $obj->product_information->product_price;
        $res->product_information->image_url = $obj->product_information->image_url;

        $res->payment_instructions->description = $obj->payment_instructions->description;
        $res->payment_instructions->step_1 = $obj->payment_instructions->step_1;
        $res->payment_instructions->step_2 = $obj->payment_instructions->step_2;
        $res->payment_instructions->step_3 = $obj->payment_instructions->step_3;
        $res->payment_instructions->note_extra_comition = $obj->payment_instructions->note_extra_comition;
        $res->payment_instructions->note_expiration_date = $obj->payment_instructions->note_expiration_date;
        $res->payment_instructions->note_confirmation = $obj->payment_instructions->note_confirmation;

        $res->payment_instructions->details->payment_amount = $obj->payment_instructions->details->payment_amount;
        $res->payment_instructions->details->payment_store = $obj->payment_instructions->details->payment_store;
        $res->payment_instructions->details->bank_account_number = $obj->payment_instructions->details->bank_account_number;
        $res->payment_instructions->details->bank_name = $obj->payment_instructions->details->bank_name;

        return $res;
    }

    /**
     * @param $source
     * @return NewOrderInfo11
     */
    public static function newOrderInfo11($source)
    {
        $res = new NewOrderInfo11();
        $obj = json_decode($source);

        $res->id = $obj->id;
        $res->short_id = $obj->short_id;
        $res->object = $obj->object;
        $res->status = $obj->status;
        $res->created = $obj->created;
        $res->exp_date = $obj->exp_date;
        $res->live_mode = $obj->live_mode;

        $res->order_info->order_id = $obj->order_info->order_id;
        $res->order_info->order_name = $obj->order_info->order_name;
        $res->order_info->order_price = $obj->order_info->order_price;
        $res->order_info->image_url = $obj->order_info->image_url;

        $res->fee_details->amount = $obj->fee_details->amount;
        $res->fee_details->tax = $obj->fee_details->tax;
        $res->fee_details->currency = $obj->fee_details->currency;
        $res->fee_details->type = isset($obj->fee_details->type) ? $obj->fee_details->type : null;
        $res->fee_details->description = $obj->fee_details->description;
        $res->fee_details->amount_refunded = $obj->fee_details->amount_refunded;

        $res->instructions->description = $obj->instructions->description;
        $res->instructions->step_1 = $obj->instructions->step_1;
        $res->instructions->step_2 = $obj->instructions->step_2;
        $res->instructions->step_3 = $obj->instructions->step_3;
        $res->instructions->note_extra_comition = $obj->instructions->note_extra_comition;
        $res->instructions->note_expiration_date = $obj->instructions->note_expiration_date;
        $res->instructions->note_confirmation = $obj->instructions->note_confirmation;

        $res->instructions->details->amount = $obj->instructions->details->amount;
        $res->instructions->details->store = $obj->instructions->details->store;
        $res->instructions->details->bank_account_number = $obj->instructions->details->bank_account_number;
        $res->instructions->details->bank_name = $obj->instructions->details->bank_name;

        return $res;
    }

    /**
     * @param $source
     * @return SmsInfo10
     */
    public static function smsInfo10($source)
    {
        $res = new SmsInfo10();
        $obj = json_decode($source);

        $res->type = $obj->type;
        $res->object = $obj->object;

        $res->payment->id = $obj->payment->id;
        $res->payment->short_id = $obj->payment->short_id;

        return $res;
    }

    /**
     * @param $source
     * @return SmsInfo11
     */
    public static function smsInfo11($source)
    {
        $res = new SmsInfo11();
        $obj = json_decode($source);

        $res->type = $obj->type;
        $res->object = $obj->object;

        $res->data->object->id = $obj->data->object->id;
        $res->data->object->short_id = $obj->data->object->short_id;
        $res->data->object->object = $obj->data->object->object;

        return $res;
    }
}