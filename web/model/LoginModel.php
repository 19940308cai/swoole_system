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

    const CUSTOMER = "customer";

    const PROVIDER = "provider";

    const STORE = "store";

    public function register($cache_key, $token)
    {
        $this->multiCommand($cache_key, function () use ($cache_key, $token) {
            $this->sadd($cache_key, $token);
            $this->expire($cache_key, 1 * self::DAY_SECONS);
        });
        return true;
    }
}