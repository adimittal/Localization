<?php

namespace app\models\baseHttp;

use JsonSerializable;
use app\components\ConfigManager;

use stdClass;

class Request implements JsonSerializable {

  protected $mf;
  protected $cf;
  protected $configManager;
  protected $port;
  protected $url;
  protected $header = array();
  protected $fields;
  protected $contentType;
  protected $method;
  protected $stringifiedFields;
  protected $ssl;
  protected $genericPort;
  protected $genericEndPoint;
  protected $requestType;
  protected $serverUrl;
  protected $actionUrlPair;
  protected $query;
  protected $config;
  protected $refreshAccessToken;
  protected $requestBody;
  protected $passwordAuth;
  protected $username;
  protected $password;
  protected $printableHeader = array();
  private $printableFieldsList = array('url', 'printableHeader', 'fields', 'method');
  protected $withPartner;

  public function __construct() {
    $this->configManager = new ConfigManager();
  }

  public function jsonSerialize() {
    $obj = array(
      'port' => $this->port,
      'url' => $this->url,
      'header' => $this->header,
      'fields' => $this->fields,
      'contentType' => $this->contentType,
      'method' => $this->method,
      'stringifiedFields' => $this->stringifiedFields,
      'ssl' => $this->ssl,
      'genericPort' => $this->genericPort,
      'genericEndPoint' => $this->genericEndPoint,
      'requestType' => $this->requestType,
      'serverUrl' => $this->serverUrl,
      'actionUrlPair' => $this->actionUrlPair,
      'query' => $this->query,
      'config' => $this->config,
      'refreshAccessToken' => $this->refreshAccessToken,
      'requestBody' => $this->requestBody,
      'passwordAuth' => $this->passwordAuth,
      'username' => $this->username,
      'password' => $this->password,
      'withPartner' => $this->withPartner,
    );
    return $obj;
  }

  private function createPrintable() {
    $this->printableHeader = array();
    foreach ($this->header as $h) {
      if (isset($GLOBALS['USER_DEV']) || (!preg_match('/authorization:Bearer/', $h))) {
        $this->printableHeader[] = $h;
      }
    }
  }

  public function getPrintable() {
    if (function_exists($this->createPrintable())) {
      $this->createPrintable();
    }
    $object = new stdClass();
    foreach ($this->printableFieldsList as $e) {
      $object->$e = $this->$e;
    }
    return $object;
  }

  public function setWithPartner($b = true) {
    $this->withPartner = $b;
  }

  /**
   * Setter for the generic url and port of the request object
   * @param string $genericUrlVariable
   * @param string $genericPort
   * @param boolean $useUrlDirectly
   */
  public function setGenericEndPoint($genericUrlVariable, $genericPort, $useUrlDirectly = false) {
    if ($useUrlDirectly) {
      $this->genericEndPoint = $genericUrlVariable;
      $this->genericPort = $genericPort;

      return true;
    }
    $default_server = $this->configManager->getParams('config', 'default', 'server');

    if (!empty($genericUrlVariable)) {
      $this->genericEndPoint = $this->configManager->getParams('config', 'servers', $default_server, $genericUrlVariable);
    }

    if (!empty($genericPort)) {
      $this->genericPort = $this->configManager->getParams('config', 'servers', $default_server, $genericPort);
    }

    return true;
  }

  /**
   * @param mixed $contentType
   */
  public function setContentType($contentType, $addHeader = true) {
    $this->contentType = $contentType;
    if ($addHeader) {
      $this->addHeader('Content-Type: ' . $contentType);
    }
  }

  /**
   * @return mixed
   */
  public function getContentType() {
    return $this->contentType;
  }

  /**
   * @param mixed $fields
   */
  public function setFields($fields) {
    $this->fields = $fields;
  }

  /**
   * @return mixed
   */
  public function getFields() {
    return $this->fields;
  }

  /**
   * @param mixed $genericPort
   */
  public function setGenericPort($genericPort) {
    $this->genericPort = $genericPort;
  }

  /**
   * @return mixed
   */
  public function getGenericPort() {
    return $this->genericPort;
  }

  /**
   * @param mixed $header
   */
  public function addHeader($header) {
    $this->header[] = $header;
  }

  /**
   * Example of a header_key is 'Content-Type'
   * @param type $header_key
   */
  public function removeHeader($header_key) {
    $matches = array();
    $header_key = strtolower($header_key);
    foreach ($this->header as $key => $header) {
      preg_match("@^$header_key@i", $header, $matches, PREG_OFFSET_CAPTURE);
      if (!empty($matches)) {
        unset($this->header[$key]);
      }
    }
  }

  /**
   * @return mixed
   */
  public function getHeaders() {
    return $this->header;
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
   * @param mixed $port
   */
  public function setPort($port) {
    $this->port = $port;
  }

  /**
   * @return mixed
   */
  public function getPort() {
    return $this->port;
  }

  /**
   * @param mixed $stringifiedFields
   */
  public function setStringifiedFields($stringifiedFields) {
    $this->stringifiedFields = $stringifiedFields;
  }

  /**
   * @return mixed
   */
  public function getStringifiedFields() {
    return $this->stringifiedFields;
  }

  /**
   * @param mixed $url
   */
  public function setUrl($url) {
    $this->url = $url;
  }

  /**
   * @return mixed
   */
  public function getUrl() {
    return $this->url;
  }

  /**
   * @return mixed
   */
  public function getGenericEndPoint() {
    return $this->genericEndPoint;
  }

  /**
   * @param mixed $requestType
   */
  public function setRequestType($requestType) {
    $this->requestType = $requestType;
  }

  /**
   * @return mixed
   */
  public function getRequestType() {
    return $this->requestType;
  }

  /**
   * @param mixed $config
   */
  public function setConfig($config) {
    $this->config = $config;
  }

  /**
   * @return mixed
   */
  public function getConfig() {
    return $this->config;
  }

  /**
   * @param mixed $serverUrl
   */
  public function setServerUrl($serverUrl) {
    $this->serverUrl = $serverUrl;
  }

  /**
   * @return mixed
   */
  public function getServerUrl() {
    return $this->serverUrl;
  }

  public function getActionUrl() {
    return $this->url;
  }
  
  public function setActionUrl($url) {
    $this->url = $url;
  }

  public function getQuery() {
    if (!$this->fields) {
      $this->fields = array();

      return;
    }
    
    $query = http_build_query($this->fields);
    return $query;
  }

  /**
   * @param mixed $query
   */
  public function setQuery($query) {
    $this->query = $query;
  }

  public function getRefreshAccessToken() {
    return $this->refreshAccessToken;
  }

  public function setRefreshAccessToken($refreshAccessToken) {
    $this->refreshAccessToken = $refreshAccessToken;
  }

  public function getRequestBody() {
    return $this->requestBody;
  }

  public function setRequestBody($requestBody) {
    $this->requestBody = $requestBody;
  }

  public function getPasswordAuth() {
    return $this->passwordAuth;
  }

  public function getUsername() {
    return $this->username;
  }

  public function getPassword() {
    return $this->password;
  }

  public function setPasswordAuth($passwordAuth) {
    $this->passwordAuth = $passwordAuth;
  }

  public function setUsername($username) {
    $this->username = $username;
  }

  public function setPassword($password) {
    $this->password = $password;
  }

}
