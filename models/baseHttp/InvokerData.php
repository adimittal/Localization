<?php

namespace app\models\baseHttp;

use app\components\ConfigManager;

class InvokerData {

  public $configManager;
  public $modelEndpointUrl = ''; //end point to be appended to the partner di url
  public $refreshAccessToken = false;
  public $method = ''; //GET, PUT, POST, DELETE
  public $id = ''; //string|uuid $id identifier
  public $action = '';
  public $data = ''; //array|string $data data to be sent to the API
  public $apiCall = '';
  public $function = null;
  public $urlParams = array();
  public $withPartner = true;
  public $modelName = '';
  public $useUrlDirectly = false; //indicates if the url needs to be over written
  public $genericUrlVariable = '';
  public $genericPort = '';
  public $withStringify = false;
  public $stringified = '';
  public $contentType = '';
  public $headers = array();
  public $removeHeaders = array();
  public $contentTypeAndHeader = true;
  public $passwordAuth = false;
  public $username = '';
  public $password = '';

  public function __construct(
  $modelEndpointUrl = null, $method = null, $id = '', $action = '', $data = '', $apiCall = '', $function = null, $urlParams = array(), $modelName = '', $refreshAccessToken = true, $useUrlDirectly = false, $genericUrlVariable = '', $genericPort = '', $withStringify = false
  ) {

    $this->modelEndpointUrl = $modelEndpointUrl;
    $this->method = $method;
    $this->id = $id;
    $this->action = $action;
    $this->data = $data;
    $this->apiCall = $apiCall;
    $this->function = $function;
    $this->urlParams = $urlParams;
    $this->modelName = $modelName;
    $this->refreshAccessToken = $refreshAccessToken;
    $this->useUrlDirectly = $useUrlDirectly;
    $this->genericUrlVariable = $genericUrlVariable;
    $this->genericPort = $genericPort;
    $this->withStringify = $withStringify;
    $this->configManager = new ConfigManager();
  }

  /**
   * Set content type to json
   */
  public function setContentTypeToJson() {
    $this->contentType = ContentType::JSON;
  }

  /**
   * Set content type to json
   */
  public function setContentTypeToFormEncoded() {
    $this->contentType = ContentType::X_WWW_FORM_URL_ENCODED;
  }

  public function getContentType() {
    return $this->contentType;
  }

  public function setContentType($contentType) {
    $this->contentType = $contentType;
  }

  public function addHeader($key, $value) {
    $this->headers[$key] = $value;
  }

  public function removeHeader($key) {
    $this->removeHeaders[] = $key;
  }

  public function setDefaultSaasHeaders() {
    $this->headers['x-io-tenant-id'] = $this->configManager->getTenantName();
    $this->headers['x-io-partner-id'] = $this->getPartnerId();
    $this->setContentTypeToJson();
  }

  /**
   * @return string
   */
  public function getGenericPort() {
    return $this->genericPort;
  }

  /**
   * @param string $genericPort
   */
  public function setGenericPort($genericPort) {
    $this->genericPort = $genericPort;
  }

  /**
   * @return string
   */
  public function getGenericUrlVariable() {
    return $this->genericUrlVariable;
  }

  /**
   * @param string $genericUrlVariable
   */
  public function setGenericUrlVariable($genericUrlVariable) {
    $this->genericUrlVariable = $genericUrlVariable;
  }

  /**
   * @return bool|string
   */
  public function getUseUrlDirectly() {
    return $this->useUrlDirectly;
  }

  /**
   * @param bool|string $useUrlDirectly
   */
  public function setUseUrlDirectly($useUrlDirectly) {
    $this->useUrlDirectly = $useUrlDirectly;
  }

  public function getModelEndpointUrl() {
    return $this->modelEndpointUrl;
  }

  public function getMethod() {
    return $this->method;
  }

  public function getId() {
    return $this->id;
  }

  public function getAction() {
    return $this->action;
  }

  public function getData() {
    return $this->data;
  }

  public function getApiCall() {
    return $this->apiCall;
  }

  public function getFunction() {
    return $this->function;
  }

  public function setModelEndpointUrl($modelEndpointUrl) {
    $this->modelEndpointUrl = $modelEndpointUrl;
  }

  public function setMethod($method) {
    $this->method = $method;
  }

  public function setId($id) {
    $this->id = $id;
  }

  public function setAction($action) {
    $this->action = $action;
  }

  public function setData($data) {
    $this->data = $data;
  }

  public function setApiCall($apiCall) {
    $this->apiCall = $apiCall;
  }

  public function setFunction($function) {
    $this->function = $function;
  }

  /**
   * @param array $urlParams
   */
  public function setUrlParams($urlParams) {
    $this->urlParams = $urlParams;
  }

  /**
   * @return array
   */
  public function getUrlParams() {
    if(empty($this->urlParams)){
      $this->urlParams = array();
    }
    return $this->urlParams;
  }

  /**
   * @param string $modelName
   */
  public function setModelName($modelName) {
    $this->modelName = $modelName;
  }

  /**
   * @return string
   */
  public function getModelName() {
    return $this->modelName;
  }

  public function getRefreshAccessToken() {
    return $this->refreshAccessToken;
  }

  public function setRefreshAccessToken($refreshAccessToken) {
    $this->refreshAccessToken = $refreshAccessToken;
  }

  public function isWithStringify() {
    return $this->withStringify;
  }

  public function setWithStringify($withStringify) {
    $this->withStringify = $withStringify;
  }
}
