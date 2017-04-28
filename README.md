# swooletest
方便快速创建基于swoole的服务
##用法
* app中新建具体behavior类，该类根据要使用的service(tcp,http,websocket)继承自相应的behavior
* 在运行脚本中指定要使用的behavior
> 使用完整命名空间路径
* 初始化相应的service;
* 运行
##example
```php
define('BASEDIR', __DIR__);
include BASEDIR . '/autoload.php'; //可自定义
$config = [
    'Host'=>'0.0.0.0',
    'Port'=>9501,
    'Behavior'=>'\app\imBehavior', //具体行为类
    'Set'=>[
        'worker_num'=>2, //onTask必须设置
        'task_worker_num'=>4,
    ]
];
$ws = new \library\wsService($config); //初始化服务
$ws->start();
```