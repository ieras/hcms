<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2023/1/9 17:05
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

use Hyperf\Crontab\Crontab;

return [
    // 是否开启定时任务，根据业务需要开启
    'enable' => true,
    // 通过配置文件定义的定时任务
    'crontab' => [
        // Callback类型定时任务（默认）
//        (new Crontab())->setName('Test')->setRule('*/5 * * * * *')
//            ->setCallback([App\Application\Demo\Task\TestTask::class, 'execute'])
//            ->setMemo('这是一个示例的定时任务'),
        // Command类型定时任务
//        (new Crontab())->setType('command')->setName('Bar')->setRule('* * * * *')->setCallback([
//            'command' => 'swiftmailer:spool:send',
//            // (optional) arguments
//            'fooArgument' => 'barValue',
//            // (optional) options
//            '--message-limit' => 1,
//            // 记住要加上，否则会导致主进程退出
//            '--disable-event-dispatcher' => true,
//        ]),
    ],
];