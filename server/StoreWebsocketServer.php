<?php
/**
 * Created by PhpStorm.
 * User: caijiang
 * Date: 2019/4/16
 * Time: 下午6:57
 */

namespace server;

use Swoole\Server;

class StoreWebsocketServer extends WebsocketServer
{

    const IP = "127.0.0.1";

    const PORT = "9093";


    public function __construct()
    {
        parent::__construct(self::class, self::IP, self::PORT);
    }


    public function start($server)
    {
        parent::startServerBefore([
            "address" => self::IP,
            "port" => self::PORT,
            "type" => "store",
        ]);
        echo 'run ' . __CLASS__ . " success\n";
    }

    public function open($server, $request)
    {
        $connection_info = $this->websocket->connection_info($request->fd);
        if (WEBSOCKET_STATUS_FRAME == $connection_info['websocket_status']) {
            echo "websocket连接";
            $this->login(ALL_STORE_UID, ALL_STORE_FD, STORE_FD_UID_MAP, STORE_UID_FD_MAP, $request->get['uid'], $request->fd);
        } else {
            echo "其它连接";
        }
    }

    public function close($server, $frame_id, $reactorId)
    {
        $connection_info = $this->websocket->connection_info($frame_id);
        if (WEBSOCKET_STATUS_FRAME == $connection_info['websocket_status']) {
            echo "websocket清理";
            $this->logout(ALL_STORE_UID, ALL_STORE_FD,STORE_FD_UID_MAP, STORE_UID_FD_MAP, $frame_id);
        } else {
            echo "其它关闭";
        }
    }

    public function message($sever, $frame)
    {
        if ("ping" == $frame->data) {
            $this->websocket->push($frame->fd, "pong");
        }

    }




    public function task($server, $task_id, $src_worker_id, $data)
    {
    }

    public function finish($server, $task_id, $data)
    {
    }
}