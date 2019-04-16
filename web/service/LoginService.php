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
                $cache_key = LoginModel::LOGIN_CUSTOMER_CACHE;
                break;
            case LoginModel::PROVIDER:
                $cache_key = LoginModel::LOGIN_PROVIDER_CACHE;
                break;
            case LoginModel::STORE:
                $cache_key = LoginModel::LOGIN_STORE_CACHE;
                break;
        }
        $Aes = new Aes();
        //$uid - 当做用户标示
        $uid = $Aes->AESEncryptRequest(LoginModel::AES_LEY,
            json_encode([
                "ip" => $remote_addr,
                "user_type" => $user_type], 320)
        );
        return $mlogin->register($cache_key, $uid) ? $uid : false;
    }

    /**
     * 解密
     * @param $uid - 用户唯一标示
     * @return bool
     */
    public function checkAuth($uid)
    {
        $Aes = new Aes();
        $token_json = $Aes->AESDecryptResponse(LoginModel::AES_LEY, $uid);
        return $token_json ? json_decode($token_json, true) : false;
    }


}
