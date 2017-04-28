<?php
namespace app;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of imBehavior
 *
 * @author WayneSun
 */
class imBehavior extends \library\wsBehavior {
    /**
     * 连接时会触发
     * @param type $serv
     * @param type $request
     */
    public $aClients = [];
    public function onOpen($serv, $request) {
        $fd=$request->fd;   
        $this->aClients[]=$fd;
        echo "connect client {$fd}\n";
        $serv->push($fd,'欢迎欢迎');
    }

    /**
     * 接收客户端消息
     * @param type $serv
     * @param type $frame
     */
    public function onMessage($serv, $frame) {
        $data = $frame->data;
        $json_str = str_replace('＼＼', '', $data);
        $aTemp = array();
        preg_match('/{.*}/', $json_str, $aTemp);
        if(!empty($aTemp)){
            $msg = json_decode($aTemp[0],true);
        }else{
            $msg = $data;
        }
        print_r(array($msg,$frame->data));
        //$data = functions::htmlspecialchars($data);
        $serv->push($frame->fd, json_encode(array('code' => 200, 'msg' => 'onMessage ')));   
        $serv->task('taskcallback',-1,function($serv,$task_id,$data){
            echo 'task callback';
            var_dump($task_id,$data);
        });
    }

    /**
     * 客户端关闭
     * @param type $serv
     * @param type $fd
     * @param type $from_id
     */
    public function onClose($serv, $fd, $from_id) {
        print_r($this->aClients);
        echo "{$fd}--{$from_id} closed\n";
    }

    /**
     * Work/Task进程启动
     * @global type $config
     * @param type $serv
     * @param type $worker_id
     */
    public function onWorkerStart($serv, $worker_id) {
//        include dirname(__FILE__) . '/common.php';
//        common::initService($worker_id, $serv);
    }
    
    public function onTask($server, $task_id, $from_id, $data) {
        //parent::onTask($server, $task_id, $from_id, $data);
        var_dump(array('ontask',$task_id,$from_id,$data));
    }
    
    public function onFinish($serv, $task_id, $data) {
        //parent::onFinish($serv, $task_id, $data);
    }

}
