<?php
/**
 * Created by PhpStorm.
 * User: caijiang
 * Date: 2019/4/10
 * Time: 上午12:09
 */
define("BOOTSTRAP_PATH", ROOT_PATH . DIRECTORY_SEPARATOR . "bootstrap");
define("CORE_PATH", ROOT_PATH . DIRECTORY_SEPARATOR . "core");
define("LIB_PATH", ROOT_PATH . DIRECTORY_SEPARATOR . "lib");
define("STATIC_PATH", ROOT_PATH . DIRECTORY_SEPARATOR . "static");
define("VIEW_PATH", ROOT_PATH . DIRECTORY_SEPARATOR . "view");
define("SERVER_PATH", ROOT_PATH . DIRECTORY_SEPARATOR . "server");
define("PID_PATH", ROOT_PATH . DIRECTORY_SEPARATOR . "pid");
define("LOG_PATH", ROOT_PATH . DIRECTORY_SEPARATOR . "log");
define("VENDOR_PATH", ROOT_PATH . DIRECTORY_SEPARATOR . "vendor");


define("START", "start");
define("STOP", "stop");
define("RELOAD", "reload");

//信箱
define("MESSAGE_WARP", "MESSAGE_WARP");
//发送信息动作
define("SEND_MESSAGE_ACTION", "send");
//收信息动作
define("RECEIVE_MESSAGE_ACTION", "receive");


//所有在线门店客服: 机器号:uid - fd映射
define("ALL_STORE_UID", "ALL_STORE_UID");
//所有在线门店客服: 机器号:fd - uid映射
define("ALL_STORE_FD", "ALL_STORE_FD");
//门店:机器号: 门店客服fd - uid映射
define("STORE_FD_UID_MAP", "STORE_FD_UID_MAP:");
//门店:机器号: 门店客服uid - fd映射
define("STORE_UID_FD_MAP", "STORE_UID_FD_MAP:");

//所有在线客户: 机器号:uid - fd映射
define("ALL_USER_UID", "ALL_USER_UID");
//所有在线客户: 机器号:fd - uid映射
define("ALL_USER_FD", "ALL_USER_FD");
//用户:机器号: 用户fd - uid映射
define("USER_FD_UID_MAP", "USER_FD_UID_MAP:");
//用户:机器号: 用户uid - fd映射
define("USER_UID_FD_MAP", "USER_UID_FD_MAP:");





