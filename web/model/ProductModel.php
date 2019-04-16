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

    public function editProduct($product_name, $product_num, $store_token, $product_id=null)
    {
        $key = ProductService::PRODUCT_CACHE_STORE . $store_token;
        $index_key = ProductService::PRODUCT_INDEX_CACHE_STORE . $store_token;

        if(!$product_id){
            //不允许重复创建
            $isset = $this->hget($key, $product_name);
            if ($isset) return false;
            $this->incr($index_key);
            $index = $this->get($index_key);
        }else{
            $this->hdel($key, $product_id);
            $this->hdel(ProductService::PRODUCT_CACHE_ALL, $store_token . $product_id);
            $index = $product_id;
        }
        $status = $this->multiCommand($key, function () use ($key, $product_name, $product_num, $index, $store_token) {
            $data = json_encode(["product_name" => $product_name, "product_num" => $product_num], 320);
            //所有商品
            $this->hset(ProductService::PRODUCT_CACHE_ALL, $store_token . (string)$index, $data);
            $this->zadd(ProductService::PRODUCT_INDEX_CACHE_STORE_ALL, time(), $store_token . (string)$index);
            //以门店为单位的商品映射
            $this->hset($key, $index, $data);
            $this->zadd(ProductService::PRODUCT_CACHE_STORE_SCORE . $store_token, time(), $index);
        });
        return $status ? true : false;
    }

    public function getProductCountByStoreToken($store_token)
    {
        $index_key = ProductService::PRODUCT_INDEX_CACHE_STORE . $store_token;
        $count = $this->get($index_key);
        return $count ? $count : 0;
    }

    public function getProductSliceByStoreToken($store_token, $offset, $limit)
    {
        $key = ProductService::PRODUCT_CACHE_STORE_SCORE . $store_token;
        $product_indexs = $this->db->zrange($key, $offset*$limit, $offset*$limit+$limit);
        return $this->db->hMGet(ProductService::PRODUCT_CACHE_STORE . $store_token, $product_indexs);
    }


    public function checkStoreAuth($store_token)
    {
        return $this->sIsMember(LoginService::LOGIN_STORE_CACHE, $store_token) ? true : false;
    }


}