<?php
/**
 * Created by PhpStorm.
 * User: caijiang
 * Date: 2019/4/10
 * Time: 下午7:41
 */

namespace web\service;


use lib\Aes;
use web\model\LoginModel;

class LoginService
{

    const AES_LEY = "caijiangcaijiang";

    const LOGIN_STORE_CACHE = "LOGIN_CACHE:STORE";

    const LOGIN_CUSTOMER_CACHE = "LOGIN_CACHE:CUSTOMER";

    const LOGIN_PROVIDER_CACHE = "LOGIN_CACHE:PROVIDER";


    /**
     * 注册
     * @param $user_type
     * @param $remote_addr
     * @return bool
     * @throws \Exception
     */
    public function register($user_type, $remote_addr)
    {
        $mlogin = new LoginModel();
        switch ($user_type) {
            case LoginModel::CUSTOMER:
                $cache_key = self::LOGIN_CUSTOMER_CACHE;
                break;
            case LoginModel::PROVIDER:
                $cache_key = self::LOGIN_PROVIDER_CACHE;
                break;
            case LoginModel::STORE:
                $cache_key = self::LOGIN_STORE_CACHE;
                break;
        }
        $Aes = new Aes();
        $token = $Aes->AESEncryptRequest(self::AES_LEY, json_encode(["ip" => $remote_addr, "user_type" => $user_type], 320));
        return $mlogin->register($cache_key, $token) ? $token : false;
    }

    /**
     * 解密
     * @param $token
     * @return bool
     */
    public function checkAuth($token)
    {
        $Aes = new Aes();
        $token_json = $Aes->AESDecryptResponse(self::AES_LEY, $token);
        return $token_json ? json_decode($token_json, true) : false;
    }


}
