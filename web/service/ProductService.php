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
        if (false === $auth) throw new \Exception("no auth");
        return $mProductModel->ProductProcess($product_name, $product_num, $uid, $product_id);
    }


    public function getProduct($page, $limit, $uid = null)
    {
        $productWarp = [
            "pager" => null,
            "products" => null,
        ];
        if ($page <= 0) $page = 1;
        $mProductModel = new ProductModel();
        if ($uid) {
            $auth = $mProductModel->checkStoreAuth($uid);
            if (false === $auth) throw new \Exception("no auth see store product");
            $store_count = $mProductModel->getStoreProductCount($uid);
            $pager = new Pager((string)$store_count, $page, $limit);
            $pager->getpagelist();
            $products = $mProductModel->getStoreProductSlice($uid, $pager->offset, $pager->limit);
            $productWarp["pager"] = $pager;
            $productWarp["products"] = $products;
        }
        return $productWarp;
    }

}