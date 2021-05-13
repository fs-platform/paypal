<?php

if (!function_exists('customer_paypal_exception')){

    /**
     * @Notes:格式化异常
     *
     * @param $jsonData
     * @param string $pre
     * @return string
     * @Author: smile
     * @Date: 2021/5/13
     * @Time: 15:35
     */
    function customer_paypal_exception( $jsonData,string $pre = ''): string{
        $pretty = "";

        foreach ($jsonData as $key => $val) {
            $pretty .= $pre . ucfirst($key) .": ";

            if (strcmp(gettype($val), "array") == 0){
                $pretty .= "\n";
                $sno = 1;

                foreach ($val as $value) {
                    $pretty .= $pre . "\t" . $sno++ . ":\n";
                    $pretty .= customer_paypal_exception($value, $pre . "\t\t");
                }
            } else {
                $pretty .= $val . "\n";
            }
        }

        return $pretty;
    }
}

if (!function_exists('customer_paypal_result')){

    /**
     * @Notes:统一返回结果
     *
     * @param string $status
     * @param int $code
     * @param string $message
     * @param null $data
     * @return array
     * @Author: smile
     * @Date: 2021/5/13
     * @Time: 15:35
     */
    function customer_paypal_result(string $status,int $code,string $message,$data = null) : array{
        return [
            'status'  => $status,
            'code'    => $code,
            'message' => $message,
            'data'    => $data
        ];
    }
}