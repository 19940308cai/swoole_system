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

class ProductService{

    const PRODUCT_CACHE_STORE = "PRODUCT:CACHE:STORE:";

    const PRODUCT_CACHE_STORE_SCORE = "PRODUCT:CACHE:STORE:SCORE:";

    const PRODUCT_INDEX_CACHE_STORE = "PRODUCT:INDEX:CACHE:STORE:";

    const PRODUCT_CACHE_ALL = "PRODUCT:CACHE:STORE:ALL";

    const PRODUCT_INDEX_CACHE_STORE_ALL = "PRODUCT:INDEX:CACHE:STORE_ALL";


    public function editProduct($product_name, $product_num, $store_token, $product_id=null)
    {
        $mProductModel = new ProductModel();
        $auth = $mProductModel->checkStoreAuth($store_token);
        if(false === $auth) throw new \Exception("no auth");
        return $mProductModel->editProduct($product_name, $product_num, $store_token, $product_id);
    }


    public function getProduct($page, $limit, $store_token=null)
    {
        $productWarp = [
            "pager" => null,
            "products" => null,
        ];
        if($page <= 0){
            $page = 1;
        }
        $mProductModel = new ProductModel();
        if($store_token){
            $auth = $mProductModel->checkStoreAuth($store_token);
            if(false === $auth) throw new \Exception("no auth see store product");
            $store_count = $mProductModel->getProductCountByStoreToken($store_token);
            $pager = new Pager((string)$store_count, $page, $limit);
            $pager->getpagelist();
            $products = $mProductModel->getProductSliceByStoreToken($store_token, $pager->offset, $pager->limit);
            $productWarp["pager"] = $pager;
            $productWarp["products"] = $products;
        }
        return $productWarp;
    }

}