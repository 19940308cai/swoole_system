<?php
/**
 * Created by PhpStorm.
 * User: caijiang
 * Date: 2019/5/14
 * Time: 下午11:01
 */

namespace web\controller;

use web\service\UserProductService;

class UserProductController extends BaseController
{

    public function getUserProducts()
    {
        $uid = $this->request->get("uid");
        $sUserProduct = new UserProductService();
        $userProducts = $sUserProduct->getUserProducts($uid);
        return $this->response->json(500, self::$success, $userProducts);
    }


}