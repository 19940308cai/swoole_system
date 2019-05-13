<?php
/**
 * Created by PhpStorm.
 * User: caijiang
 * Date: 2019/4/11
 * Time: 下午11:42
 */

namespace web\model;


use web\service\LoginService;
use web\service\ProductService;

class BaseModel
{

    protected $db;

    const ADDR = "127.0.0.1";

    const PORT = "6379";

    const DAY_SECONS = 86400;

    const MONTH_SECONS = self::DAY_SECONS * 30;

    const MIN_SECONS = 60;

    public function __construct()
    {
        $this->db = new \Redis();
        $this->db->connect(self::ADDR, self::PORT);
    }

    /**
     * @param $listen_key
     * @param \Closure $callback
     */
    protected function multiCommand($listen_keys=[], \Closure $callback)
    {
        foreach ($listen_keys as $listen) {
            $this->db->watch($listen);
        }
        try {
            $this->db->multi(\Redis::PIPELINE);
            $callback();
            $exec_result = $this->db->exec();
            return $exec_result;
        } catch (\Exception $e) {
            $this->db->discard();
            return false;
        }
    }

    /**
     * 检查门店用户权限
     * @param $uid
     * @return bool
     */
    public function checkStoreAuth($uid)
    {
        return $this->db->hExists(LoginModel::LOGIN_STORE_CACHE, $uid);
    }

    /**
     * 获取门店员工信息
     * @param $uid
     * @return string
     */
    public function getStoreUserMessageByUid($uid)
    {
        return json_decode($this->db->hGet(LoginModel::LOGIN_STORE_CACHE, $uid), true);
    }

    /**
     * 获取门店产品总数
     * @param $store_id
     * @return int
     */
    public function getStoreProductCount($store_id)
    {
        return $this->db->hLen(ProductModel::PRODUCT_CACHE_STORE . $store_id);
    }

    /**
     * 获取所有门店产品总数
     * @return int
     */
    public function getAllStoreProductCount()
    {
        return $this->db->hLen(ProductModel::PRODUCT_CACHE_ALL);
    }

    /**
     * 获取门店产品 - 分页
     * @param $store_id
     * @param $offset
     * @param $limit
     * @return array
     */
    public function getStoreProductSlice($store_id, $offset, $limit)
    {
        $product_store_key = ProductModel::PRODUCT_CACHE_STORE_SCORE . $store_id;
        $product_indexs = $this->db->zrange($product_store_key, $offset * $limit, $offset * $limit + $limit);
        return $this->db->hMGet(ProductModel::PRODUCT_CACHE_STORE . $store_id, $product_indexs);
    }

    /**
     * 从所有的门店产品中筛选出来东西
     * @param $offset
     * @param $limit
     */
    public function getAllStoreProductSlice($offset, $limit)
    {
        $product_store_key = ProductModel::PRODUCT_CACHE_STORE_ALL_SCORE;
        $product_indexs = $this->db->zrange($product_store_key, $offset * $limit, $offset * $limit + $limit);
        return $this->db->hMGet(ProductModel::PRODUCT_CACHE_ALL, $product_indexs);
    }


    /**
     * @param $name
     * @param $arguments
     * @throws \Exception
     */
//    public function __call($name, $arguments)
//    {
//        $methoder = new \ReflectionMethod($this->db, "set");
//        $parameters = $methoder->getParameters();
//        if (count($parameters) != count($arguments)) {
//            $not_found_arguments_text = "";
//            foreach ($parameters as $k => $v) {
//                if (false == $v->isDefaultValueAvailable()) {
//                    if(!isset($arguments[$k])){
//                        $not_found_arguments_text .= " {$k}:{$v->getName()} ";
//                    }
//                }
//            }
//            throw new \Exception("arguments length error, message is :" . $not_found_arguments_text);
//        }

//        return $this->db->$name(...$arguments);
//    }


}