<?php
define('BASEDIR', __DIR__);
include BASEDIR . '/autoload.php';
$config = [
    'Host'=>'0.0.0.0',
    'Port'=>9501,
    'Behavior'=>'\app\imBehavior',
    'Set'=>[
        'worker_num'=>2,
        'task_worker_num'=>4,
    ]
];
$ws = new \library\wsService($config);
$ws->start();





