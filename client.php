<?php
require __DIR__ . "/WebSocketClient.php";
$host = '127.0.0.1';
$prot = 9501;
$client = new webSocketClient($host, $prot);
$data = $client->connect();
while (true){
    $user_input = file_get_contents("php://stdin");
    $client->send(trim($user_input));
    $tmp = $client->recv();
    echo $tmp."\n";
}
$client->close();
