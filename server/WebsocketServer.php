<?php
/**
 * Created by PhpStorm.
 * User: caijiang
 * Date: 2019/4/9
 * Time: 下午11:54
 */

namespace server;

class WebsocketServer
{

    private $websocket;

    private $file_name;

    public function __construct()
    {
        $this->websocket = new \Swoole\WebSocket\Server("0.0.0.0", "9093");

        $this->file_name = str_replace('\\', '_', self::class);

        $this->websocket->set([
            'task_worker_num' => 1,
//            'daemonize' => 1,
            'log_file' => LOG_PATH . '/' . $this->file_name . '.log',
            'pid_file' => PID_PATH . '/' . $this->file_name . '.pid',
        ]);
        $this->websocket->on("open", [$this, "open"]);
        $this->websocket->on("message", [$this, "message"]);
        $this->websocket->on("request", [$this, "request"]);
        $this->websocket->on("task", [$this, "task"]);
        $this->websocket->on("finish", [$this, "finish"]);
        $this->websocket->on("close", [$this, "close"]);
        $this->websocket->on("Start", [$this, "start"]);
    }

    public function start()
    {
        file_put_contents(LOG_PATH . '/' . $this->file_name . '.run', "1");
        swoole_timer_tick(1000, function(){
            $redis = new \Redis();
            $redis->connect("127.0.0.1", "6379");
            $redis->publish("nodePool", json_encode(["address" => "127.0.0.1:9093"]));
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