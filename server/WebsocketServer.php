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

    protected $redis;

    protected $ip;

    protected $port;

    public function __construct($child_class, $ip, $port)
    {
        $this->ip = $ip;
        $this->port = $port;
        $this->websocket = new \Swoole\WebSocket\Server($this->ip, $this->port);

        $this->redis = new \Redis();
        $this->redis->connect("127.0.0.1", "6379");

        $this->file_name = str_replace('\\', '_', $child_class);

        $this->websocket->set([
            'task_worker_num' => 1,
            'daemonize' => 1,
            'heartbeat_check_interval' => 5,
            'heartbeat_idle_time' => 10,
            'log_file' => LOG_PATH . '/' . $this->file_name . '.log',
            'pid_file' => PID_PATH . '/' . $this->file_name . '.pid',
        ]);
        //connect success callback
        $this->websocket->on("open", [$this, "open"]);
        $this->websocket->on("message", [$this, "message"]);
        $this->websocket->on("request", [$this, "request"]);
        $this->websocket->on("task", [$this, "task"]);
        $this->websocket->on("finish", [$this, "finish"]);
        $this->websocket->on("close", [$this, "close"]);
        //app start callback
        $this->websocket->on("Start", [$this, "start"]);
        $this->websocket->on("WorkerStart", [$this, "workerStart"]);
    }

    public function startServerBefore($publis_params)
    {
        file_put_contents(LOG_PATH . '/' . $this->file_name . '.run', "is run");
        swoole_timer_tick(1000, function () use ($publis_params) {
            $this->redis->publish("websocket_heart", json_encode($publis_params));
        });

    }

    public function login($all_uid_key, $all_fd_key, $fd_uid_map, $uid_fd_map, $uid, $fd)
    {
        try {
            $this->redis->multi(\Redis::PIPELINE);
            $this->redis->hset($all_uid_key, $this->ip . ":" . $this->port . ":" . $uid, $fd);
            $this->redis->hset($all_fd_key, $this->ip . ":" . $this->port . ":" . $fd, $uid);
            $this->redis->hSet($fd_uid_map . $this->ip . ":" . $this->port, $fd, $uid);
            $this->redis->hSet($uid_fd_map . $this->ip . ":" . $this->port, $uid, $fd);
            $this->redis->exec();
        } catch (\Exception $e) {
            $this->redis->discard();
            $this->websocket->close($fd);
        }
    }

    public function logout($all_uid_key, $all_fd_key, $fd_uid_map, $uid_fd_map, $fd)
    {
        $uid = $this->redis->hget($all_fd_key, $this->ip . ":" . $this->port . ":" . $fd);
        echo <<<HTML
logout key {$all_fd_key}
row key $this->ip:$this->port:$fd;
HTML;
        try {
            $this->redis->multi(\Redis::PIPELINE);
            $this->redis->hDel($all_uid_key, $this->ip . ":" . $this->port . ":" . $uid);
            $this->redis->hDel($all_fd_key, $this->ip . ":" . $this->port . ":" . $fd);
            $this->redis->hDel($fd_uid_map . $this->ip . ":" . $this->port, $fd);
            $this->redis->hDel($uid_fd_map . $this->ip . ":" . $this->port, $uid);
            $this->redis->exec();
        } catch (\Exception $e) {
            $this->redis->discard();
            $this->websocket->close($fd);
        }
    }


    public function workerStart($server, $worker_id)
    {
        if (false === $server->taskworker) {
            echo "task start\n";
        } else {
            echo "worker start\n";
        }
    }

    public function request($request, $response)
    {
        include_once VENDOR_PATH.DIRECTORY_SEPARATOR."autoload.php";
        $response->header("Access-Control-Allow-Origin", "*");
        $response->header("Content-Type", "application/json");
        $msg = [
            "msg" => ""
        ];
        if('POST' != $request->server['request_method']) {
            $msg["msg"] = "not allow get method...";
            $response->end(json_encode($msg, 320));
        }
        if(!isset($request->post['action'])) {
            $msg["msg"] = "not found action...";
            $response->end(json_encode($msg, 320));
        }
        $msg = $this->OnRequest($request, $response, $msg);
        $response->end(json_encode($msg, 320));
    }

    public function open($server, $request)
    {
    }

    public function message($sever, $frame)
    {
    }

    public function close($server, $frame_id, $reactorId)
    {
    }

    public function task($server, $task_id, $src_worker_id, $data)
    {
    }

    public function finish($server, $task_id, $data)
    {
    }

    public function run()
    {
        $this->websocket->start();
    }
}
