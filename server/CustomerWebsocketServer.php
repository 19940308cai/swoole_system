<?php
/**
 * Created by PhpStorm.
 * User: caijiang
 * Date: 2019/4/16
 * Time: 下午6:57
 */

namespace server;

use GuzzleHttp\Client;
use Swoole\Http\Request;

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
            "address" => self::IP,
            "port" => self::PORT,
            "type" => "customer",
        ]);
        echo 'run ' . __CLASS__ . " success\n";
    }

    public function open($server, $request)
    {
        if ('customer' != $request->get['user_type']) {
            $server->close($request->fd);
        } else {
            $connect_info = $this->websocket->connection_info($request->fd);
            echo "建立了链接....\r\n";
            if ($connect_info['websocket_status']) {
                $this->login(
                    ALL_USER_UID,
                    ALL_USER_FD,
                    USER_UID_FD_MAP,
                    USER_FD_UID_MAP,
                    $request->get['uid'],
                    $request->fd
                );
            }
        }
    }

    public function OnRequest($request, $response, $msg)
    {
        switch ($request->post['action']){
            case SEND_MESSAGE_ACTION:
                $msg = $this->send_action_handle($request);
                break;
            default:
                $msg["msg"] = "miss";
        }
        return $msg;
    }

    /**
     * 发送消息
     * @param $request
     * @return array
     */
    private function send_action_handle($request)
    {
        $msg = [
            "msg" => "消息已经存放在信箱"
        ];
        //以用户为颗粒度生成用户信箱.用户登录之后立马生成信箱
        $push_message_warp_func = function($redis_conn, $request){
            $redis_conn->rPush(MESSAGE_WARP.$request->post['to_user_id'], json_encode([
                "from_user_id" => $request->post['from_user_id'],
                "to_user_id" => $request->post['to_user_id'],
                "message" => $request->post['message'],
            ], 320));
        };
        $redis_conn = new \Redis();
        $redis_conn->connect("127.0.0.1");
        //获取所有的门店客服 - value为门店客服的uid
        $store_providers = $redis_conn->hGetAll(ALL_STORE_FD);
        var_dump($store_providers);
        if (!$store_providers) {
            $push_message_warp_func($redis_conn, $request);
            return $msg;
        } else {
            $msg_warp = [];
            foreach ($store_providers as $key => $store_provider_uid) {
                if ($request->post['to_user_id'] == $store_provider_uid) {
                    $tmp_array = explode(":", $key);
                    $msg_warp["action"] = RECEIVE_MESSAGE_ACTION;
                    $msg_warp["from_user_id"] = $request->post['from_user_id'];
                    $msg_warp["to_user_id"] = $request->post['to_user_id'];
                    $msg_warp["message"] = $request->post['message'];
                    $msg_warp["host"] = $tmp_array[0];
                    $msg_warp["port"] = $tmp_array[1];
                    $msg_warp["fd"] = $tmp_array[2];
                    break;
                }
            }
            if (!$msg_warp) {
                $push_message_warp_func($redis_conn, $request);
                return $msg;
            } else {
                $http_client = new Client();
                $http_client->post("http://".implode(":", [$msg_warp['host'], $msg_warp['port']]), ["form_params" => $msg_warp]);
                $msg["msg"] = "send message success...";
                return $msg;
            }
        }
    }

    public function message($sever, $frame)
    {
        if ("ping" == $frame->data) {
            $this->websocket->push($frame->fd, "pong");
            echo "【{$frame->fd}】>>> ping\r\n";
            echo "【{$frame->fd}】<<< pong\r\n";
        }
    }

    public function close($server, $frame_id, $reactorId)
    {
        $connection_info = $this->websocket->connection_info($frame_id);
        if ($connection_info['websocket_status']) {
            $this->logout(
                ALL_USER_UID,
                ALL_USER_FD,
                USER_UID_FD_MAP,
                USER_FD_UID_MAP,
                $frame_id
            );
        } else {
            echo "其它关闭\r\n";
        }
    }


    public function task($server, $task_id, $src_worker_id, $data)
    {
    }

    public function finish($server, $task_id, $data)
    {

    }

}
