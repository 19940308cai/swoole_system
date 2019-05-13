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
     * @param $user_name
     * @return bool
     * @throws \Exception
     */
    public function register($user_type, $user_name, $remote_addr)
    {
        $mlogin = new LoginModel();
        //构造用户ID
        $Aes = new Aes();
        $uid = $Aes->AESEncryptRequest(LoginModel::AES_LEY,
            json_encode([
                "ip" => $remote_addr,
                "user_type" => $user_type], 320)
        );
        return $mlogin->register($user_type, $user_name, $uid) ? $uid : false;
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
