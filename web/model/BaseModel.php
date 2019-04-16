<?php
/**
 * Created by PhpStorm.
 * User: caijiang
 * Date: 2019/4/11
 * Time: 下午11:42
 */

namespace web\model;


class BaseModel
{

    public $db;

    const ADDR = "127.0.0.1";

    const PORT = "6379";

    const DAY_SECONS = 86400;

    public function __construct()
    {
        $this->db = new \Redis();
        $this->db->connect(self::ADDR, self::PORT);
    }

    /**
     * @param $listen_key
     * @param \Closure $callback
     */
    protected function multiCommand($listen_key, \Closure $callback)
    {
//        $this->db->watch($listen_key);
        try{
            $this->db->multi(\Redis::PIPELINE);
            $callback();
            $exec_result = $this->db->exec();
            return $exec_result;
        }catch (\Exception $e){
            $this->db->discard();
            return false;
        }
    }

    /**
     * @param $name
     * @param $arguments
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
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

        return $this->db->$name(...$arguments);
    }


}