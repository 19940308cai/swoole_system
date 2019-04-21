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


//所有在线人员: 机器号:uid - fd映射
define("ALL_STORE_UID", "ALL_STORE_UID");
//所有在线人员: 机器号:fd - uid映射
define("ALL_STORE_FD", "ALL_STORE_FD");
//以门店为纬度: 门店fd - uid映射
define("STORE_FD_UID_MAP", "STORE_FD_UID_MAP:");
//以门店为纬度: 门店uid - fd映射
define("STORE_UID_FD_MAP", "STORE_UID_FD_MAP:");



