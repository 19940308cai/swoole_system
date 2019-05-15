<?php
/**
 * Created by PhpStorm.
 * User: caijiang
 * Date: 2019/4/14
 * Time: 下午9:17
 */

namespace web\model;

class ProductModel extends BaseModel
{
    //门店 - 产品
    const PRODUCT_CACHE_STORE = "PRODUCT:CACHE:STORE:";

    //门店 - 产品 - 权重
    const PRODUCT_CACHE_STORE_SCORE = "PRODUCT:CACHE:STORE:SCORE:";

    //产品primary key
    const PRODUCT_INDEX_CACHE = "PRODUCT:INDEX:CACHE";

    //门店 - 产品名称 key
    const PRODUCT_NAME_CACHE_ALL = "PRODUCT_NAME:CACHE:ALL:STORE:";

    //所有门店 - 产品
    const PRODUCT_CACHE_ALL = "PRODUCT:CACHE:STORE:ALL";

    //所有门店 - 产品 - 权重
    const PRODUCT_CACHE_STORE_ALL_SCORE = "PRODUCT:CACHE:STORE_ALL:SCORE";

    //门店 - 产品 - 用户
    const PRODUCT_CACHE_USER_STORE = "PRODUCT:CACHE:USER:STORE:";

    /**
     * @param $product_id
     * @param $store_id
     * @param $uid
     * @return bool
     */
    public function buyProduct($product_id, $store_id, $uid)
    {
        /**
         * 门店纬度: 产品 - uid映射
         * 用户纬度: 产品 - store_id映射
         */
        $store_user_product_key = self::PRODUCT_CACHE_USER_STORE . $store_id;
        $user_store_product_key = UserModel::PRODUCT_CACHE_USER_STORE . $uid;
        //检查是否重复购买
        if ($this->db->hExists($user_store_product_key, $product_id)) {
            throw new \Exception("不能重复购买...");
        }
        $this->lock(implode("-", [
            $product_id,
            $store_id,
            $uid
        ]));
        $command_result = $this->multiCommand([], function () use ($store_user_product_key, $user_store_product_key, $product_id, $uid, $store_id) {
            $this->db->hSet($store_user_product_key, $product_id, $uid);
            $this->db->hSet($user_store_product_key, $product_id, $store_id);
        });
        $this->unlock(implode("-", [
            $product_id,
            $store_id,
            $uid
        ]));
        return $command_result ? true : false;
    }

    /**
     * @param $product_name
     * @param $product_num
     * @param $store_user
     * @return bool
     * @throws \Exception
     */
    private function addProduct($product_name, $product_num, $store_user)
    {

        $store_product_key = self::PRODUCT_CACHE_STORE . $store_user['store_index'];
        $product_key = self::PRODUCT_CACHE_ALL;
        $product_name_key = self::PRODUCT_NAME_CACHE_ALL . $store_user['store_index'];
        $product_primary_key = self::PRODUCT_INDEX_CACHE;
        $product_store_score_key = self::PRODUCT_CACHE_STORE_SCORE . $store_user['store_index'];
        $product_score_key = self::PRODUCT_CACHE_STORE_ALL_SCORE;
        //去除重复
        if ($this->db->sIsMember($product_name_key, $product_name)) {
            throw new \Exception("不允许重复提交...");
        }
        //计算产品主键
        $primary_key = $this->db->incr($product_primary_key);
        $product = json_encode(["product_id" => $primary_key, "product_name" => $product_name, "product_num" => $product_num, "store_index" => $store_user['store_index'], "creator" => $store_user['uid']], 320);
        $command_result = $this->multiCommand([
            $store_product_key,
            $product_key,
            $product_name_key
        ], function () use ($product_store_score_key, $product_score_key, $product_name_key, $product_name, $primary_key, $product_key, $store_product_key, $product) {
            $this->db->zadd($product_store_score_key, time(), $primary_key);
            $this->db->zadd($product_score_key, time(), $primary_key);
            $this->db->sadd($product_name_key, $product_name);
            $this->db->hSet($product_key, $primary_key, $product);
            $this->db->hSet($store_product_key, $primary_key, $product);
        });
        return $command_result ? true : false;
    }

    /**
     * @param $product_id
     * @param $product_name
     * @param $product_num
     * @param $store_user
     * @return bool
     * @throws \Exception
     */
    private function editProduct($product_id, $product_name, $product_num, $store_user)
    {
        $store_product_key = self::PRODUCT_CACHE_STORE . $store_user['store_index'];
        $product_key = self::PRODUCT_CACHE_ALL;
        $product_name_key = self::PRODUCT_NAME_CACHE_ALL . $store_user['store_index'];
        $product_store_score_key = self::PRODUCT_CACHE_STORE_SCORE . $store_user['store_index'];
        $product_score_key = self::PRODUCT_CACHE_STORE_ALL_SCORE;
        $this->lock(implode("-", [
            $product_id,
            $store_user['store_index'],
            $store_user['uid']
        ]));
        $new_product = json_encode(["product_name" => $product_name, "product_num" => $product_num, "store_index" => $store_user['store_index'], "uid" => $store_user['uid']], 320);
        $old_product = json_decode($this->db->hGet($product_key, $product_id), true);
        if (!$old_product) {
            throw new \Exception("需要修改的[{$product_name}]不存在...");
        }
        $command_result = $this->multiCommand([], function () use ($old_product, $product_score_key, $product_store_score_key, $new_product, $product_name_key, $product_name, $store_product_key, $product_key, $product_id) {
            $this->db->sRemove($product_name_key, $old_product["product_name"]);
            $this->db->hdel($store_product_key, $product_id);
            $this->db->hdel($product_key, $product_id);
            $this->db->sAdd($product_name_key, $product_name);
            $this->db->hSet($store_product_key, $product_id, $new_product);
            $this->db->hSet($product_key, $product_id, $new_product);
            $this->db->zadd($product_store_score_key, time(), $product_id);
            $this->db->zadd($product_score_key, time(), $product_id);
        });
        $this->unlock(implode("-", [
            $product_id,
            $store_user['store_index'],
            $store_user['uid']
        ]));
        return $command_result ? true : false;
    }

    /**
     * @param $product_name
     * @param $product_num
     * @param $uid
     * @param null $product_id
     * @return bool
     * @throws \Exception
     */
    public function ProductProcess($product_name, $product_num, $uid, $product_id = null)
    {
        $store_user = json_decode($this->db->hGet(LoginModel::LOGIN_STORE_CACHE, $uid), true);
        if (!$store_user) {
            throw new \Exception("没有找到门店信息...");
        }
        if (null == $product_id) {
            return $this->addProduct($product_name, $product_num, $store_user);
        } else {
            return $this->editProduct($product_id, $product_name, $product_num, $store_user);
        }
    }
}