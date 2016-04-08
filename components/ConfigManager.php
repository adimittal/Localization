<?php

namespace app\components;

use Yii;
use yii\base\Exception;

/**
 * Description of ConfigManager
 *
 * @author adityamittal
 */
class ConfigManager {

  private $server;
  private $ssl;
  private $headers;
  private $portalslug = 'itsonportal';
  private $myaccountslug = 'my-account';
  private $iosliteclientslug = 'iosliteclient';
  private $temmandroidslug = 'ioclient-temm-android';
  private $ioclientslug = 'ioclient';

  public function getProjectNames() {
    return ['portal', 'myaccount', 'iosliteclient', 'temmandroid', 'ioclient'];
  }

  /**
   * Project name or slug can be passed in to get the slug
   * @param type $project
   * @return type
   * @throws Exception
   */
  public function getSlug($project) {
    switch ($project) {
      case 'portal' :
        return $this->portalslug;
      case 'itsonportal' :
        return $this->portalslug;
      case 'myaccount' :
        return $this->myaccountslug;
      case 'my-account' :
        return $this->myaccountslug;
      case 'iosliteclient' :
        return $this->iosliteclientslug;
      case 'temmandroid' :
        return $this->temmandroidslug;
      case 'ioclient-temm-android' :
        return $this->temmandroidslug;
      case 'ioclient' :
        return $this->ioclientslug;
      default :
        throw new Exception("Please provide a valid project name or project slug");
    }
  }

  public function getTransifexUrl() {
    return \Yii::$app->params['transifexUrl'];
  }

  public function getTransifexSettings() {
    $user = getenv("TRANSIFEX_USER");
    $pass = getenv("TRANSIFEX_PASS");
    return $settings = ['user' => $user, 'password' => $pass];
  }

  public function getUploadPath() {
    return Yii::$app->basePath . "/uploads";
  }
  
  public function getDownloadableZipPath() {
    return Yii::$app->basePath . "/web/downloadableZip";
  }

  public function getMessageDataPath($projectSlug) {
    return Yii::$app->basePath . "/messageData/$projectSlug";
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
