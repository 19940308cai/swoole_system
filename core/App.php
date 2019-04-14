<?php
/**
 * Created by PhpStorm.
 * User: caijiang
 * Date: 2019/4/11
 * Time: 上午12:11
 */

namespace core;

class App
{

    private $request;

    private $response;

    public function __construct()
    {
        $this->makeRequest();
        $this->makeResponse();
    }

    private function makeRequest()
    {
        $this->request = new \core\Request();
    }

    private function makeResponse()
    {
        $this->response = new \core\Response();
    }

    public function runMvc()
    {
        $this->request->parseMvc();
        $class_name = "web\controller\\" . $this->request->getController() . "Controller";
        $object = new $class_name($this->request, $this->response);
        if( false === method_exists($object, $this->request->getFunction())){
            die("not found '{$this->request->getFunction()}' method");
        }
        $response = call_user_func([$object, $this->request->getFunction()]);
        echo $this->parseResponse($response);
    }

    public function parseResponse($response)
    {
        $headers = $response->get_headers();
        foreach ($headers as $k => $v){
            header($k." ".$v);
        }
        return $response->get_response_body();
    }


}