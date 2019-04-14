<?php
$server = new swoole_websocket_server("127.0.0.1", 9095);
$server->set([
    'task_worker_num' => 1,
]);

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
define("FPID_KEY", "ZAIXIAN_PID");

$server->on('open', function (swoole_websocket_server $_server, $request) use ($redis) {
    echo "server#{$_server->worker_pid}: handshake success with fd#{$request->fd}\n";
    $redis->hMset(FPID_KEY, [$request->fd => $request->fd]);
});

$server->on('request', function($request, $response) {
    global $server;//调用外部的server
    foreach ($server->connections as $fd) {
        if( in_array( $server->connection_info($fd)['websocket_status'] , [1,2,3]) ){
            $server->push($fd, "pong");
        }else{
            $response->end("success");
        }
    }
});

$server->on('message', function (swoole_websocket_server $_server, $frame) use ($redis) {
    echo "received " . strlen($frame->data) . " bytes\n";
    $_server->task($frame);
});

$server->on('task', function (swoole_websocket_server $_server, $task_id, $src_worker_id, $frame) use ($redis) {
    //获取所有人
    $harray = $redis->hGetAll(FPID_KEY);
    $from = $frame->fd;
    $text = $frame->data;
    $to = null;
    foreach ($harray as $k => $v) {
        if ($v != $from) {
            $_server->push($v, "{$from}说 : {$text}");
        } else {
            $_server->push($v, "自己说 : {$text}");
        }
    }
    return "task end";
});

$server->on('Finish', function (swoole_websocket_server $serv, $task_id, $data) {
    echo "finish: " . $data;
});

$server->on('close', function ($_server, $fd) use ($redis) {
    echo "client {$fd} closed\n";
    $redis->hDel(FPID_KEY, $fd);
});


$server->start();
