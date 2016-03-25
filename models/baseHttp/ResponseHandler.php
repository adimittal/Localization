<?php

namespace app\models\baseHttp;

/**
 * ResponseHandler prepares front-end user-friendly messages and data from results of Response API calls;
 * The messages are model and function specific
 */
class ResponseHandler {

  private $response;
  private $error;
  private $httpResponse;
  private $method;
  private $type;
  private $model;
  private $function;
  private $responseMessages;
  private $id;
  private $code;
  private $locale = 'messages_locale';
  private $message;
  private $appLog;
  private $cf;
  private $appTranslate;
  private $debug;
  private $p;
  private $obj;
  private $data;

  public function setHandlerData(ResponseHandlerData $handlerData) {
    $this->httpResponse = $handlerData->getResponse();
    $f = Factory::getInstance();
    $this->cf = $f->createComponentFactory();
    $this->setAppLog($this->cf->createAppLog());
    $this->setAppTranslate($this->cf->createAppTranslate());
    $this->method = $handlerData->getMethod();
    $this->type = $handlerData->getType();
    $this->model = $handlerData->getModel();
    $this->function = $handlerData->getFunction();
    $this->debug = $this->mf()->createDebugWriter();
    $this->p = $this->cf->createIOPure();

    $respMessagesObj = $this->mf()->createResponseMessages();
    $this->responseMessages = $respMessagesObj->getMessages();
    $this->setResponseData();
    $this->setResponseError();
    $this->obj = $this->httpResponse->domObj();
  }

  /*
 * The Model Factory is extremely heavyweight due to ModelBuilder
 * We're not going to store it in any model any more to reduce the
 * memory footpring of the App. As an example the SecurityQuestion
 * object only has a few methods but come in at a huge 5.2MB serialized
 * as JSON.
 */
  protected function mf() {
    $factory = Factory::getInstance();
    return $factory->createModelFactory();
  }

  private function setResponseData() {
    if ($this->httpResponse->isGood()) {
      $obj = $this->httpResponse->domObj();
      $this->response['data'] = $obj;
    }
  }

  private function setResponseError() {
    if ($this->httpResponse && $this->httpResponse->isGood()) {
      $this->id = 'notify_Successful';
      $this->code = 0;
      $this->message = "$this->method $this->type was successful";
    }
    else {
      $this->id = $this->httpResponse->error_id();
      $this->code = $this->httpResponse->error_code();
      $this->message = $this->httpResponse->error_message();
    }

    // override default message if a pretty message exists
    $responseMessage = $this->getResponseMessage();
    if ($responseMessage) {
      $this->message = $this->translate($this->locale, $responseMessage);
    }

    // override id, if needed
    if (0 == $this->code) {
      $this->id = "notify_" . $this->function . 'Successful';
    }

    $this->response['error'] = $this->setError($this->id, $this->code, $this->message);
  }

  public function postProcessResponse() {
    if ($this->httpResponse && $this->httpResponse->isGood()) {
      $obj = $this->httpResponse->domObj();
      $this->response = $this->setPagination($obj);
      $this->response['error'] = $this->getError();
      $this->response = $this->debug->attachDebugMessages($this->response);

      return $this->response;
    }

    $this->response = $this->getResponse();
    $this->response['data'] = array("policy" => array(), "groups" => array(), "features" => array());
    $this->response = $this->debug->attachDebugMessages($this->response);

    return $this->response;
  }

  public function setPagination($obj = '') {
    if (!empty($obj)) {
      $this->obj = $obj;
    }
    $ret = array();
    $ret['data'] = $this->obj->getData();
    $count = $this->obj->getPageIndexCount();
    $count = $count == null ? 0 : $count;
    $ret['iTotalRecords'] = $count;
    $ret['iTotalDisplayRecords'] = $count;
    $ret['sEcho'] = $this->p->getGet('sEcho', 0, true);
    return $ret;
  }

  /**
   * @param type $response
   * @param type $method
   * @param type $type
   * @param type $model
   * @param type $function
   */
  public function setResponseHandler($response, $method, $type = ContentType::JSON, $model = 'allModels', $function = 'allOperations') {
    $this->handlerData = new ResponseHandlerData();
    $this->setResponseHandlerData($response, $method, $type, $model, $function);
  }

  /**
   * @param type $response
   * @param type $method
   * @param type $type
   * @param type $name
   * @param type $function
   */
  private function setResponseHandlerData($response, $method, $type = '', $name = 'allModels', $function = 'operation') {
    $this->handlerData->setResponse($response);
    $this->handlerData->setMethod($method);
    $this->handlerData->setType($type);
    $this->handlerData->setModel($name);
    $this->handlerData->setFunction($function);

    $this->setHandlerData($this->handlerData);
  }

  public function isGood($response) {
    if (isset($response['error']['code']) && $response['error']['code'] == 1) {
      return false;
    }
    return true;
  }

  /**
   * Takes a response from doInvoke and returns the data in the given key
   * If a key is not set, returns all the data
   * @param type $response
   * @param type $key
   */
  public function getData($response, $key = '') {
    if (isset($response['data'])) {
      $this->data = $response['data']->getData();
      if (!empty($key)) {
        return $this->data->$key;
      }
      return $this->data;
    }
    return false;
  }

  private function setError($id, $code, $message) {
    $this->error = array();
    $this->error['id'] = $id;
    $this->error['code'] = $code;
    $this->error['message'] = $message;

    return $this->error;
  }

  public function getError() {
    return $this->error;
  }

  /**
   * @param mixed $response
   */
  private function setResponse($response)
  {

    $this->response = $response;
  }

  /**
   * @return mixed
   */
  public function getResponse() {
    return $this->response;
  }

  private function getResponseMessage() {
    if (isset($this->responseMessages["{$this->model}"]["{$this->function}"]["{$this->id}"]["{$this->code}"])) {
      return $this->responseMessages["{$this->model}"]["{$this->function}"]["{$this->id}"]["{$this->code}"];
    }

    if ($this->code > 0) {
      $error = $this->setError($this->id, $this->code, $this->message);
      $this->getAppLog()->log("Error: Error received from API that we do not cover in message handler yet:" . print_r($error, true), 'error', 'application');
    }

    return false;
  }

  private function translate($locale, $messageCode) {
    return $this->getAppTranslate($locale, $messageCode);
  }

  /**
   * @param mixed $code
   */
  public function setCode($code) {
    $this->code = $code;
  }

  /**
   * @return mixed
   */
  public function getCode() {
    return $this->code;
  }

  /**
   * @param mixed $function
   */
  public function setFunction($function) {
    $this->function = $function;
  }

  /**
   * @return mixed
   */
  public function getFunction() {
    return $this->function;
  }

  /**
   * @param mixed $id
   */
  public function setId($id) {
    $this->id = $id;
  }

  /**
   * @return mixed
   */
  public function getId() {
    return $this->id;
  }

  /**
   * @param mixed $model
   */
  public function setModel($model) {
    $this->model = $model;
  }

  /**
   * @return mixed
   */
  public function getModel() {
    return $this->model;
  }

  /**
   * @param mixed $appLog
   */
  public function setAppLog($appLog) {
    $this->appLog = $appLog;
  }

  /**
   * @return mixed
   */
  public function getAppLog() {
    return $this->appLog;
  }

  /**
   * @param mixed $appTranslate
   */
  public function setAppTranslate($appTranslate) {
    $this->appTranslate = $appTranslate;
  }

  /**
   * @return mixed
   */
  public function getAppTranslate() {
    return $this->appTranslate;
  }

}
