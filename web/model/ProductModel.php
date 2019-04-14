<?php
/**
 * Created by PhpStorm.
 * User: caijiang
 * Date: 2019/4/14
 * Time: 下午9:17
 */

namespace web\model;

use web\service\LoginService;
use web\service\ProductService;

class ProductModel extends BaseModel
{

    public function editProduct($product_name, $product_num, $store_token)
    {
        $key = ProductService::PRODUCT_CACHE_STORE . $store_token;
        $index_key = ProductService::PRODUCT_INDEX_CACHE_STORE . $store_token;
        $isset = $this->hget($key, $product_name);
        if ($isset) return false;
        $this->incr($index_key);
        $index = $this->get($index_key);
        $status = $this->multiCommand($key, function () use ($key, $product_name, $product_num, $index) {
            $this->hset($key, $index, json_encode(["product_name" => $product_name, "product_num" => $product_num], 320));
            $this->zadd($key, time(), $index);
        });
        return $status ? true : false;
    }


    public function checkStoreAuth($store_token)
    {
        return $this->sIsMember(LoginService::LOGIN_STORE_CACHE, $store_token) ? true : false;
    }


}