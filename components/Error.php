<?php

namespace app\components;

use Yii;

/**
 * Error object
 *
 * @author adityamittal
 */
class Error {
  
  public $code;
  public $status;
  public $message;
  public $http_response_code;
  
  public function __construct() {
    $this->success();
  }
  
  public function success($message = "No error", $code = 0, $status = 'Success', $http_response_code = 200) {
    $this->code = $code;
    $this->status = $status;
    $this->message = $message;
    $this->http_response_code = $http_response_code;
    
    return $this;
  }
  
  public function fail($message = "Unknown failure", $code = 1, $status = 'Fail', $http_response_code = 400) {
    $this->code = $code;
    $this->status = $status;
    $this->message = $message;
    $this->http_response_code = $http_response_code;
    
    Yii::$app->response->statusCode = $http_response_code;
    
    return $this;
  }
  
  public function missingInputError($inputName) {
    $funcName = debug_backtrace()[1]['function'];
    $className = debug_backtrace()[1]['class'];
    $this->code = 2;
    $this->status = "MissingInput";
    $this->message = "Missing input $inputName in $funcName of $className";
    
    return $this;
  }
  
}
