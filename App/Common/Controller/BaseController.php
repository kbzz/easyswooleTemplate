<?php


namespace App\Common\Controller;


use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Validate\Validate;
use Swoole\Coroutine\Http\Client\Exception;

class BaseController extends Controller
{
    public $input = [];

    public function index()
    {

    }

    /**
     * 权限相关的设置
     * @param string|null $action
     * @return bool|null
     */
    protected function onRequest(?string $action): ?bool
    {
        // 中间件
        $middleware = (new \App\Common\Middleware\Middleware())->handle($this->request());

        if ($middleware !== true) {
            if (is_string($middleware)) {
                $this->responseError($middleware);
            } else {
                $this->responseError($middleware['message'], $middleware['status']);
            }
            return false;
        }
        // 参数验证，验证器
        $validate = (new \App\Common\Validate\Validate())->handle($this->request());

        if ($validate !== true) {
            $this->responseError($validate);
            return false;
        }
        $requestParam = $this->request()->getRequestParam() ?? [];
        $attributesParam = $this->request()->getAttributes() ?? [];

        $this->input = array_merge($requestParam, $attributesParam);
        return true;

    }


    /**
     *
     * @param string|null $action
     * @return bool|void
     */
    /**
     * 找不到方法
     * @param string|null $action
     */
    protected function actionNotFound(?string $action)
    {
        $class = static::class;
        $this->responseError("{$class} has not action for {$action}");
    }

    /**
     * 异常
     * @param \Throwable $throwable
     */
    protected function onException(\Throwable $throwable): void
    {
        $this->responseException($throwable->getMessage());
    }

    /**
     * 请求成功
     * @param string $message
     * @param array $data
     * @param string $status
     * @return bool
     */
    public function responseSuccess($message = '', $data = [], $status = '')
    {
        $response['status'] = $status ?: '1';
        $response['msg'] = $message ?: 'success';
        !empty($data) ? $response['data'] = $data : $response['data'] = [];
        return $this->responseBody($response, 200);
    }

    /**
     * 返回失败
     * @param string $message
     * @param string $status
     * @return bool
     */
    public function responseError($message = '', $status = '')
    {
        $response['status'] = $status ?: '0';
        $response['msg'] = $message ?: '网络错误';
        return $this->responseBody($response, 200);
    }

    /**
     * 返回异常
     * @param string $message
     * @return bool
     */
    public function responseException($message = '')
    {
        return $this->responseError($message);
    }

    protected function responseBody($response, $code = 200)
    {
        if (!$this->response()->isEndResponse()) {
            $this->response()->write(json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $this->response()->withHeader('Content-type', 'application/json;charset=utf-8');
            $this->response()->withStatus($code);
            return true;
        } else {
            return false;
        }
    }

}