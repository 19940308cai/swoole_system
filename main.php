<?php
/**
 * Created by PhpStorm.
 * User: caijiang
 * Date: 2019/4/10
 * Time: 上午12:02
 */

define("ROOT_PATH", __DIR__);
require_once ROOT_PATH . DIRECTORY_SEPARATOR . "bootstrap" . DIRECTORY_SEPARATOR . "constant.php";
require_once ROOT_PATH . DIRECTORY_SEPARATOR . "bootstrap" . DIRECTORY_SEPARATOR . "autoload.php";

$command = ["h:", "s:", "a:"];
$action_options = [START, STOP, RELOAD];
$command_params = getopt(implode("", $command));


$allow_run_server = [
    "websocket" => \server\WebsocketServer::class,
];

$php_bin = exec("which php");

$help_text = <<<HELP
command items 
h - 帮助命令
s - 服务器名称
a - 服务器动作

command options
h:
    1.all
s:
    1.websocket
    .....
a:
    1.start
    2.stop
    3.reload

HELP;

if (count($command_params) == 0 || isset($command_params['h'])) {
    die($help_text);
}

if (isset($command_params['s'])) {
    if (!in_array($command_params['a'], $action_options)) {
        die($help_text);
    }
    if (!array_key_exists($command_params['s'], $allow_run_server)) {
        die("{$command_params['s']} is not allow server...\n");
    }
    if ($command_params['a'] == START) {
        start_server($command_params['s'], $allow_run_server[$command_params['s']]);
        $process = new swoole_process(function () use ($allow_run_server, $command_params) {
            require_once ROOT_PATH . DIRECTORY_SEPARATOR . "bootstrap" . DIRECTORY_SEPARATOR . "autoload.php";
            $server = new $allow_run_server[$command_params['s']]();
            $server->run();
        }, true);
        $process->start();
        //echo $process->read();
        swoole_process::wait();
        while (true) {
            if (file_exists(LOG_PATH . '/' . str_replace('\\', '_', $allow_run_server[$command_params['s']]) . '.run')) {
                die("run {$command_params['s']} success...\n");
            }
        }
    }
    if ($command_params['a'] == STOP) {
        die(stop_server($command_params['s'], $allow_run_server[$command_params['s']], $allow_run_server));
    }
    if ($command_params['a'] == RELOAD) {
        die(reload_server($command_params['s'], $allow_run_server[$command_params['s']]));
    }
}

function see_pid($file_name)
{
    return exec("cat " . PID_PATH . DIRECTORY_SEPARATOR . $file_name . ".pid 2>/dev/null");
}

function reload_server($alisa_server_name, $server_name)
{
    echo "reload {$alisa_server_name} ing...\n";
    $pid = see_pid($server_name);
    if ($pid) {
        exec("kill -USR1 {$pid} 2>/dev/null");
        echo "reload {$alisa_server_name} success\n";
    } else {
        echo "not found {$alisa_server_name} pid...\n";
        echo "reload {$alisa_server_name} error...\n";
    }
}


function stop_server($alisa_server_name, $server_name, $allow_run_server)
{
    echo "stop {$alisa_server_name} ing...\n";
    $file_name = str_replace('\\', '_', $allow_run_server[$alisa_server_name]);
    $pid = see_pid($file_name);
    if ($pid) {
        exec("kill -TERM {$pid} 2>/dev/null");
        sleep(1);
        if (file_exists(LOG_PATH . '/' . $file_name . '.run')) {
            @unlink(LOG_PATH . '/' . $file_name . '.run');
        }
        if (file_exists(LOG_PATH . '/' . $file_name . '.log')) {
            @unlink(LOG_PATH . '/' . $file_name . '.log');
        }
        $process_isset = exec("ps -ef | grep {$pid} | grep -v grep");
        if ($process_isset) {
            echo "stop {$alisa_server_name} error...\n";
        } else {
            echo "stop {$alisa_server_name} success...\n";
        }
    } else {
        echo "not found {$alisa_server_name} pid...\n";
        echo "stop {$alisa_server_name} error...\n";
    }
}

function start_server($alisa_server_name, $server_name)
{
    echo "run {$alisa_server_name} ing...\n";
    //check process is runing
    $pid = see_pid($server_name);
    if ($pid) {
        echo "server {$alisa_server_name} don't run agin...\n";
        die("run {$alisa_server_name} error...\n");
    }
}



