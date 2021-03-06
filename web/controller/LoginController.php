<?php
/**
 * Created by PhpStorm.
 * User: caijiang
 * Date: 2019/4/10
 * Time: 下午7:41
 */

namespace web\controller;



use web\service\LoginService;

class LoginController extends BaseController
{

    public function register()
    {
        try {
            $user_type = $this->request->get("user_type");
            if (null == $user_type) {
                throw new \Exception("must be select user_type");
            }
            $user_name = $this->request->get("user_name");
            if( null == $user_name){
                throw new \Exception("must be write user_name");
            }
            $slogin = new LoginService();
            $uid = $slogin->register($user_type, $user_name, $this->request->getRemoteAddr());
            if(false == $uid){
                return $this->response->json(500, self::$error, "register error");
            }else{
                return $this->response->json(200, self::$success, ["uid" => $uid]);
            }
        } catch (\Exception $e) {
            return $this->response->json(500, self::$error, $e->getMessage());
        }
    }


}