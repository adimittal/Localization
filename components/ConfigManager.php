<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\components;

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
