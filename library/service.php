<?php

namespace waynesun\swoole;

/**
 * Description of service
 * swoole server 基类，其他类型server继承此类
 * @author WayneSun
 */
class service {

    /**
     * ```php
     * 'Set'=>[
     *  'host'=>'127.0.0.1',
     *  'port'=>9501
     * ]
     * 
     * ```
     * @var array
     */
    protected $_config;

    /**
     * 具体行为类的类名，建议传入包括命名空间
     * @var string
     */
    protected $_behavior;
    private $_events = [
        "onStart",
        "onShutdown",
        "onWorkerStart",
        "onWorkerStop",
        "onTimer",
        "onConnect",
        "onClose",
        "onTask",
        "onFinish",
        "onPipeMessage",
        "onWorkerError",
        "onManagerStart",
        "onManagerStop"
    ];

    /**
     * 由子类构造函数初始化，tcp,websocket,http
     * @var swoole 
     */
    public $swoole;

    public function __construct($config) {
        $this->_config = $config;
    }

    /**
     * 启动服务,this->swoole需要再子类中初始化
     * @param boolean $clearIPC 是否清除消息队列
     * @param string $timezone 时区
     * 
     */
    public function start($clearIPC = true, $timezone = 'Asia/Shanghai') {
        system("umask 002");
        $timezone && date_default_timezone_set($timezone);
        //清除队列
        if ($clearIPC && isset($this->_config['Set']['message_queue_key'])) {
            $messagekey = sprintf("0x%08x", intval($this->_config['Set']['message_queue_key']));
            system('ipcrm -Q ' . $messagekey);
        }
        if (isset($this->_config['log_file'])) {
            //创建swoole_log日志目录
            if (!is_dir($this->_config['log_file'])) {
                @mkdir($this->_config['log_file'], 0775, true);
            }
        }
        if (!is_object($this->swoole)) {
            exit('The swoole object has not been initialized yet! ');
        }
        $this->swoole->set($this->_config['Set']);
        foreach ($this->_events as $event_name) {
            if (method_exists($this, $event_name)) {
                $this->swoole->on(substr($event_name, 2), array($this, $event_name));
            }
        }
        $this->swoole->start();
    }

    /**
     * 添加监听事件
     * @param array $liste_events
     */
    public function addListentEvents($liste_events) {
        $this->_events = array_merge($this->_events, $liste_events);
    }

    public function onStart($serv) {
        
    }

    public function onWorkerStart($serv, $workerId) {
        if (function_exists('apc_clear_cache')) {
            apc_clear_cache();
        }
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
        try {
            define('SWOOLE_WORKER_ID', $workerId);
            $_behavior = $this->_config['Behavior'];
            $this->_behavior = new $_behavior();
            $MainProcessName = implode(' ', $_SERVER['argv']);
            if ($serv->taskworker) {
                swoole_set_process_name("{$MainProcessName}_task_pid_{$serv->master_pid}");
            } else {
                swoole_set_process_name("{$MainProcessName}_work_pid_{$serv->master_pid}");
            }
            $this->_behavior->onWorkerStart($serv, $workerId);
        } catch (\Exception $ex) {
            echo 'onworkerstart err';
            //$this->logException($ex);
        } catch (\Throwable $ex) {
            //$this->logException($ex);
        }
    }

    public function onWorkerError($serv, $workerId, $workerPid, $exitCode) {
        try {
            $data = error_get_last();
            $msg = "code:{$exitCode}";
            if ($data) {
                $msg .= ',msg:' . var_export($data, true);
            }
            if (defined('SWOOLE_WORKER_ID')) {
                logClient::log($msg, "onWorkerError.log");
            } else {
                echo "onWorkerError:" . $msg . PHP_EOL;
            }
        } catch (\Exception $ex) {
            
        }
    }

    public function onWorkerStop($serv, $workerId) {
        try {
            $this->_behavior->onWorkerStop($serv, $workerId);
        } catch (\Exception $ex) {
            $this->logException($ex);
        } catch (\Throwable $ex) {
            $this->logException($ex);
        }
    }

    public function onConnect($serv, $fd, $fromId) {
        try {
            echo 'onConnect';
            $this->_behavior->onConnect($serv, $fd, $fromId);
        } catch (Exception $ex) {
            
        }
    }

    public function onClose($serv, $fd, $fromId) {
        try {
            $this->_behavior->onClose($serv, $fd, $fromId);
        } catch (Exception $ex) {
            
        }
    }

    public function onPipeMessage($serv, $fromWorkerId, $message) {
        try {
            $this->_behavior->onPipeMessage($serv, $fromWorkerId, $message);
        } catch (Exception $ex) {
            
        }
    }

    public function onTask($serv, $taskId, $fromId, $data) {
        try {
            $this->_behavior->onTask($serv, $taskId, $fromId, $data);
        } catch (\Exception $ex) {
            $this->logException($ex);
        } catch (\Throwable $ex) {
            $this->logException($ex);
        }
    }

    public function onFinish($serv, $taskId, $data) {
        try {
            $this->_behavior->onFinish($serv, $taskId, $data);
        } catch (\Exception $ex) {
            $this->logException($ex);
        } catch (\Throwable $ex) {
            $this->logException($ex);
        }
    }

}
