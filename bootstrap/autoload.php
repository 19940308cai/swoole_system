<?php
/**
 * Created by PhpStorm.
 * User: caijiang
 * Date: 2019/4/12
 * Time: 下午3:53
 */

spl_autoload_register(function ($class_name) {
    $file_path = str_replace('\\', '/', $class_name) . ".php";
    if (file_exists($file_path)) {
        require_once $file_path;
    }
});