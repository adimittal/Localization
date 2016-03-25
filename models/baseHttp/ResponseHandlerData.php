<?php

namespace app\models\baseHttp;

class ResponseHandlerData {

  private $response;
  private $method;
  private $type;
  private $model;
  private $function;

  public function setResponseHandlerData($response, $method, $type = '', $name = 'allModels', $function = 'operation') {
    $this->setResponse($response);
    $this->setMethod($method);
    $this->setType($type);
    $this->setModel($name);
    $this->setFunction($function);
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
   * @param mixed $method
   */
  public function setMethod($method) {
    $this->method = $method;
  }

  /**
   * @return mixed
   */
  public function getMethod() {
    return $this->method;
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
   * @param mixed Response
   */
  public function setResponse($response) {
    $this->response = $response;
  }

  /**
   * @return mixed
   */
  public function getResponse() {
    return $this->response;
  }

  /**
   * @param mixed $type
   */
  public function setType($type) {
    $this->type = $type;
  }

  /**
   * @return mixed
   */
  public function getType() {
    return $this->type;
  }
}
