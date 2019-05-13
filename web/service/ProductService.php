<?php
/**
 * Created by PhpStorm.
 * User: caijiang
 * Date: 2019/4/14
 * Time: 下午9:15
 */

namespace web\service;


use lib\Pager;
use web\model\ProductModel;

class ProductService
{


    public function editProduct($product_name, $product_num, $uid, $product_id = null)
    {
        $mProductModel = new ProductModel();
        $auth = $mProductModel->checkStoreAuth($uid);
        if (false === $auth) {
            throw new \Exception("no auth");
        }
        return $mProductModel->ProductProcess($product_name, $product_num, $uid, $product_id);
    }

    /**
     * @param $product_id
     * @param $store_id
     * @param $uid
     */
    public function buyProduct($product_id, $store_id, $uid){
        $mProductModel = new ProductModel();
        return $mProductModel->buyProduct($product_id, $store_id, $uid);
    }

    public function getAllProduct($page, $limit)
    {
        $productWarp = [
            "pager" => null,
            "products" => null,
        ];
        if ($page <= 0) {
            $page = 1;
        }
        $mProductModel = new ProductModel();
        $store_product_count = $mProductModel->getAllStoreProductCount();
        if((int)$store_product_count > 0 ){
            $pager = new Pager((string)$store_product_count, $page, $limit);
            $pager->getpagelist();
            $products = $mProductModel->getAllStoreProductSlice($pager->offset, $pager->limit);
            $productWarp["pager"] = $pager;
            $productWarp["products"] = $products;
        }
        return $productWarp;

    }


    public function getStoreProduct($page, $limit, $uid = null)
    {
        $productWarp = [
            "pager" => null,
            "products" => null,
        ];
        if ($page <= 0) {
            $page = 1;
        }
        $mProductModel = new ProductModel();
        if ($uid) {
            $auth = $mProductModel->checkStoreAuth($uid);
            if (false === $auth) {
                throw new \Exception("no auth see store product");
            }
            //获取用户信息
            $store_msg = $mProductModel->getStoreUserMessageByUid($uid);
            if (!$store_msg) {
                throw new \Exception("没有找到店员信息");
            }
            $store_count = $mProductModel->getStoreProductCount($store_msg['store_index']);
            $pager = new Pager((string)$store_count, $page, $limit);
            $pager->getpagelist();
            $products = $mProductModel->getStoreProductSlice($store_msg['store_index'], $pager->offset, $pager->limit);
            $productWarp["pager"] = $pager;
            $productWarp["products"] = $products;
        }
        return $productWarp;
    }

}