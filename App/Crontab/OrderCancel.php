<?php

namespace App\Crontab;

use EasySwoole\EasySwoole\Crontab\AbstractCronTask;

class OrderCancel extends AbstractCronTask
{

    public static function getRule(): string
    {
        return '*/1 * * * *';
    }

    public static function getTaskName(): string
    {
        return 'orderCancel';
    }

    /**
     * 优惠券过期处理
     * @param int $taskId
     * @param int $workerIndex
     */
    function run(int $taskId, int $workerIndex)
    {
        var_dump(1);
    }

    function onException(\Throwable $throwable, int $taskId, int $workerIndex)
    {
        echo $throwable->getMessage();
    }
}
