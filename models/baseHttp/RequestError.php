<?php

namespace app\models\baseHttp;

/**
 * Description of Service Component - class used for Service Component management
 * 
 */
class RequestError {
  private $id;
  private $code;
  private $msg;

  public function __construct($id, $code, $msg) {
    $this->id = $id;
    $this->code = $code;
    if($code > 0) {
      $this->msg = $msg;
    }
  }

  public function __toString() {
      return json_encode(array(
          'id' => $this->id,
          'code' => $this->code,
          'message' => $this->msg,
        )
      );
  }

  public function asArray()
  {
    return array(
      'id' => $this->id,
      'code' => $this->code,
      'message' => $this->msg,
    );
  }

  public function error_id() {
    return $this->id;
  }

  public function error_code() {
    return $this->code;
  }
 
  public function error_message() {
    return $this->msg;
  }
}
