<?php

namespace app\models\baseHttp;

use app\models\baseHttp\RequestError;
use app\components\AppLog;

/**
 * Description of Response
 *
 * This is the response object that gets passed back from the Rest Agent call
 *
 */
class Response {

  private $appLog;
  protected $rawData;
  protected $json;
  private $curlErrNo;
  private $curlInfo;
  protected $pf;
  protected $cf;
  protected $data;
  public $errObj;

  /*
   * Note: The following public variables are currently necessary for tests.
   * They are called from the AbstractModel during pagination. This is bad.
   * We need to switch everything out to use the private data variable and
   * put this behind a public method.
   */
  public $filters;
  public $groups;
  public $policy;

  public function __construct($rawData, $curlErrNo, $curlInfo) {
    $this->rawData = $rawData;
    $this->curlErrNo = $curlErrNo;
    $this->curlInfo = $curlInfo;

    $this->appLog = new AppLog();
    $this->setError();
  }

  public function setRawData($rawData) {
    $this->rawData = $rawData;
  }

  public function getRawData($codec = 'PhpArray') {
    return ('PhpArray' === $codec) ? json_decode($this->rawData, true) : json_decode($this->rawData);
  }

  public function domObj() {
    return $this->pf->createPartnerObject($this->data, $this->getContentType());
  }

  /**
   * Function returns Response Content Type
   * @return string
   *    application/json
   *    application/octet-stream
   */
  private function getContentType() {
    return (is_array($this->curlInfo) && isset($this->curlInfo['content_type'])) ? $this->curlInfo['content_type'] : ContentType::JSON;
  }

  private function isOctetStream() {
    return $this->getContentType() === ContentType::OCTET_STREAM;
  }

  private function isJson() {
    return $this->getContentType() === ContentType::JSON;
  }

  public function setError() {
    $this->errObj = new RequestError(0, '', '');

    if ($this->rawData === false || $this->curlErrNo) {
      $this->errObj = new RequestError($this->curlErrNo, 'saas.req.curl_error', "Error: Internal Curl Error: ");
      $this->appLog->log("Curl Error = " . $this->curlErrNo, 'error', 'application');
      return;
    }
    if (isset($this->rawData) && $this->isOctetStream()) {
      $octetObj = new stdClass();
      $octetObj->binaryData = $this->rawData;
      $octetObj->contentType = $this->getContentType();
      $this->data = $octetObj;
      return;
    }
    elseif (isset($this->rawData) && $this->isJson()) {
      $this->json = json_decode($this->rawData);
      $jsonerr = json_last_error();
      if ($jsonerr) {
        $this->appLog->log(print_r($this->rawData, true));
        $this->errObj = new RequestError( $jsonerr, 'saas.req.json_error', "Error: Internal JSON Parsing error: " . $this->rawData);
        return;
      }

      if ($this->json === null) {
        $this->errObj = new RequestError( 1, 'saas.req.json_error.null', "Error: JSON from server was empty");
        return;
      }

      if (is_object($this->json)) {
        if (property_exists($this->json, 'error')) {
          $this->errObj = new RequestError(1, $this->json->error->code, $this->json->error->message);
        }
        $this->data = $this->json;
      }
    }
    elseif ($this->rawData) {
      $this->json = json_decode($this->rawData);
      $jsonerr = json_last_error();
      if (! $jsonerr) {
        $this->data = $this->json;
      }
    }
  }

  public function getData() {
    return $this->data;
  }

  /**
   * @param mixed $data
   */
  public function setData($data) {
    $this->data = $data;
  }

  /**
   * Returns an associative array by key
   * @param type $key
   * @return type
   */
  public function getDataByKey($key) {
    $response_r = json_decode($this, true);
    if (isset($response_r[$key])) {
      return $response_r[$key];
    }
    return false;
  }

  /**
   * Abstracts the getting of params of the objects on which this function is invoked
   * NOTE: This function looks like it doesn't take any params but it does!
   * pass in the multidimensional params as a set of params
   * @author Gaurav Kumkar, Aditya Mittal
   */
  public function getDataByMultiKey() {
    $val = json_decode($this, true);
    for ($i = 0; $i < func_num_args(); $i++) {
      $valid = false; //assume response is not valid unless the ith argument is found
      if (isset($val[func_get_arg($i)])) {
        $val = $val[func_get_arg($i)];
        $valid = true;
      }
    }
    if ($valid) {
      return $val;
    }
    else {
      return null;
    }
    return null;
  }

  public function isGood() {
    if (!isset($this->errObj)) {
      return true;
    }
    $code = $this->error_code();
    if (isset($code) && ($code === 0)) {
      return true;
    }

    return false;
  }

  /*
    This is BAD, we're doing a lot of magic here to avoid errors.
   */

  public function __toString() {
    return (string)$this->rawData;
  }

  public function error_message() {
    return $this->errObj->error_message();
  }

  public function error_id() {
    return $this->errObj->error_id();
  }

  public function error_code() {
    return $this->errObj->error_code();
  }

  /**
   * @param mixed $errObj
   */
  public function setErrObj($errObj) {
    $this->errObj = $errObj;
  }

  /**
   * @return mixed
   */
  public function getErrObj() {
    return $this->errObj;
  }

  /**
   * @codeCoverageIgnore
   */
  public function getCurlErrNo() {
    return $this->curlErrNo;
  }

  /**
   * @return array
   * @codeCoverageIgnore
   */
  public function getCurlInfo() {
    return $this->curlInfo;
  }

}
