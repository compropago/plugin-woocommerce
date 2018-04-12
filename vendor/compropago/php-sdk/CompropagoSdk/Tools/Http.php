<?php

namespace CompropagoSdk\Tools;

class Http
{
    private $url;
    private $data;
    private $auth;
    private $method;
    private $headers;
    private $handler;

    /**
     * Http Constructor
     * @param string $url
     */
    public function __construct($url)
    {
        $this->url = $url;
        $this->headers = array();
        $this->handler = curl_init($this->url);

        curl_setopt($this->handler, CURLOPT_HEADER, true);
        curl_setopt($this->handler, CURLOPT_COOKIE, true);
        curl_setopt($this->handler, CURLOPT_RETURNTRANSFER, true);
    }

    /**
     * Set HTTP Method for the request
     * @param string $method
     * @throws \Exception
     */
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
                throw new \Exception('Not supported method');
        }

        curl_setopt($this->handler, CURLOPT_CUSTOMREQUEST, $this->method);
    }

    /**
     * Set basic auth params for the request
     * @param array $auth
     * @throws \Exception
     */
    public function setAuth(array $auth)
    {
        if (!empty($auth)) {
            if (array_key_exists('user', $auth) && array_key_exists('pass', $auth)) {
                $this->auth = $auth['user'].':'.$auth['pass'];
                curl_setopt($this->handler, CURLOPT_USERPWD, $this->auth);
            } else {
                $message = 'Cant assign the user and password for basic auth';
                throw new \Exception($message);
            }
        }

    }

    /**
     * Set json data for the request
     * @param array $data
     */
    public function setJsonData(array $data)
    {
        if (!empty($data)) {
            $this->data = json_encode($data);
            $this->headers = array_merge(
                $this->headers,
                [
                    'Content-Type' => 'application/json',
                    'Content-Length' => strlen($this->data)
                ]
            );

            curl_setopt($this->handler, CURLOPT_POSTFIELDS, $this->data);
        }
    }

    /**
     * Set request headers
     * @param array $headers
     */
    public function setHeaders(array $headers)
    {
        if (!empty($headers)) {
            $this->headers = array_merge($this->headers, $headers);
        }
    }

    /**
     * Execute request from previous configuration
     * @return HttpResponse
     * @throws \Exception
     */
    public function executeRequest()
    {
        $aux = array();

        foreach ($this->headers as $header => $value) {
            $aux[] = "$header: $value";
        }

        curl_setopt($this->handler, CURLOPT_HTTPHEADER, $aux);

        $body = curl_exec($this->handler);

        if(empty($body)){
            $code = curl_errno($this->handler);

            if ($code == 60 || $code == 77) {
                curl_setopt($this->handler, CURLOPT_CAINFO, __DIR__ . '/cacerts.pem');
                $body = curl_exec($this->handler);
            }

            if(empty($body)){
                $error = curl_error($this->handler);
                $code = curl_errno($this->handler);
                throw new \Exception($error, $code);
            }
        }

        $statusCode = curl_getinfo($this->handler, CURLINFO_HTTP_CODE);
        $cookies = curl_getinfo($this->handler, CURLINFO_COOKIELIST);

        $response = new HttpResponse();
        $response->cookies = $cookies;
        $response->statusCode = $statusCode;
        $this->getBodyAndHeaders($body, $response);

        curl_close($this->handler);

        return $response;
    }

    /**
     * Extract all headers from curl response
     * @param $body
     * @param HttpResponse $response
     * @return void
     */
    private function getBodyAndHeaders($body, &$response)
    {
        $headers = array();

        $data = explode("\n",$body);

        $flag = 1;

        foreach ($data as $part) {
            if ($flag == 1) {
                $headers['Protocol'] = explode(' ', $part)[0];
            } else if(count($data) == $flag) {
                $response->body = trim($part);
                break;
            } else {
                $aux = explode(': ', $part);

                if (count($aux) >= 2) {
                    $headers[$aux[0]] = trim($aux[1]);
                }
            }

            $flag++;
        }

        $response->headers = $headers;
    }

}