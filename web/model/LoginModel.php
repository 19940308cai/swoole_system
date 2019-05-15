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

    const STORE_CACHE = "STORE_CACHE";

    const LOGIN_STORE_CACHE = "LOGIN_CACHE:STORE";

    const LOGIN_CUSTOMER_CACHE = "LOGIN_CACHE:CUSTOMER";

    const LOGIN_PROVIDER_CACHE = "LOGIN_CACHE:PROVIDER";

    const CUSTOMER = "customer";

    const CUSTOMER_INDEX = "CUSTOMER_INDEX";

    const PROVIDER = "provider";

    const PROVIDER_INDEX = "PROVIDER_INDEX";

    const STORE = "store";

    const STORE_INDEX = "STORE_INDEX";

    public $allow_function = [self::CUSTOMER, self::PROVIDER, self::STORE];

    /**
     * @param $user_type
     * @param $user_name
     * @param $uid
     * @return bool
     */
    public function register($user_type, $user_name, $uid)
    {
        if (!in_array($user_type, $this->allow_function)) {
            return false;
        }
        return call_user_func_array([$this, $user_type], [$user_type, $user_name, $uid]);
    }

    public function customer($user_type, $user_name, $uid)
    {
        $this->multiCommand([], function () use ($user_type, $user_name, $uid) {
            $this->db->hSet(self::LOGIN_CUSTOMER_CACHE, $uid, json_encode([
                "user_type" => $user_type,
                "user_name" => $user_name,
                "uid" => $uid,
                "created_at" => date("Y-m-d H:i:s")
            ], 320));
        });
        return true;
    }

    public function provider($user_type, $user_name, $uid)
    {
        $this->multiCommand([], function () use ($user_type, $user_name, $uid) {
            $this->db->hSet(self::LOGIN_PROVIDER_CACHE, $uid, json_encode([
                "user_type" => $user_type,
                "user_name" => $user_name,
                "uid" => $uid,
                "created_at" => date("Y-m-d H:i:s")
            ], 320));
        });
        return true;
    }

    public function store($user_type, $user_name, $uid)
    {
        if ($this->db->hGet(self::LOGIN_STORE_CACHE, $uid)) {
            return true;
        }
        $store_index = $this->db->incr(self::STORE_INDEX);
        $this->multiCommand([], function () use ($user_type, $user_name, $uid, $store_index) {
            $this->db->hSet(self::STORE_CACHE, $store_index, json_encode([
                "store_index" => $store_index,
                "store_name" => "[" . $user_name . "]的门店",
            ], 320));

            $this->db->hSet(self::LOGIN_STORE_CACHE, $uid, json_encode([
                "user_type" => $user_type,
                "user_name" => $user_name,
                "store_index" => $store_index,
                "uid" => $uid,
                "created_at" => date("Y-m-d H:i:s")
            ], 320));
        });
        return true;
    }

    public function getStoreProvider($store_id)
    {
        $stores = $this->db->hGetAll(self::LOGIN_STORE_CACHE);
        if (!$stores) {
            return [];
        } else {
            $tmp = [];
            foreach ($stores as $uid => $store_msg) {
                $store_msg = json_decode($store_msg, true);
                if ($store_id == $store_msg['store_index']) {
                    array_push($tmp, $store_msg);
                }
            }
            return $tmp;
        }
    }


}