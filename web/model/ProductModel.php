<?php
/**
 * Created by PhpStorm.
 * User: caijiang
 * Date: 2019/4/14
 * Time: 下午9:17
 */

namespace web\model;

use web\service\LoginService;
use web\service\self;

class ProductModel extends BaseModel
{
    //门店 - 产品
    const PRODUCT_CACHE_STORE = "PRODUCT:CACHE:STORE:";

    //门店 - 产品 - 权重
    const PRODUCT_CACHE_STORE_SCORE = "PRODUCT:CACHE:STORE:SCORE:";

    //产品primary key
    const PRODUCT_INDEX_CACHE = "PRODUCT:INDEX:CACHE";

    //产品名称 key
    const PRODUCT_NAME_CACHE_ALL = "PRODUCT_NAME:CACHE:ALL";

    //所有门店 - 产品
    const PRODUCT_CACHE_ALL = "PRODUCT:CACHE:STORE:ALL";

    //所有门店 - 产品 - 权重
    const PRODUCT_CACHE_STORE_ALL_SCORE = "PRODUCT:CACHE:STORE_ALL:SCORE";


    /**
     * @param $product_name
     * @param $product_num
     * @param $uid
     * @return bool
     * @throws \Exception
     */
    private function addProduct($product_name, $product_num, $uid)
    {
        $store_product_key = self::PRODUCT_CACHE_STORE . $uid;
        $product_key = self::PRODUCT_CACHE_ALL;
        $product_name_key = self::PRODUCT_NAME_CACHE_ALL;
        $product_primary_key = self::PRODUCT_INDEX_CACHE;
        $product_store_score_key = self::PRODUCT_CACHE_STORE_SCORE . $uid;
        $product_score_key = self::PRODUCT_CACHE_STORE_ALL_SCORE;
        if ($this->db->sIsMember($product_name_key, $product_name)) {
            throw new \Exception("不允许重复提交...");
        }
        $product = json_encode(["product_name" => $product_name, "product_num" => $product_num], 320);
        $primary_key = $this->db->incr($product_primary_key);
        $command_result = $this->multiCommand([
            $store_product_key,
            $product_key,
            $product_name_key
        ], function () use ($product_store_score_key, $product_score_key, $product_name_key, $product_name, $primary_key, $product_key, $store_product_key, $product) {
            $this->db->zadd($product_store_score_key, time(), $product_key);
            $this->db->zadd($product_score_key, time(), $product_key);
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
     * @param $uid
     * @return bool
     * @throws \Exception
     */
    private function editProduct($product_id, $product_name, $product_num, $uid)
    {
        $store_product_key = self::PRODUCT_CACHE_STORE . $uid;
        $product_key = self::PRODUCT_CACHE_ALL;
        $product_name_key = self::PRODUCT_NAME_CACHE_ALL;
        $product_store_score_key = self::PRODUCT_CACHE_STORE_SCORE . $uid;
        $product_score_key = self::PRODUCT_CACHE_STORE_ALL_SCORE;
        $new_product = json_encode(["product_name" => $product_name, "product_num" => $product_num], 320);
        $old_product = json_decode($this->db->hGet($product_key, $product_id), true);
        if (!$old_product) {
            throw new \Exception("需要修改的[{$product_name}]不存在...");
        }
        $command_result = $this->multiCommand([], function () use ($product_score_key, $product_store_score_key, $new_product, $product_name_key, $product_name, $store_product_key, $product_key, $product_id) {
            $this->db->sRemove($product_name_key, $product_name);
            $this->db->hdel($store_product_key, $product_id);
            $this->db->hdel($product_key, $product_id);
            $this->db->sAdd($product_name_key, $product_name);
            $this->db->hSet($store_product_key, $product_id, $new_product);
            $this->db->hSet($product_key, $product_id, $new_product);
            $this->db->zadd($product_store_score_key, time(), $product_key);
            $this->db->zadd($product_score_key, time(), $product_key);
        });
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
        if (null == $product_id) {
            return $this->addProduct($product_name, $product_num, $uid);
        } else {
            return $this->editProduct($product_id, $product_name, $product_num, $uid);
        }
    }
}