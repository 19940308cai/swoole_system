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

    protected $uid;

    public function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $response;
        $this->uid = $this->request->get("uid");
        if (!in_array($this->request->getRequestUri(), self::$allow_uri)) {
            if (null === $this->uid) {
                exit("please register user");
            }
            $sLogin = new LoginService();
            $result = $sLogin->checkAuth($this->uid);
            if(false === $result){
                exit("faild user");
            }
        }
    }

}