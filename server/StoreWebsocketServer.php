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
        if ($connection_info['websocket_status']) {
            $this->login(
                ALL_STORE_UID,
                ALL_STORE_FD,
                STORE_FD_UID_MAP,
                STORE_UID_FD_MAP,
                $request->get['uid'],
                $request->fd
            );
        } else {
            echo "其它连接";
        }
    }

    public function close($server, $frame_id, $reactorId)
    {
        $connection_info = $this->websocket->connection_info($frame_id);
        if (WEBSOCKET_STATUS_FRAME == $connection_info['websocket_status']) {
            $this->logout(
                ALL_STORE_UID,
                ALL_STORE_FD,
                STORE_FD_UID_MAP,
                STORE_UID_FD_MAP,
                $frame_id
                );
        } else {
            echo "其它关闭";
        }
    }

    public function OnRequest($request, $response, $msg)
    {
        switch ($request->post['action']){
            case RECEIVE_MESSAGE_ACTION:
                $msg = $this->receive_action_handle($request);
                break;
            default:
                $msg["msg"] = "miss";
        }
        return $msg;
    }

    /**
     * 接收消息
     * @param $request
     */
    public function receive_action_handle($request)
    {
        $msg = [
            "msg" => "客服没有在线，消息放到客服信箱中..."
        ];
        //以用户为颗粒度生成用户信箱.用户登录之后立马生成信箱
        $push_message_warp_func = function($redis_conn, $request){
            $redis_conn->rPush(MESSAGE_WARP.$request->post['to_user_id'], json_encode([
                "from_user_id" => $request->post['from_user_id'],
                "to_user_id" => $request->post['to_user_id'],
                "message" => $request->post['message'],
            ], 320));
        };
        if( !isset($request->post['from_user_id']) || !isset($request->post['to_user_id']) || !isset($request->post['message']) ){
            $msg["msg"] = "params error";
            return $msg;
        }
        echo "receive_body:".var_export($request->post, true)."\r\n";
        $redis_conn = new \Redis();
        $redis_conn->connect("127.0.0.1");
        //定位门店客服
        $to_user_fd = $redis_conn->hGet(STORE_UID_FD_MAP . $this->ip . ":" . $this->port, $request->post['to_user_id']);
        echo "store_cache_key :".var_export(STORE_UID_FD_MAP . $this->ip . ":" . $this->port, true)."\r\n";
        echo "store_provider data:".var_export($to_user_fd, true)."\r\n";
        if(!$to_user_fd){
            $push_message_warp_func($redis_conn, $request);
            return $msg;
        }else{
            $this->websocket->task([
                "from_user_id" => $request->post['from_user_id'],
                "to_user_id" => $request->post['to_user_id'],
                "to_user_fd" => $to_user_fd,
                "message" => $request->post['message'],
            ]);
            $msg = "发送消息给客服成功...";
            return $msg;
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
        $this->websocket->push($data['to_user_fd'], $data['message']);
        return "success";
    }

    public function finish($server, $task_id, $data)
    {
    }
}