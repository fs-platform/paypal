<?php

namespace Smbear\Paypal\Traits;

use Smbear\Paypal\Exceptions\ParamsException;

trait PaypalParams
{
    public $params = [];

    /**
     * @Notes:设置金额
     *
     * @param float $mount
     * @param string $currencyCode
     * @return object
     * @throws ParamsException
     * @Author: smile
     * @Date: 2021/5/13
     * @Time: 15:40
     */
    public function setAmount(float $mount,string $currencyCode = 'USD') : object
    {
        if (empty($mount) || empty($currencyCode)) throw new ParamsException(__METHOD__.'参数异常');

        $this->params['setAmount'] = [
            'mount'        => $mount,
            'currencyCode' => $currencyCode
        ];

        return $this;
    }

    /**
     * @Notes:设置编号
     *
     * @param string $referenceId
     * @return object
     * @throws ParamsException
     * @Author: smile
     * @Date: 2021/5/13
     * @Time: 15:40
     */
    public function setReferenceId(string $referenceId) : object
    {
        if (empty($referenceId)) throw new ParamsException(__METHOD__.'参数异常');

        $this->params['setReferenceId'] = [
            'referenceId' => $referenceId
        ];

        return $this;
    }
}