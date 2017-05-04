<?php

namespace app;

/**
 * Description of imBehavior
 * 该文件为实现具体行为的的方法，继承自相应service的行为类
 * @author WayneSun
 */
class imBehavior extends \waynesun\swoole\wsBehavior {

	/**
	 * 连接时会触发
	 * @param type $serv
	 * @param type $request
	 */
	public $aClients = [];

	public function onOpen($serv, $request) {
		$fd = $request->fd;
		$this->aClients[] = $fd;
		echo "connect client {$fd}\n";
		$serv->push($fd, '欢迎欢迎');
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
		if (!empty($aTemp)) {
			$msg = json_decode($aTemp[0], true);
		} else {
			$msg = $data;
		}
		print_r(array($msg, $frame->data));
		//$data = functions::htmlspecialchars($data);
		$serv->push($frame->fd, json_encode(array('code' => 200, 'msg' => 'onMessage ')));
		$serv->task('taskcallback', -1, function($serv, $task_id, $data) {
			echo 'task callback';
			var_dump($task_id, $data);
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

	/**
	 * onTask需要在配置中增加task_worker_num配置，否则会报错
	 * @param type $server
	 * @param type $task_id
	 * @param type $from_id
	 * @param type $data
	 */
	public function onTask($server, $task_id, $from_id, $data) {
		//parent::onTask($server, $task_id, $from_id, $data);
		var_dump(array('ontask', $task_id, $from_id, $data));
	}

	/**
	 *  need
	 * @param type $serv
	 * @param type $task_id
	 * @param type $data
	 */
	public function onFinish($serv, $task_id, $data) {
		//parent::onFinish($serv, $task_id, $data);
	}

}
