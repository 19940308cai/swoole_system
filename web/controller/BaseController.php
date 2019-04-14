<?php
/**
 * Created by PhpStorm.
 * User: caijiang
 * Date: 2019/4/11
 * Time: 上午12:02
 */

namespace web\controller;

use web\service\LoginService;

class BaseController
{
    static $success = "success";

    static $error = "error";

    static $allow_uri = [
        "/login/register"
    ];

    protected $request;

    protected $response;

    protected $token;

    public function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $response;
        if (!in_array($this->request->getRequestUri(), self::$allow_uri)) {
            $token = $this->request->get("token");
            if (null === $token) {
                exit("no token");
            }
            $sLogin = new LoginService();
            $token = $sLogin->checkAuth($token);
            if(false === $token){
                exit("faild token");
            }
        }
        $this->token = $this->request->get("token");
    }

}