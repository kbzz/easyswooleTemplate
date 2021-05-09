<?php

namespace EasySwoole\EasySwoole;


use App\Crontab\OrderCancel;
use EasySwoole\Component\Di;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\ORM\DbManager;
use EasySwoole\ORM\Db\Connection;
use EasySwoole\EasySwoole\Config;
use EasySwoole\Redis\Config\RedisConfig;
use EasySwoole\Pool\Manager;
use EasySwoole\Utility\File;
use  EasySwoole\EasySwoole\Crontab\Crontab;


class EasySwooleEvent implements Event
{

    public static function initialize()
    {
        // TODO: Implement initialize() method.
        date_default_timezone_set('Asia/Shanghai');


        // 配置控制器命名空间
        Di::getInstance()->set(SysConst::HTTP_CONTROLLER_NAMESPACE, 'App\\Controller\\');
        // 路由解析最大层级
        Di::getInstance()->set(SysConst::HTTP_CONTROLLER_MAX_DEPTH, 5);

        // 全局异常处理
        Di::getInstance()->set(SysConst::HTTP_EXCEPTION_HANDLER, [\App\ExceptionHandler::class, 'handle']);

        // 载入配置文件
        self::loadConf(EASYSWOOLE_ROOT . '/Config');

    }

    public static function mainServerCreate(EventRegister $register)
    {
        // 热加载
        self::hotReload();
        self::mySql($register);
        self::redis();

//        // 取消订单
//        Crontab::getInstance()->addTask(OrderCancel::class);
//
////        self::Timer($register);


    }

    public static function onRequest(Request $request, Response $response): bool
    {
        // TODO: Implement onRequest() method.
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
        // TODO: Implement afterAction() method.
    }

    public static function loadConf($confPath)
    {

        $conf = Config::getInstance();
        $files = File::scanDirectory($confPath);

        foreach ($files['files'] as $file) {
            $data = require_once $file;
            $conf->setConf(strtolower(basename($file, '.php')), (array)$data);
        }
    }

    public static function mySql($register)
    {

        $config = new \EasySwoole\ORM\Db\Config();
        $config->setDatabase(Config('database.project.database'));
        $config->setUser(Config('database.project.username'));
        $config->setPassword(Config('database.project.password'));
        $config->setHost(Config('database.project.host'));
        $config->setPort(Config('database.project.port'));
        $config->setCharset(Config('database.project.charset'));
        //连接池配置
        $config->setGetObjectTimeout(3.0); //设置获取连接池对象超时时间
        $config->setIntervalCheckTime(30 * 1000); //设置检测连接存活执行回收和创建的周期
        $config->setMaxIdleTime(15); //连接池对象最大闲置时间(秒)
        $config->setMaxObjectNum(20); //设置最大连接池存在连接对象数量
        $config->setMinObjectNum(5); //设置最小连接池存在连接对象数量
        $config->setAutoPing(5); //设置自动ping客户端链接的间隔
        DbManager::getInstance()->addConnection(new Connection($config));

        $register->add($register::onWorkerStart, function () {
            // 链接预热
            DbManager::getInstance()->getConnection()->__getClientPool()->keepMin();
        });

    }

    public static function redis()
    {
        // redis 连接池
        $config = new \EasySwoole\Pool\Config();
        $redisConfig1 = new RedisConfig(config('redis'));
        Manager::getInstance()->register(new \App\Common\RedisPool($config, $redisConfig1), 'redis');
    }

    /**
     * 热加载
     */
    public static function hotReload()
    {
        $hotReloadOptions = new \EasySwoole\HotReload\HotReloadOptions;
        $hotReload = new \EasySwoole\HotReload\HotReload($hotReloadOptions);
        $hotReloadOptions->setMonitorFolder([EASYSWOOLE_ROOT . '/App']);
        $server = ServerManager::getInstance()->getSwooleServer();
        $hotReload->attachToServer($server);
    }

    public static function Timer($register)
    {
        $register->add(EventRegister::onWorkerStart, function (\swoole_server $server, $workerId) {
            //如何避免定时器因为进程重启而丢失
            //例如在第一个进程 添加一个10秒的定时器
            if ($workerId == 0) {
                \EasySwoole\Component\Timer::getInstance()->loop(10 * 1000, function () {
                    // 从数据库，或者是redis中，去获取下个就近10秒内需要执行的任务
                    // 例如:2秒后一个任务，3秒后一个任务 代码如下
                    \EasySwoole\Component\Timer::getInstance()->after(2 * 1000, function () {
                        //为了防止因为任务阻塞，引起定时器不准确，把任务给异步进程处理
                        Logger::getInstance()->console("time 2", false);
                    });
                    \EasySwoole\Component\Timer::getInstance()->after(3 * 1000, function () {
                        //为了防止因为任务阻塞，引起定时器不准确，把任务给异步进程处理
                        Logger::getInstance()->console("time 3", false);
                    });
                });
            }
        });
    }
}