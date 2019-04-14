<?php
/**
 * Created by PhpStorm.
 * User: caijiang
 * Date: 2019/4/14
 * Time: 下午9:15
 */
namespace web\service;


use web\model\ProductModel;

class ProductService{

    const PRODUCT_CACHE_STORE = "PRODUCT:CACHE:STORE:";

    const PRODUCT_INDEX_CACHE_STORE = "PRODUCT:INDEX:CACHE:STORE:";


    public function editProduct($product_name, $product_num, $store_token)
    {
        $mProductModel = new ProductModel();
        $auth = $mProductModel->checkStoreAuth($store_token);
        if(false === $auth) throw new \Exception("no auth");
        return $mProductModel->editProduct($product_name, $product_num, $store_token);
    }

}