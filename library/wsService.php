<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace waynesun\swoole;

/**
 * Description of wsService
 *
 * @author WayneSun
 */
class wsService extends service {

    public function __construct($config, $isHandShake = false) {
        parent::__construct($config);
        $this->swoole = new \Swoole\Websocket\Server($this->_config['Host'], $this->_config['Port']);
        if ($isHandShake) {
            $this->addListentEvents(["onMessage", "onHandShake"]);
        } else {
            $this->addListentEvents(["onMessage", "onOpen"]);
        }
    }

    /**
     * 当服务器收到来自客户端的数据帧时会回调此函数。
     * @param swoole_websocket_frame $frame 是swoole_websocket_frame对象，包含了客户端发来的数据帧信息
     * @param swoole_server $server Description
     */
    public function onMessage($server, $frame) {
        try {
            $this->_behavior->onMessage($server, $frame);
        } catch (\Exception $ex) {
            $this->logException($ex);
        } catch (\Throwable $ex) {
            $this->logException($ex);
        }
        
    }

    /**
     * 当WebSocket客户端与服务器建立连接并完成握手后会回调此函数。
     * @param Swoole\Websocket\Server $svr
     * @param swoole_http_request $req
     */
    public function onOpen($svr, $req) {
        try {
            echo 'onOPen';
            $this->_behavior->onOpen($svr, $req);
        } catch (\Exception $ex) {
            $this->logException($ex);
        } catch (\Throwable $ex) {
            $this->logException($ex);
        }
    }

    /**
     * WebSocket建立连接后进行握手。WebSocket服务器已经内置了handshake，如果用户希望自己进行握手处理，可以设置onHandShake事件回调函数。
     * @param swoole_http_request $request
     * @param swoole_http_response $response
     */
    public function onHandShake($request, $response) {
        try {
            return $this->_behavior->onHandShake($request, $response);
        } catch (\Exception $ex) {
            $this->logException($ex);
        } catch (\Throwable $ex) {
            $this->logException($ex);
        }
    }

}

