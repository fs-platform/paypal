<?php

namespace Smbear\Paypal\Services;

use Illuminate\Config\Repository;
use Smbear\Paypal\Enums\PaypalEnums;
use Smbear\Paypal\Exceptions\ConfigException;
use Illuminate\Contracts\Foundation\Application;

class PaypalConfigService
{
    /**
     * PaymentConfig constructor.
     * @throws ConfigException
     */
    public function __construct()
    {
        if (!file_exists(config_path(PaypalEnums::CONFIG_FILE.'.php'))){
            throw new ConfigException('配置文件'.PaypalEnums::CONFIG_FILE.'.php'.'不存在');
        }
    }

    /**
     * @Notes:获取到配置文件的值
     *
     * @param string $key
     * @return Repository|Application|mixed|null
     * @throws ConfigException
     * @Author: smile
     * @Date: 2021/5/12
     * @Time: 17:57
     */
    public function getConfigValue(string $key)
    {
        $keys = array_keys(config(PaypalEnums::CONFIG_FILE));

        if (empty($keys)) return null;

        if (!in_array($key,$keys)){
            throw new ConfigException(PaypalEnums::CONFIG_FILE.'中 参数'.$key.'不存在');
        }

        return config(PaypalEnums::CONFIG_FILE.'.'.$key);
    }
}