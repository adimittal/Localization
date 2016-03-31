<?php

namespace app\models;

use app\models\Transifex;


/*
 * Encapsulates any translation service such as Google translate, transifex
 */

/**
 * Description of TranslateService
 *
 * @author adityamittal
 */
class TranslateService {

  private $transifex;

  public function init(TranslationBuilder $tb) {

  }

  /**
   * Check for the required fields and return error string if required fields are missing, otherwise return an empty string
   */
  public function checkRequired($tb) {
    $error = array();
    foreach ($tb->required as $r) {
      if (empty($tb->$r)) {
        $error[] = "$r is required";
      }
    }

    return $error;
  }

  /**
   * Get all the projects
   * @param TranslationBuilder $tb
   */
  public function getProjects(TranslationBuilder $tb) {
    $error = $this->checkRequired($tb);
    if (!empty($error)) {
      return json_encode($error);
    }
    if ($tb->service == 'transifex') {
      $this->transifex = new Transifex($tb);
      return $this->transifex->getProjects();
    }
  }

  /**
   * Get a project instance
   * @param TranslationBuilder $tb
   */
  public function getProject(TranslationBuilder $tb) {
    $error = $this->checkRequired($tb);
    if (!empty($error)) {
      return json_encode($error);
    }
    if ($tb->service == 'transifex') {
      $this->transifex = new Transifex($tb);
      return $this->transifex->getProject();
    }
  }

  /**
   * Get details by language code
   * @param TranslationBuilder $tb
   */
  public function getLanguage(TranslationBuilder $tb) {
    $error = $this->checkRequired($tb);
    if (!empty($error)) {
      return json_encode($error);
    }
    if ($tb->service == 'transifex') {
      $this->transifex = new Transifex($tb);
      return $this->transifex->getLanguage($tb->languageCode);
    }
  }

  /**
   * Get a specific resource by its slug
   * @param TranslationBuilder $tb
   */
  public function getResource(TranslationBuilder $tb) {
    $error = $this->checkRequired($tb);
    if (!empty($error)) {
      return json_encode($error);
    }
    if ($tb->service == 'transifex') {
      $this->transifex = new Transifex($tb);
      return $this->transifex->getResource($tb->resourceSlug);
    }
  }

  /**
   * Create resource
   * @param TranslationBuilder $tb
   * @return type
   */
  public function createResource(TranslationBuilder $tb) {
    $error = $this->checkRequired($tb);
    if (!empty($error)) {
      return json_encode($error);
    }
    if ($tb->service == 'transifex') {
      $this->transifex = new Transifex($tb);
      if (empty($tb->resourceName)) {
        $tb->resourceName = basename($tb->messages_file); //if the resource name is not set we use the file name to set it
      }
      if (empty($tb->resourceSlug)) {
        $tb->slugifyResource(); //if a resource slug is not set we use a resource name to make it
      }
      if (empty($tb->messages_file_type)) {
        $tb->messages_file_type = 'PHP_ARRAY';
      }
      return $this->transifex->createResource($tb->resourceName, $tb->resourceSlug, $tb->messages_file, $tb->messages_file_type);
    }
  }

  /**
   * Update a resource
   * @param TranslationBuilder $tb
   * @return type
   */
  public function updateResource(TranslationBuilder $tb) {
    $error = $this->checkRequired($tb);
    if (!empty($error)) {
      return json_encode($error);
    }
    if ($tb->service == 'transifex') {
      $this->transifex = new Transifex($tb);
      if (empty($tb->resourceName)) {
        $tb->resourceName = basename($tb->messages_file); //if the resource name is not set we use the file name to set it
      }
      if (empty($tb->resourceSlug)) {
        $tb->slugifyResource(); //if a resource slug is not set we use a resource name to make it
      }
      return $this->transifex->putResource($tb->resourceSlug, $tb->messages_file);
    }
  }

  /**
   * Delete a resource
   * @param TranslationBuilder $tb
   * @return type
   */
  public function deleteResource(TranslationBuilder $tb) {
    $error = $this->checkRequired($tb);
    if (!empty($error)) {
      return json_encode($error);
    }
    if ($tb->service == 'transifex') {
      $this->transifex = new Transifex($tb);

      return $this->transifex->deleteResource($tb->resourceSlug);
    }
  }

  /**
   * Get the list of available resources
   * @param TranslationBuilder $tb
   * @param type $resource
   */
  public function getResources(TranslationBuilder $tb) {
    $error = $this->checkRequired($tb);
    if (!empty($error)) {
      return json_encode($error);
    }
    if ($tb->service == 'transifex') {
      $this->transifex = new Transifex($tb);
      return $this->transifex->getResources();
    }
  }

  /**
   * Get translation
   * Note that we get back a translation object from transifex but we only return its content
   * @param TranslationBuilder $tb
   */
  public function getTranslation(TranslationBuilder $tb) {
    $error = $this->checkRequired($tb);
    if (!empty($error)) {
      return json_encode($error);
    }
    if ($tb->service == 'transifex') {
      $this->transifex = new Transifex($tb);

      $translation = $this->transifex->getTranslations($tb->resourceSlug, $tb->languageCode);

      if(!$translation){
        return false;
      }
      return $translation->content;
    }
  }

  /**
   * Add a translation file
   * @param TranslationBuilder $tb
   * @param type $resource
   */
  public function putTranslationFile(TranslationBuilder $tb) {
    $error = $this->checkRequired($tb);
    if (!empty($error)) {
      return json_encode($error);
    }
    if ($tb->service == 'transifex') {
      $this->transifex = new Transifex($tb);
      return $this->transifex->putTranslations($tb->resourceSlug, $tb->language, $tb->file);
    }
  }

}
