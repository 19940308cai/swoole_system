<?php
/**
 * Created by PhpStorm.
 * User: caijiang
 * Date: 2019/4/10
 * Time: 下午7:33
 */

error_reporting(E_ERROR);
define("ROOT_PATH", __DIR__);

require_once ROOT_PATH . DIRECTORY_SEPARATOR . "bootstrap" . DIRECTORY_SEPARATOR . "constant.php";
require_once ROOT_PATH . DIRECTORY_SEPARATOR . "bootstrap" . DIRECTORY_SEPARATOR . "autoload.php";
require_once VENDOR_PATH . DIRECTORY_SEPARATOR . "autoload.php";




$app = new \core\App();
$app->runMvc();