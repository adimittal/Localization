<?php

namespace app\models\baseHttp;

use app\components\Timer;
use app\components\AppLog;
use Yii;

/**
 * Description of Agent: This class is used to perform Create Read
 * Update Delete (CRUD) operations using curl
 */
class Agent {

  /** @var  ConfigManager */
  protected $configManager;
  protected $server;
  protected $ssl;
  protected $port;
  protected $curlOptions = array();
  protected $logCurlOptions = array();
  protected $appLog;
  protected $curlResult;
  protected $errorNo;
  protected $curlInfo;
  private $headers;
  private $requestStr = "";
  private $reqObj;
  private $reqId = "ERROR__REQUEST_ID_WAS_NOT_SET";
  private $actionUrl = '';
  private $timer;

  public function __construct($appLog, $configManager) {
    $this->appLog = $appLog;
    $this->configManager = $configManager;
    $this->server = $this->configManager->getDefaultServer();
    $this->ssl = $this->configManager->getSSL();
    $this->headers = $this->configManager->getAllHeaders(); //Yii::app()->request->getAllHeaders();
    $this->timer = new Timer();
  }

  protected function setCurlOptions($option, $value, $code) {
    $this->curlOptions[$option] = $value;
    $this->logCurlOptions["{$code}"] = array($option => $value);
  }

  /**
   * Do POST, PUT, DELETE, GET request with curl.
   *
   * ItsOn server expects us to send json encoded data.
   */
  public function request(Request $request) {
    $this->reqObj = $request;
    AppLog::log('Request: ' . print_r($request, true), 'error', 'application');
    if ($request->getPasswordAuth() == true) {
      $response = $this->doPasswordAuthenticatedRequest($request);
    }
    else {
      $this->doRequest($request);
      $response = $this->createResponse();
    }
    AppLog::log('Response: ' . print_r($response, true), 'error', 'application');
    return $response;
  }

  private function logRequest($request) {
    if (is_array($this->headers)) {
      $rawheaders = array_change_key_case($this->headers, CASE_UPPER);
    }
    if (isset($rawheaders['X-IO-REQUEST-ID'])) {
      $requestId = $rawheaders['X-IO-REQUEST-ID'];
      $this->reqId = $requestId;
      $this->requestStr = "for X-IO-REQUEST-ID $requestId";
    }
    if (isset($rawheaders) && isset($rawheaders['X-IO-SIMDB']) && $rawheaders['X-IO-SIMDB'] == true) {
      AppLog::log('Request HEADERS: ' . print_r($rawheaders, true), 'info', 'application');
    }
    AppLog::log("Request to REST Layer $this->requestStr \n" . print_r($request->getPrintable(), true), 'info', 'application');
  }

  private function logResponse($error = false, $errMsg = '') {
    if (isset($this->curlInfo['content_type']) && 'application/octet-stream' == $this->curlInfo['content_type']) {
      // Do not log binary data $this->curlResult
      AppLog::log("Result from REST Layer $this->requestStr \n" . '<binary result>', 'info', 'application');
    }
    else {
      AppLog::log("Result from REST Layer $this->requestStr \n" . print_r($this->curlResult, true), 'info', 'application');
    }
    AppLog::log('Curl info: ' . print_r($this->curlInfo, true), 'info', 'application');
    if ($error) {
      AppLog::log("Curl Error number: $this->errorNo, Error: $errMsg", 'error', 'application');
      $this->logCurlOptions();
    }
  }

  protected function doRequest(Request $request) {
    $this->curlOptions = array();
    $this->logCurlOptions = array();

    $this->reqObj = $request;
    $this->logRequest($request);

    $method = $request->getMethod();
    $port = $request->getPort() ? : $this->port;

    $this->actionUrl = $request->getActionUrl();
    $this->setCurlOptions(CURLOPT_URL, $this->actionUrl, 'CURLOPT_URL');
    $this->setCurlOptions(CURLOPT_HTTPHEADER, $request->getHeaders(), 'CURLOPT_HTTPHEADER');

    if ($this->ssl) {
      $this->setCurlOptions(CURLOPT_SSL_VERIFYPEER, true, 'CURLOPT_SSL_VERIFYPEER');
      $this->setCurlOptions(CURLOPT_SSL_VERIFYHOST, 2, 'CURLOPT_SSL_VERIFYHOST');
    }

    $this->setCurlOptions(CURLOPT_RETURNTRANSFER, true, 'CURLOPT_RETURNTRANSFER');
    if (isset($port) && $port > 0) {
      $this->setCurlOptions(CURLOPT_PORT, $port, 'CURLOPT_PORT');
    }

    if ($method != 'GET') {
      $this->setCurlOptions(CURLOPT_POST, count($request->getFields()), 'CURLOPT_POST');

      switch ($request->getContentType()) {
        case ContentType::X_WWW_FORM_URL_ENCODED:
          $this->setCurlOptions(CURLOPT_POSTFIELDS, $request->stringifyFields(), 'CURLOPT_POSTFIELDS');
          AppLog::log('Stringified:' . print_r($request->stringifyFields(), true), 'info', 'application');
          break;

        case ContentType::MULTIPART_FORM_DATA:
          $this->setCurlOptions(CURLOPT_POSTFIELDS, $request->getRequestBody(), 'CURLOPT_POSTFIELDS');
          // Do not log binary data $request->getRequestBody()
          AppLog::log('Request body:' . '<ContentType::MULTIPART_FORM_DATA>', 'info', 'application');
          break;

        default:
          $this->setCurlOptions(CURLOPT_POSTFIELDS, json_encode($request->getFields(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 'CURLOPT_POSTFIELDS');
          AppLog::log(print_r('Input parameters: ' . json_encode($request->getFields(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), true), 'info', 'application');
      }
    }

    if (in_array($method, array('DELETE', 'PUT'))) {
      $this->setCurlOptions(CURLOPT_CUSTOMREQUEST, $method, 'CURLOPT_CUSTOMREQUEST');
    }

    $ch = curl_init();
    $curlSetoptResult = curl_setopt_array($ch, $this->curlOptions);

    if (false === $curlSetoptResult) {
      $this->logCurlOptions();
    }

    $this->makeCurlRequest($ch);
  }

  /**
   * doPasswordAuthenticatedRequest
   *
   * API like Transifex make use of a password authenticated Request
   */
  protected function doPasswordAuthenticatedRequest($request) {
    $this->reqObj = $request;

    //get necessary parts of request
    $url = $request->getUrl();
    $user = $request->getUsername();
    $pass = $request->getPassword();
    $type = $request->getMethod();

    $this->logRequest($request); //log the request
    //setup and execute the curl request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, "$user:$pass");

    if ($type == 'GET') {
      curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    }
    else if ($type == 'POST' || $type == 'PUT') {
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $request->getFields());
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    }
    else if ($type == 'DELETE') {
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
    }
    $this->makeCurlRequest($ch);
    return json_decode($this->curlResult, true);
  }

  private function makeCurlRequest($ch) {
    $error = false;
    $this->timer->start();
    $this->curlResult = curl_exec($ch);
    $this->timer->log("Curl request for action " . $this->actionUrl);
    $this->curlInfo = curl_getinfo($ch);
    $this->errorNo = curl_errno($ch);
    $method = $this->reqObj->getMethod();
    $reqid = $this->reqId;
    $url = $this->reqObj->getUrl();
    $resp_code = $this->curlInfo['http_code'];
    if (($errMsg = curl_error($ch)) || !HTTPStatus::is2nn($this->curlInfo['http_code'])) {
      $error = true;
      $req_base64 = base64_encode(json_encode($this->reqObj));
      $response = array(
        'culrResult' => $this->curlResult,
        'curlInfo' => $this->curlInfo,
        'curlerrno' => $this->errorNo,
      );
      $resp_base64 = base64_encode(json_encode($response));
      Yii::error("BEGIN:METHOD=$method:RESPCODE=$resp_code:REQID=$reqid:URL=$url:REQ_B64=$req_base64:RESP_B64=$resp_base64:END", "http_agent");
    }

    $this->concurrentLoginNotAllowed();
    $this->logResponse($error, $errMsg);
    curl_close($ch);
  }

  private function concurrentLoginNotAllowed() {
    $concurrentLoginNotAllowedMessage = '{"error": {"code": "invalid.token","message": "concurrent.login.notallowed"}}';
    if ($this->curlResult == $concurrentLoginNotAllowedMessage) {
      http_response_code(403);
      print_r($this->curlResult);
      die;
    }
  }

  protected function createResponse() {
    return new Response($this->curlResult, $this->errorNo, $this->curlInfo);
  }

  protected function logCurlOptions() {
    if ($this->logCurlOptions && is_array($this->logCurlOptions)) {
      AppLog::log('Curl options log: ' . print_r($this->logCurlOptions, true), 'info', 'application');
    }
  }

  /**
   * @param mixed $appParams
   */
  public function setAppParams($appParams) {
    $this->appParams = $appParams;
  }

  /**
   * @return mixed
   */
  public function getAppParams() {
    return $this->appParams;
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
   * @param mixed $curlInfo
   */
  public function setCurlInfo($curlInfo) {
    $this->curlInfo = $curlInfo;
  }

  /**
   * @return mixed
   */
  public function getCurlInfo() {
    return $this->curlInfo;
  }

}
