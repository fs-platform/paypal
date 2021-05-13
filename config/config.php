<?php

return [
    'mode'    => env('PAYPAL_MODE', 'sandbox'),


    'client_id'     => 'AYVJ5B_TcdYGsmaUPBhc8jyrC1kCG6zVv_n7mCX4ovBfFvimqp9UsunwYfB1Zaa9F3QFhLxmczbAmHbG',

    'client_secret' => 'EDjfpyCEcMJt8rDnrtLlAqEb_2eVmRrr6TXEUG9xuHc6hhws2bOKqKg4YhbsoiIBuEdrXsWRCiYiv9AQ',

    'return_url'    => 'http://127.0.0.1/pay/callback/paypal?act=success',

    'cancel_url'    => 'http://127.0.0.1/pay/callback/paypal?act=cancel'
];