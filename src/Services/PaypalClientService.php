<?php

namespace Smbear\Paypal\Services;

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use Smbear\Paypal\Exceptions\ConfigException;

class PaypalClientService
{
    public $paypalConfigService;

    public function __construct()
    {
        $this->paypalConfigService = new PaypalConfigService();
    }

    /**
     * @Notes:创建环境
     *
     * @throws ConfigException
     * @Author: smile
     * @Date: 2021/5/12
     * @Time: 18:49
     */
    public function createEnvironment()
    {
        $clientId = $this->paypalConfigService->getConfigValue('client_id');
        $clientSecret = $this->paypalConfigService->getConfigValue('client_secret');

        if (empty($clientId) || empty($clientSecret)){
            throw new ConfigException('创建环境失败，参数不存在');
        }

        //判断是否是沙盒测试模式
        if ($this->paypalConfigService->getConfigValue('mode') == 'sandbox'){
            return new SandboxEnvironment(
                $clientId,$clientSecret
            );
        }

        return new ProductionEnvironment(
            $clientId,$clientSecret
        );
    }

    /**
     * @Notes:获取到client
     *
     * @return PayPalHttpClient
     * @Author: smile
     * @Date: 2021/5/12
     * @Time: 12:26
     * @throws ConfigException
     */
    public function getClient(): PayPalHttpClient
    {
        return new PayPalHttpClient($this->createEnvironment());
    }
}