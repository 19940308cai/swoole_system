<?php
/**
 * Created by PhpStorm.
 * User: caijiang
 * Date: 2019/5/14
 * Time: 下午11:02
 */

namespace web\service;


use web\model\UserProductModel;

class UserProductService
{

    public function getUserProducts($uid)
    {
        $mUserProductModel = new UserProductModel();
        $auth = $mUserProductModel->checkCustomerAuth($uid);
        if (false === $auth) {
            throw new \Exception("no auth");
        }
        return $mUserProductModel->getUserProducts($uid);
    }

}