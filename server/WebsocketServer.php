<?php
/**
 * Created by PhpStorm.
 * User: caijiang
 * Date: 2019/4/9
 * Time: 下午11:54
 */

namespace server;

abstract class WebsocketServer
{

    protected $websocket;

    protected $file_name;

    public function __construct($child_class, $ip, $port)
    {
        $this->websocket = new \Swoole\WebSocket\Server($ip, $port);

        $this->file_name = str_replace('\\', '_', $child_class);

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
    }
}
