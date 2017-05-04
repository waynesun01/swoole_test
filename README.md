# swoole_test
完善中
方便快速创建基于swoole的服务
## 配置
需要php.ini中开启swoole.use_namespace，使用命名空间
## 用法
* app中新建具体behavior类，该类根据要使用的service(tcp,http,websocket)继承自相应的behavior
* 在运行脚本中指定要使用的behavior
> 使用完整命名空间路径
* 初始化相应的service;
* 运行
## example
```php
define('BASEDIR', __DIR__);
require 'autoload.php';//也可通过require引入行为类文件
require 'vendor/autoload.php';
$config = [
    'Host'=>'0.0.0.0',
    'Port'=>9501,
    'Behavior'=>'\app\imBehavior', //具体行为类
    'Set'=>[
        'worker_num'=>2, //onTask必须设置
        'task_worker_num'=>4,
    ]
];
//require 'app/imBehavior.php';//也可以自定义自动载入文件来加载具体行为类
$ws = new waynesun\swoole\wsService($config); //初始化服务
$ws->start();
```