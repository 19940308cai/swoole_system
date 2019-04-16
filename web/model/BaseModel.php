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

    public $db;

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
//        foreach ($listen_keys as $listen) {
//            $this->db->watch($listen);
//        }
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
     * 检查门店用户是否合法
     * @param $uid
     * @return bool
     */
    public function checkStoreAuth($uid)
    {
        return $this->db->sIsMember(LoginService::LOGIN_STORE_CACHE, $uid);
    }

    /**
     * 获取门店产品总数
     * @param $uid
     * @return int
     */
    public function getStoreProductCount($uid)
    {
        return $this->db->hLen(ProductModel::PRODUCT_CACHE_STORE . $uid);
    }

    /**
     * 获取门店产品 - 分页
     * @param $uid
     * @param $offset
     * @param $limit
     * @return array
     */
    public function getStoreProductSlice($uid, $offset, $limit)
    {
        $product_store_key = ProductModel::PRODUCT_CACHE_STORE_SCORE . $uid;
        $product_indexs = $this->db->zrange($product_store_key, $offset * $limit, $offset * $limit + $limit);
        return $this->db->hMGet(ProductModel::PRODUCT_CACHE_STORE . $uid, $product_indexs);
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