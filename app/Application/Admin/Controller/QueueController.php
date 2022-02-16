<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/2/15 18:29
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Admin\Controller;

use App\Annotation\View;
use App\Application\Admin\Lib\RenderParam;
use App\Application\Admin\Middleware\AdminMiddleware;
use App\Application\Admin\Model\QueueList;
use Hyperf\AsyncQueue\Message;
use Hyperf\Contract\ConfigInterface;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\Redis\RedisFactory;
use Psr\Container\ContainerInterface;

/**
 * @Middleware(AdminMiddleware::class)
 * @Controller(prefix="admin/queue")
 */
class QueueController extends AdminAbstractController
{

    /**
     * @GetMapping(path="status/lists")
     */
    function statusLists(ContainerInterface $container, ConfigInterface $config)
    {
        try {
            $async_queue_config = $config->get('async_queue.default');
            $redis = $container->get(RedisFactory::class)
                ->get($async_queue_config['pool'] ?? 'default');
            $type = $this->request->input('type', 'delayed');

            $channel = ($async_queue_config['channel'] ?? 'queue');
            $key = $channel . ':' . $type;
            $lists = [];
            if ($redis->type($key) === 4) {
                // zset
                $range = $redis->zRange($key, 0, 20, true);
                foreach ($range as $serialize => $time) {
                    /**
                     * @var Message $message
                     */
                    $message = unserialize($serialize);
                    $lists[] = [
                        'class_name' => $message->job()->class,
                        'method' => $message->job()->method,
                        'params' => json_encode($message->job()->params),
                        'params_md5' => md5(json_encode($message->job()->params)),
                        'max_attempts' => $message->job()
                            ->getMaxAttempts(),
                        'attempts' => $message->getAttempts(),
                        'time' => date('Y-m-d H:i:s', (int)$time)
                    ];
                }
            }
            if ($redis->type($key) === 3) {
                // zlist
                $range = $redis->lRange($key, 0, 20);
                foreach ($range as $serialize) {
                    /**
                     * @var Message $message
                     */
                    $message = unserialize($serialize);
                    $lists[] = [
                        'class_name' => $message->job()->class,
                        'method' => $message->job()->method,
                        'params' => json_encode($message->job()->params),
                        'params_md5' => md5(json_encode($message->job()->params)),
                        'max_attempts' => $message->job()
                            ->getMaxAttempts(),
                        'attempts' => $message->getAttempts(),
                        'time' => ''
                    ];
                }
            }
            $count_list = [
                'failed_count' => $redis->lLen($channel . ':failed'),
                'waiting_count' => $redis->lLen($channel . ':waiting'),
                'timeout_count' => $redis->lLen($channel . ':timeout'),
            ];

            return $this->returnSuccessJson(compact('lists', 'count_list'));
        } catch (\Exception $exception) {
            return $this->returnErrorJson($exception->getMessage());
        }
    }

    /**
     * @GetMapping(path="index/lists")
     */
    function lists()
    {
        $class_name = $this->request->input('class_name', '');
        $method = $this->request->input('method', '');
        $status = (int)$this->request->input('status', -1);
        $where = [];
        if ($class_name !== '') {
            $where[] = ['class_name', 'like', "%{$class_name}%"];
        }
        if ($method !== '') {
            $where[] = ['method', 'like', "%{$method}%"];
        }
        if ($status !== -1) {
            $where[] = ['status', '=', $status];
        }
        $lists = QueueList::where($where)
            ->orderBy('queue_id', 'DESC')
            ->paginate();

        return $this->returnSuccessJson(compact('lists', 'where'));
    }

    /**
     * 显示队列状态页面
     * @View()
     * @GetMapping(path="status")
     */
    function status()
    {
        return RenderParam::display();
    }

    /**
     * 执行记录页面
     * @View()
     * @GetMapping(path="index")
     */
    function index()
    {
        return RenderParam::display();
    }
}