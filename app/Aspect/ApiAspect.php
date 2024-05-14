<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/5/18 09:47
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Aspect;

use App\Annotation\Api;
use App\Exception\ApiErrorException;
use App\Service\ApiService;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use \PDOException;
use function Hyperf\Support\env;

#[Aspect]
class ApiAspect extends AbstractAspect
{
    public ?int $priority = 99;
    public array $annotations = [
        Api::class
    ];

    #[Inject]
    protected ResponseInterface $response;

    #[Inject]
    protected RequestInterface $request;

    #[Inject]
    protected ApiService $api_service;

    /**
     * @throws ApiErrorException
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        try {
            $res = $proceedingJoinPoint->process();

            //如果api接口返回数组，直接默认是成功返回格式
            return is_array($res) ? $this->response->json($this->api_service->encryptData([
                'data' => $res,
                'code' => 200,
                'status' => true,
                'msg' => $this->request->isMethod('GET') ? '请求成功' : '操作成功'
            ])) : $res;
        } catch (PDOException $pdoException) {
            //数据库的错误，正式环境不能直接返回sql语句
            if (env('APP_ENV') === 'dev') {
                throw new ApiErrorException($pdoException->getMessage(), (int)$pdoException->getCode(),
                    $pdoException->getPrevious());
            } else {
                throw new ApiErrorException("系统错误", (int)$pdoException->getCode(), $pdoException->getPrevious());
            }
        } catch (\Throwable $exception) {
            throw new ApiErrorException($exception->getMessage(), (int)$exception->getCode(),
                $exception->getPrevious());
        }
    }
}