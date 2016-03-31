<?php

namespace app\components;

use Yii;

/**
 * Description of ConfigManager
 *
 * @author adityamittal
 */
class ConfigManager {
  
  private $server;
  private $ssl;
  private $headers;
  

  public function getTransifexUrl() {
    return \Yii::$app->params['transifexUrl'];
  }

  public function getTransifexSettings() {
    $user = getenv("TRANSIFEX_USER");
    $pass = getenv("TRANSIFEX_PASS");
    return $settings = ['user' => $user, 'password' => $pass];
  }
  
  public function getUploadPath() {
    return Yii::$app->basePath. "/uploads";
  }
  
  public function getMessageDataPath($projectSlug) {
    return Yii::$app->basePath. "/messageData/$projectSlug";
  }

  public function getDefaultServer() {
    return $this->server;
  }
  
  public function getSSL() {
    return $this->ssl;
  }
  
  public function getAllHeaders() {
    return $this->headers;
  }

}
