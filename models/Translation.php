<?php

namespace app\models;

/**
 * Description of Translation
 *
 * @author adityamittal
 */
class Translation extends AbstractModel {

  private $word;
  private $tb; //translation builder
  private $translateService; //translation service
  private $translateStore; //translation store service

  public function init(TranslationBuilder $tb) {
    $this->tb = $tb;
    $this->translateService = new TranslateService();
    $this->translateStore = new TranslateStore();

    // $this->tb->setDefaults();
  }

  public function resource() {
    return $this->crud('resource');
  }

  public function resources() {
    return $this->crud('resources');
  }

  public function project() {
    return $this->crud('project');
  }

  public function projects() {
    return $this->crud('projects');
  }

  /**
   * Note: Ex: Translation slug for content_locale.php is content_localephp
   */
  public function getTranslation($projectSlug, $resourceSlug, $languageCode) {
    $this->tb->service = 'transifex';
    $this->tb->project = $projectSlug;
    $this->tb->resourceSlug = $resourceSlug;
    $this->tb->languageCode = $languageCode;
    $this->tb->required = array('project', 'resourceSlug', 'languageCode');

    $translation = $this->translateService->getTranslation($this->tb);

    return $translation;
  }

  /**
   * Note: Ex: Resource slug for content_locale.php is content_localephp
   */
  public function getResource() {
    $this->tb->service = 'transifex';
    $this->tb->project = $this->p->getGet('project');
    $this->tb->resourceSlug = $this->p->getGet('resourceSlug');
    $this->tb->required = array('project', 'resourceSlug');

    return $this->translateService->getResource($this->tb);
  }

  public function createResource() {
    $this->tb->service = 'transifex';
    $this->tb->project = $this->p->getGet('project');
    $this->tb->messages_file = $this->p->getPost('messages_file');
    $this->tb->messages_file_type = 'PHP_ARRAY';
    $this->tb->required = array('project', 'messages_file');

    echo $this->translateService->createResource($this->tb);
  }

  public function updateResource($project = null, $resourceSlug = null, $messages_file = null) {
    $this->tb->service = 'transifex';
    if(empty($project)){
      $this->tb->project = $this->p->getGet('project');
    }
    if(empty($messages_file)){
      $this->tb->messages_file = $this->p->getPut('messages_file');
    }
    if(empty($resourceSlug)){
      $this->tb->resourceSlug = $this->p->getPut('resourceSlug');
    }
    $this->tb->messages_file_type = 'PHP_ARRAY';
    $this->tb->required = array('project', 'messages_file');

    echo $this->translateService->updateResource($this->tb);
  }

  public function deleteResource() {
    $this->tb->service = 'transifex';
    $this->tb->project = $this->p->getGet('project');
    $this->tb->resourceSlug = $this->p->getGet('resourceSlug');
    $this->tb->required = array('project', 'resourceSlug');

    echo $this->translateService->deleteResource($this->tb);
  }

  public function getResources() {
    $this->tb->service = 'transifex';
    $this->tb->project = $this->p->getGet('project');
    $this->tb->required = array('project');

    echo $this->translateService->getResources($this->tb);
  }

  public function createResources() {

  }

  public function updateResources() {

  }

  public function deleteResources() {

  }

  public function getProject() {
    $this->tb->service = 'transifex';
    $this->tb->project = $this->p->getGet('project');
    $this->tb->required = array('project');

    echo $this->translateService->getProject($this->tb);
  }

  public function createProject() {

  }

  public function updateProject() {

  }

  public function deleteProject() {

  }

  public function getProjects() {
    $this->tb->service = 'transifex';

    echo $this->translateService->getProjects($this->tb);
  }

  public function createProjects() {

  }

  public function updateProjects() {

  }

  public function deleteProjects() {

  }

  public function getLanguage() {
    $this->tb->service = 'transifex';
    $this->tb->project = $this->p->getGet('project');
    $this->tb->languageCode = $this->p->getGet('languageCode');
    $this->tb->required = array('project', 'languageCode');

    echo $this->translateService->getLanguage($this->tb);
  }

  public function getWordTranslation() {
    $this->tb->word = $this->p->getGet('word');
    $this->tb->translateWord = true;
  }

  public function getMessageTranslation() {
    $this->tb->message = $this->p->getGet('word');
  }

  public function getFileTranslation($inDir, $inFile, $outDir, $outFile) {
    $this->tb->file = $inDir . "/" . $inFile;
    $this->tb->messages_dir = $outDir;
    $this->tb->messages_file = $outFile;
  }

  /**
   * @todo add a global translation and store it for future use
   */
  public function addGlobalTranslation() {
    $this->tb->scope = 'global';
  }

}
