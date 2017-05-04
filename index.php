<?php
define('BASEDIR', __DIR__);
include BASEDIR . '/autoload.php'; // 自动加载具体behavior类 ，也可以不用这个 通过include引入类文件
$config = [
    'Host'=>'0.0.0.0',
    'Port'=>9501,
    'Behavior'=>'\app\imBehavior',
    'Set'=>[
        'worker_num'=>2,
        'task_worker_num'=>4,
    ]
];
$ws = new waynesun\swoole\wsService($config);
$ws->start();





