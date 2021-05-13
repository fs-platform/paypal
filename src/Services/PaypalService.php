<?php

namespace Smbear\Paypal\Services;

use PayPalHttp\HttpException;
use Smbear\Paypal\Enums\PaypalEnums;
use Smbear\Paypal\Exceptions\FunctionException;
use Smbear\Paypal\Traits\PaypalParams;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;

class PaypalService
{
    use PaypalParams;

    public $paypalClientService;

    public $paypalConfigService;

    public function __construct()
    {
        $this->paypalClientService = new PaypalClientService();

        $this->paypalConfigService = new PaypalConfigService();
    }

    /**
     * @Notes:构建请求
     *
     * @return array
     * @throws FunctionException
     * @throws \Smbear\Paypal\Exceptions\ConfigException
     * @Author: smile
     * @Date: 2021/5/13
     * @Time: 15:54
     */
    public function buildRequestBody() : array
    {
        $this->checkFunction();

        return [
            "intent" => "CAPTURE",
            "purchase_units" => [[
                "reference_id" => $this->params['setReferenceId']['referenceId'],
                "amount" => [
                    "value"         => (string) $this->params['setAmount']['mount'],
                    "currency_code" => $this->params['setAmount']['currencyCode']
                ]
            ]],
            "application_context" => [
                "cancel_url" => $this->paypalConfigService->getConfigValue('cancel_url'),
                "return_url" => $this->paypalConfigService->getConfigValue('return_url')
            ]
        ];
    }

    /**
     * @Notes:核对方法
     *
     * @throws FunctionException
     * @Author: smile
     * @Date: 2021/5/13
     * @Time: 15:54
     */
    public function checkFunction()
    {
        $functions = explode(',',PaypalEnums::MUST_FUNCTION);

        foreach ($functions as $value){
            if (empty($this->params[$value])){
                throw new FunctionException('请设置'.$value.'方法');
            }
        }
    }

    /**
     * @Notes:创建支付订单
     *
     * @return array
     * @throws FunctionException
     * @throws \Smbear\Paypal\Exceptions\ConfigException
     * @Author: smile
     * @Date: 2021/5/13
     * @Time: 15:56
     */
    public function createOrder() : array
    {
        $request = new OrdersCreateRequest();
        $request->prefer('return=representation');

        $request->body = $this->buildRequestBody();

        try{
            $response = $this->paypalClientService
                ->getClient()
                ->execute($request);

            if ($response->statusCode == 201) {
                return customer_paypal_result('success',200,'创建成功',$response->result);
            }

            return customer_paypal_result('error',500,'创建失败');
        }catch (\Exception $exception){
            return $this->throwException($exception);
        }
    }

    /**
     * @Notes:获取到支付订单
     *
     * @param string $orderId
     * @return array
     * @Author: smile
     * @Date: 2021/5/13
     * @Time: 16:34
     */
    public function getOrder(string $orderId) : array
    {
        $request = new OrdersGetRequest($orderId);

        try {
            $response = $this->paypalClientService
                ->getClient()
                ->execute($request);

            if ($response->statusCode == 200){
                switch ($response->result->status){
                    case 'COMPLETED':
                        return customer_paypal_result('success','200','支付成功');
                    case 'APPROVED':
                        return $this->captureOrder($orderId);
                    default:
                        return custom_return_error('error',$response->result->status,'支付失败');
                }
            }
            return custom_return_error('error',$response->statusCode,'支付失败');
        }catch (\Exception $exception){
            return $this->throwException($exception);
        }
    }

    /**
     * @Notes:支付审核
     *
     * @param string $orderId
     * @return array
     * @Author: smile
     * @Date: 2021/5/13
     * @Time: 16:35
     */
    private function captureOrder(string $orderId): array
    {
        $request = new OrdersCaptureRequest($orderId);

        try{
            $response = $this->paypalClientService
                ->getClient()
                ->execute($request);

            if ($response->statusCode == 201){
                if ($response->result->status == 'COMPLETED'){
                    return customer_paypal_result('success','200','支付成功');
                }

                return custom_return_error('error',$response->result->status,'支付失败');
            }

            return custom_return_error('error',$response->statusCode,'支付失败');
        }catch (\Exception $exception){
            return $this->throwException($exception);
        }
    }

    /**
     * @Notes:格式化异常
     *
     * @param $exception
     * @return array
     * @Author: smile
     * @Date: 2021/5/13
     * @Time: 16:07
     */
    private function throwException($exception) : array
    {
        if ($exception instanceof HttpException){

            $message = json_decode($exception->getMessage(),true);

            if ($exception->statusCode == '401'){
                return customer_paypal_result('error',$exception->statusCode,$message['error']);
            }

            if ($exception->statusCode == '400'){
                return customer_paypal_result('error',$exception->statusCode,$message['message']);
            }
        }

        return customer_paypal_result('error',500,$exception->getMessage());
    }
}