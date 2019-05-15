<?php
/**
 * Created by PhpStorm.
 * User: caijiang
 * Date: 2019/5/14
 * Time: 下午11:37
 */
namespace web\service;

use web\model\LoginModel;

class StoreService
{


    public function getStoreProvider($store_id)
    {
        $mLogin = new LoginModel();
        return $mLogin->getStoreProvider($store_id);
    }

}