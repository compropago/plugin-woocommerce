<?php
/**
 * @author Eduardo Aguilar <dante.aguilar41>
 */
namespace CompropagoSdk\Factory\Models;

use CompropagoSdk\Client;

class PlaceOrderInfo
{
    public $order_id;
    public $order_name;
    public $order_price;
    public $customer_name;
    public $customer_email;
    public $customer_phone;
    public $payment_type;
    public $currency;
    public $expiration_time;
    public $image_url;
    public $app_client_name;
    public $app_client_version;
    public $extras;

    /**
     * PlaceOrderInfo constructor.
     * @param string $order_id
     * @param string $order_name
     * @param mixed $order_price
     * @param string $customer_name
     * @param string $customer_email
     * @param string $payment_type
     * @param string $currency
     * @param null|string $customer_phone
     * @param null|int $expiration_time
     * @param null|string $image_url
     * @param string $app_client_name
     * @param string $app_client_version
     * @param null|array $extras
     */
    public function __construct(
        $order_id,
        $order_name,
        $order_price,
        $customer_name,
        $customer_email,
        $customer_phone=null,
        $payment_type="OXXO",
        $currency="MXN",
        $expiration_time=null,
        $image_url=null,
        $app_client_name="phpsdk",
        $app_client_version=Client::VERSION,
        $extras=null
    ) {
        $this->order_id = $order_id;
        $this->order_name = $order_name;
        $this->order_price = $order_price;
        $this->customer_name = $customer_name;
        $this->customer_email = $customer_email;
        $this->customer_phone = $customer_phone;
        $this->payment_type = $payment_type;
        $this->currency = $currency;
        $this->expiration_time = $expiration_time;
        $this->image_url = $image_url;
        $this->app_client_name = $app_client_name;
        $this->app_client_version = $app_client_version;
        $this->extras = $extras;
    }
}