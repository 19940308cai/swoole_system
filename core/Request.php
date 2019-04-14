<?php
/**
 * Created by PhpStorm.
 * User: caijiang
 * Date: 2019/4/11
 * Time: 上午12:14
 */

namespace core;


class Request
{

    private $get;

    private $post;

    private $server;

    private $cookie;

    private $request_uri;

    private $controller;

    private $function;

    private $current_body;

    public function __construct()
    {
        $this->register_attribute();
        $this->register_current_request_body();
    }

    private function register_current_request_body()
    {
        if ($this->getMethod() == "GET") {
            $this->current_body = $this->get;
        } elseif ($this->getMethod() == "POST") {
            $this->current_body = $this->post;
        } else {
            $this->current_body = [];
        }
    }

    private function register_attribute()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->server = $_SERVER;
        $this->cookie = $_COOKIE;
    }

    public function parseMvc()
    {
        $this->request_uri = $this->get['s'];
        unset($this->get['s']);
        $uri_array = explode("/", ltrim($this->request_uri, "/"));
        $this->controller = ucwords($uri_array[0]);
        $this->function = $uri_array[1];
    }

    public function getRequestUri()
    {
        return $this->request_uri;
    }

    public function getController()
    {
        return $this->controller;
    }

    public function getFunction()
    {
        return $this->function;
    }

    public function getMethod()
    {
        return $this->server["REQUEST_METHOD"];
    }

    public function getAll()
    {
        return $this->current_body;
    }

    public function get($k)
    {
        return isset($this->current_body[$k]) ? $this->current_body[$k] : null;
    }

    public function getRemoteAddr()
    {
        return $this->server["REMOTE_ADDR"];
    }


}