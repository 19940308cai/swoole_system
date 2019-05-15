<?php
/**
 * Created by PhpStorm.
 * User: caijiang
 * Date: 2019/5/14
 * Time: 下午11:04
 */

namespace web\model;

class UserProductModel extends BaseModel
{

    public function getUserProducts($uid)
    {
        $mProductModel = new ProductModel();
        $user_store_product_key = UserModel::PRODUCT_CACHE_USER_STORE . $uid;
        $user_products = $this->db->hGetAll($user_store_product_key);
        if(!$user_products){
            return [];
        }else{
            $tmp = [];
            foreach ($user_products as $product_id => $store_id){
                $product = json_decode($this->db->hGet(ProductModel::PRODUCT_CACHE_ALL, $product_id), true);
                array_push($tmp, $product);
            }
            return $tmp;
        }
    }

}