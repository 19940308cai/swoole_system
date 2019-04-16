<?php
/**
 * Created by PhpStorm.
 * User: caijiang
 * Date: 2019/4/16
 * Time: 下午6:59
 */
namespace server;


class WebsocketFactory{


    public function WebSocketFactory($role)
    {
        switch ($role){
            case "c":
                return CustomerWebsocketServer::class;
                break;
            case "p":
                return ProviderWebsocketServer::class;
                break;
            case "s":
                return StoreWebsocketServer::class;
                break;
        }
        return null;
    }


}