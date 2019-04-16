<?php
/**
 * Created by PhpStorm.
 * User: caijiang
 * Date: 2019/4/10
 * Time: 下午7:41
 */

namespace web\model;

class LoginModel extends BaseModel
{

    const AES_LEY = "caijiangcaijiang";

    const LOGIN_STORE_CACHE = "LOGIN_CACHE:STORE";

    const LOGIN_CUSTOMER_CACHE = "LOGIN_CACHE:CUSTOMER";

    const LOGIN_PROVIDER_CACHE = "LOGIN_CACHE:PROVIDER";

    const CUSTOMER = "customer";

    const PROVIDER = "provider";

    const STORE = "store";

    /**
     * @param $cache_key
     * @param $uid
     * @return bool
     */
    public function register($cache_key, $uid)
    {
        $this->multiCommand($cache_key, function () use ($cache_key, $uid) {
            $this->db->sadd($cache_key, $uid);
            $this->db->expire($cache_key, self::DAY_SECONS);
        });
        return true;
    }
}