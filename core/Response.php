<?php
/**
 * Created by PhpStorm.
 * User: caijiang
 * Date: 2019/4/11
 * Time: 上午12:14
 */

namespace core;


class Response
{
    private $header;

    private $response_body;

    public function __construct()
    {

    }

    public function json($code, $message, $data)
    {
        $this->header["Content-Type:"] = "application/json";
        $this->register_body(json_encode([
            "code" => $code,
            "message" => $message,
            "data" => $data
        ], 320));
        return $this;
    }

    public function register_body($body)
    {
        $this->response_body = $body;
    }


    public function get_headers()
    {
        return $this->header;
    }

    public function get_response_body()
    {
        return $this->response_body;
    }




    
}