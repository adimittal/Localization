<?php

namespace app\models;

/**
 * Class to interact with Transifex service
 * 
 * @todo curl get and post with username / password should go to corresponding class 
 */
class Transifex extends AbstractModel {

  private $transifexUrl;
  private $settings;
  private $projectName;

  /**
   * Transifex::__construct()
   *
   * @param array $settings
   * @throws RuntimeException Exception.
   */
  public function init($translationBuilder) {
    $this->translationBuilder = $translationBuilder;
    $this->projectName = $this->translationBuilder->project;

    $this->transifexUrl = $this->configManager->getTransifexUrl();
    $this->settings = $this->configManager->getTransifexSettings();
    if (empty($this->settings['user']) || empty($this->settings['password'])) {
      echo "Transifex Credentials missing. Please set in your apache environment TRANSIFEX_USER and TRANSIFEX_PASS then try again.";
      die;
      throw new RuntimeException('Transifex Credentials missing');
    }
  }

  /**
   * Transifex::getProjects()
   *
   * @return array
   */
  public function getProjects() {
    $url = $this->transifexUrl . "projects/";
    return $this->call($url);
  }

  /**
   * Transifex::getProject()
   *
   * @return array
   */
  public function getProject() {
    $url = $this->transifexUrl . "project/$this->projectName/?details";
    return $this->call($url);
  }

  /**
   * Transifex::getResources()
   *
   * @return array
   */
  public function getResources() {
    $url = $this->transifexUrl . "project/$this->projectName/resources/";
    return $this->call($url);
  }

  /**
   * Transifex::getResource()
   *
   * @param mixed $resourceSlug
   * @return array
   */
  public function getResource($resourceSlug) {
    if ($resourceSlug) {
      $resourceSlug .= '/';
    }
    $url = $this->transifexUrl . "project/$this->projectName/resource/$resourceSlug";
    return $this->call($url);
  }

  /**
   * Transifex::createResource()
   *
   * @param $resourceName
   * @param $resourceSlug
   * @param $file
   * @param $i18n_type - can be 'PO' or 'PHP_ARRAY'  (Yii messages files are PHP_ARRAY so we'll default to that)
   * @return mixed
   */
  public function createResource($resourceName, $resourceSlug, $file, $i18n_type) {
    $url = $this->transifexUrl . "project/$this->projectName/resources";
    $body = array(
      'name' => $resourceName,
      'slug' => $resourceSlug,
      'i18n_type' => $i18n_type
    );
    $data = $this->curl->getCurlFile($file, $body);

    return $this->call($url, $data, 'POST');
  }

  /**
   * Transifex::putResource()
   *
   * @param $resourceSlug
   * @param $file
   * @return mixed
   */
  public function putResource($resourceSlug, $file) {
    $url = $this->transifexUrl . "project/$this->projectName/resource/$resourceSlug/content";
    $data = $this->curl->getCurlFile($file);

    return $this->call($url, $data, 'PUT');
  }

  /**
   * Transifex::deleteResource()
   * @param $resourceSlug
   * @param $file
   * @return mixed
   */
  public function deleteResource($resourceSlug) {
    $url = $this->transifexUrl . "project/$this->projectName/resource/$resourceSlug/";

    return $this->call($url, '', 'DELETE');
  }

  /**
   * Transifex::getLanguages()
   * Only the project owner or the project maintainers can perform this action.
   *
   * @return array
   */
  public function getLanguages() {
    $url = $this->transifexUrl . "project/$this->projectName/languages/";
    return $this->call($url);
  }

  /**
   * Transifex::getLanguage()
   * Only the project owner or the project maintainers can perform this action.
   *
   * @param $language
   * @return array
   */
  public function getLanguage($language) {
    $url = $this->transifexUrl . "project/$this->projectName/language/$language/?details";
    return $this->call($url);
  }

  /**
   * Transifex::getLanguageInfo()
   *
   * @param $language
   * @return array
   */
  public function getLanguageInfo($language) {
    $url = $this->transifexUrl . "language/$language/";
    return $this->call($url);
  }

  /**
   * Transifex::getTranslations()
   * Example: https://www.transifex.com/api/2/project/sprintportal/resource/content_localephp/translation/es_MX?file=myfile
   * @param mixed $resourceSlug
   * @param mixed $language
   * @param mixed $file - returns as file data if true, as json if false
   * @param bool $reviewedOnly
   * @return array
   */
  public function getTranslations($resourceSlug, $language, $file = false, $reviewedOnly = false) {
    $params = array();
    $params[] = $reviewedOnly ? "mode=reviewed" : "";
    $params[] = $file ? "file=myfile" : "";
    $url = $this->transifexUrl . "project/$this->projectName/resource/$resourceSlug/translation/$language?" . implode('&', $params);
    $translation = json_decode($this->call($url));
    
    return $translation;
  }

  /**
   * Transifex::putTranslations()
   *
   * @param $resourceSlug
   * @param $language
   * @param $file 
   * @return mixed
   */
  public function putTranslations($resourceSlug, $language, $file) {
    $url = $this->transifexUrl . "project/$this->projectName/resource/$resourceSlug/translation/$language";
    $data = $this->curl->getCurlFile($file);

    return $this->call($url, $data, 'PUT');
  }

  /**
   * Transifex::getStats()
   *
   * @param string $resourceSlug
   * @param string $language
   * @return array
   */
  public function getStats($resourceSlug, $language = null) {
    if ($language) {
      $language .= '/';
    }
    $url = $this->transifexUrl . "project/$this->projectName/resource/$resourceSlug/stats/$language";
    return $this->call($url);
  }

  private function call($url, $data = '', $method = 'GET') {
    $this->invokerData->modelEndpointUrl = $url;
    $this->invokerData->passwordAuth = true;
    $this->invokerData->username = $this->settings['user'];
    $this->invokerData->password = $this->settings['password'];
    $this->invokerData->data = $data;
    $this->invokerData->method = $method;

    $this->invokerData->removeHeader('x-io-tenant-id');
    $this->invokerData->removeHeader('x-io-partner-id');
    $this->invokerData->removeHeader('authorization');

    if ($method == 'POST') {
      $this->invokerData->contentType = 'multipart/form-data';
    }

    return json_encode($this->invoke($this->invokerData));
  }

}
