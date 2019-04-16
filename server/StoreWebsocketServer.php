<?php
/**
 * Created by PhpStorm.
 * User: caijiang
 * Date: 2019/4/16
 * Time: 下午6:57
 */
namespace server;

class StoreWebsocketServer extends WebsocketServer{


    public function __construct()
    {
        parent::__construct(self::class, "127.0.0.1", "9093");
    }


    public function start(){
        file_put_contents(LOG_PATH . '/' . $this->file_name . '.run', "is run");
        swoole_timer_tick(1000, function(){
            $redis = new \Redis();
            $redis->connect("127.0.0.1", "6379");
            $redis->publish("websocket_heart", json_encode([
                "address" => "127.0.0.1:9093",
                "type" => "store",
            ]));
        });
        echo 'run ' . __CLASS__ . " success\n";
    }

    public function open()
    {

    }


    public function message()
    {

    }


    public function request()
    {

    }

    public function close()
    {

    }

    public function task()
    {

    }

    public function finish()
    {

    }

    public function run()
    {
        $this->websocket->start();
    }


}