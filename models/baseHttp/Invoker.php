<?php

namespace app\models\baseHttp;

use app\components\AppLog;
use app\components\ConfigManager;

/**
 * Description - invokes Rest call
 */
class Invoker {

  protected $invokerData;
  protected $debug;

  /**
   * @var SaasAgent
   */
  protected $agent;
  protected $request;
  protected $reqObj;
  protected $handlerData;
  protected $httpResponse;
  protected $response = array();

  public function __construct() {
    $configManager = new ConfigManager();
    $appLog = new AppLog();
    $this->reqObj = new Request();
    $this->agent = new Agent($appLog, $configManager);
  }

  /**
   * Called from doInvoke in Abstract
   * $invokerData
   */
  public function invoke(InvokerData $invokerData) {
    $this->invokerData = $invokerData;
    if ($this->invokerData->headers) {
      foreach ($this->invokerData->headers as $value) {
        $this->reqObj->removeHeader(trim(substr($value, 0, stripos($value, ':') - 1)));
        $this->reqObj->addHeader($value);
      }
    }

    if (is_array($this->invokerData->removeHeaders)) {
      foreach ($this->invokerData->removeHeaders as $removeKey) {
        $this->reqObj->removeHeader($removeKey);
      }
    }

    if ($this->invokerData->contentType) {
      $this->reqObj->setContentType($this->invokerData->contentType, $this->invokerData->contentTypeAndHeader);
    }

    if ($this->invokerData->passwordAuth == true) {
      return $this->passwordAuthRequest();
    }
  }

  private function passwordAuthRequest() {
    $this->invokerData->useUrlDirectly = true;
    $this->setDirectUrl();

    $this->reqObj->setPasswordAuth(true);
    $this->reqObj->setUsername($this->invokerData->username);
    $this->reqObj->setPassword($this->invokerData->password);
    $this->reqObj->setMethod($this->invokerData->method);
    $this->reqObj->setActionUrl($this->invokerData->modelEndpointUrl . $this->invokerData->id . $this->invokerData->action . $this->invokerData->stringified);

    $this->setRequestData();

    return $this->request($this->reqObj);
  }

  private function setDirectUrl() {
    if ($this->invokerData->useUrlDirectly) {
      $this->reqObj->setGenericEndPoint($this->invokerData->genericUrlVariable, $this->invokerData->genericPort, $this->invokerData->useUrlDirectly);
    }
    //XXXX Dirty Hack to avoid someone setting it and it not getting replaced.
    $this->invokerData->useUrlDirectly = false;
  }

  private function setRequestData() {
    if (isset($this->invokerData->data)) {
      $this->reqObj->setFields($this->invokerData->data);
      if ($this->invokerData->withStringify) {
        $this->invokerData->stringified = '?' . $this->reqObj->getQuery();
      }
      else {
        $url = $this->invokerData->getModelEndpointUrl();
        $params = $this->invokerData->getUrlParams();
        $this->invokerData->stringified = $this->stringifyParams($params, $url);
      }
      if ('?' == $this->invokerData->stringified) {
        $this->invokerData->stringified = '';
      }
    }
  }

  private function stringifyParams($params, $url = '') {
    $stringified = "";

    $questionCharacter = '?';

    if (!empty($url) && strpos($url, '?') !== FALSE) {
      $questionCharacter = '&';
    }

    if ((is_object($params) || is_array($params)) && !empty($params)) {
      $stringified = $questionCharacter . http_build_query($this->invokerData->getUrlParams());
    }
    return $stringified;
  }

  protected function setResponseHandler($response, $method, $type = 'allTypes', $model = 'allModels', $function = 'allOperations') {
    $this->handlerData = new ResponseHandlerData();
    $this->setResponseHandlerData($response, $method, $type, $model, $function);
  }

  private function setResponseHandlerData($response, $method, $type = '', $name = 'allModels', $function = 'operation') {
    $this->handlerData->setResponse($response);
    $this->handlerData->setMethod($method);
    $this->handlerData->setType($type);
    $this->handlerData->setModel($name);
    $this->handlerData->setFunction($function);
  }

  protected function prepareResponse() {
    $responseHandler = $this->prepareRequestResponse();

    return $responseHandler->getResponse();
  }

  public function prepareRequestResponse() {
    $factory = Factory::getInstance();
    $f = $factory->createModelFactory();
    $responseHandler = $f->createResponseHandler();
    $responseHandler->setHandlerData($this->handlerData);

    return $responseHandler;
  }

  protected function prepareResponseError() {
    $responseHandler = $this->prepareRequestResponse();

    return $responseHandler->getError();
  }

  public function request(Request $request) {
    return $this->agent->request($request);
  }

  /**
   * @param mixed $agent
   */
  public function setAgent($agent) {
    $this->agent = $agent;
  }

  /**
   * @return mixed
   */
  public function getAgent() {
    return $this->agent;
  }

  /**
   * @param mixed $debug
   */
  public function setDebug($debug) {
    $this->debug = $debug;
  }

  /**
   * @return mixed
   */
  public function getDebug() {
    return $this->debug;
  }

  /**
   * @param mixed $invokerData
   */
  public function setInvokerData($invokerData) {
    $this->invokerData = $invokerData;
  }

  /**
   * @return mixed
   */
  public function getInvokerData() {
    return $this->invokerData;
  }

  /**
   * @param mixed $request
   */
  public function setRequest($request) {
    $this->request = $request;
  }

  /**
   * @return mixed
   */
  public function getRequest() {
    return $this->request;
  }

  /**
   * @param mixed $httpResponse
   */
  public function setHttpResponse($httpResponse) {
    $this->httpResponse = $httpResponse;
  }

  /**
   * @return mixed
   */
  public function getHttpResponse() {
    return $this->httpResponse;
  }

}
