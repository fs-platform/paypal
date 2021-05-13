<center>Paypal支付<center>

### 安装使用

* composer require smbear/paypal
Paypal支付流程
* 创建订单

  ```shell
  curl -v -X POST https://api-m.sandbox.paypal.com/v2/checkout/orders \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer Access-Token" \
  -d '{
    "intent": "CAPTURE",
    "purchase_units": [
      {
        "amount": {
          "currency_code": "USD",
          "value": "100.00"
        }
      }
    ]
  }'
  ```
  
  ```php
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
  ```

* 查询订单

  ```php
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
  ```

* 当订单状态为APPROVED 审核下

  ```php
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
  ```