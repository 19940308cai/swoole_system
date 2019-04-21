<?php
/**
 * Created by PhpStorm.
 * User: caijiang
 * Date: 2019/4/16
 * Time: 下午6:57
 */

namespace server;


class CustomerWebsocketServer extends WebsocketServer
{

    const IP = "127.0.0.1";

    const PORT = "9092";


    public function __construct()
    {
        parent::__construct(self::class, self::IP, self::PORT);
    }


    public function start($server)
    {
        parent::startServerBefore([
            "address" => self::IP.self::PORT,
            "type" => "store",
        ]);
        echo 'run ' . __CLASS__ . " success\n";
    }

    public function open($server, $request)
    {
        $data = $this->websocket->connection_info($request);
        var_dump($data);
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

}
