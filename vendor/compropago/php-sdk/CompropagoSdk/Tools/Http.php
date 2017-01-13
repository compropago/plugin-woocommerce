<?php

namespace CompropagoSdk\Tools;

class Http
{
    private $url;
    private $data;
    private $auth;
    private $method;
    private $extra_headers;

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function setMethod($method)
    {
        switch ($method) {
            case 'GET':
            case 'POST':
            case 'PUT':
            case 'DELETE':
                $this->method = $method;
                break;
            default:
                throw new \Exception('Not supported method.');
        }
    }

    public function setAuth(array $auth)
    {
        if (array_key_exists('user', $auth) && array_key_exists('pass', $auth)) {
            $this->auth = $auth['user'].':'.$auth['pass'];
        }else{
            $this->auth = array();
        }
    }

    public function setData(array $data)
    {
        if (!empty($data)) {
            $this->data = json_encode($data);
        }
    }

    public function setExtraHeaders(array $headers)
    {
        if (!empty($headers)) {
            $this->extra_headers = $headers;
        }
    }

    public function executeRequest()
    {
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->method);

        if (!empty($this->auth)) {
            curl_setopt($ch, CURLOPT_USERPWD, $this->auth);
        }

        if (!empty($this->data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->data);
        }

        $headers = [
            'Content-Type' => 'application/json',
            'Cache-Control' => 'no-cache',
            'Content-Length' => strlen($this->data)
        ];

        $final_headers = [];

        if (!empty($this->extra_headers)) {
            foreach ($this->extra_headers as $key => $value) {
                $headers[$key] = $value;
            }
        }

        foreach ($headers as $key => $value) {
            $final_headers[] = $key.': '.$value;
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $final_headers);

        $response = curl_exec($ch);

        if(empty($response)){
            $code = curl_errno($ch);

            if ($code == 60 || $code == 77) {
                curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . '/cacerts.pem');
                $response = curl_exec($ch);
            }

            if(empty($response)){
                $error = curl_error($ch);
                $code = curl_errno($ch);
                throw new \Exception($error, $code);
            }
        }

        curl_close($ch);

        return $response;
    }

}