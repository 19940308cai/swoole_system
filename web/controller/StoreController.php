<?php
/**
 * Created by PhpStorm.
 * User: caijiang
 * Date: 2019/5/14
 * Time: 下午11:35
 */
namespace web\controller;

use web\service\StoreService;

class StoreController extends BaseController
{

    public function getStoreProvider()
    {
        $store_id = $this->request->get('store_id');
        $sStoreService = new StoreService();
        $store_msg = $sStoreService->getStoreProvider($store_id);
        return $this->response->json(200, self::$success, $store_msg);
    }




}