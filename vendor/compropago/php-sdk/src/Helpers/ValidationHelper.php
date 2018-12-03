<?php

namespace CompropagoSdk\Helpers;

use Requests_Response;

trait ValidationHelper
{
    /**
     * Validate if the response of the ComproPago API is a success response or an error response
     *
     * @param Requests_Response $res Response object from Requests library
     *
     * @return void
     *
     * @throws \Exception Request error or exception
     */
    public function validateResponse(Requests_Response $res)
    {
        $body = json_decode($res->body, true);

        # HTTP Error or API Error
        if ($res->status_code != 200 || (isset($body['code']) && $body['code'] != 200)) {
            $message = sprintf(
                'Request Error [%d]: %s',
                $res->status_code,
                $res->body
            );
            
            throw new \Exception($message, $res->status_code);
        }
    }
}
