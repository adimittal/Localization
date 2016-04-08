<?php

namespace app\models;

use Exception;

/**
 * Description of Translation
 *
 * @author adityamittal
 */
class Translation extends AbstractModel {

  private $word;
  protected $tb; //translation builder
  private $translateService; //translation service
  private $translateStore; //translation store service
  public $resourceLang = 'en'; //the resource language

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

  private function getSlug($project) {
    try {
      $slug = $this->configManager->getSlug($project);
    } catch (Exception $ex) {
      $error = $ex->getMessage();
      return ['error' => $error];
    }
    return $slug;
  }

  public function getProjectDetails($project) {
    $this->tb->service = 'transifex';
    $this->tb->project = $this->getSlug($project);

    return json_decode($this->translateService->getProject($this->tb));
  }

  public function getProjectResources($project) {
    return $this->getProjectDetails($project)->resources;
  }

  private function getProjectResourcesArray($project) {
    $resources = $this->getProjectResources($project);

    $return = [];

    foreach ($resources as $r) {
      $filename = $r->name;
      $slug = $r->slug;
      $return[$slug] = $filename;
    }

    return $return;
  }

  public function getProjectResourceFileNames($project) {
    $resources = $this->getProjectResources($project);
    $fileNames = [];
    foreach ($resources as $r) {
      $fileNames[] = isset($r->name) ? $r->name : '';
    }
    return $fileNames;
  }

  /**
   * Get all locale keys => values
   * TODO: Take Yii->app() usage out of this function
   * @return array List of all locales, keys => value
   */
  public function getAll($project, $languageIn = '') {
    $language = !empty($language) ? $languageIn : Yii::app()->language;
    $projectSlug = $this->getSlug($project);
    $messagesDir = $this->configManager->getMessageDataPath($projectSlug);
    $path = $messagesDir . DIRECTORY_SEPARATOR . $language . DIRECTORY_SEPARATOR;
    $localeArray = array();
    $fileNamesArray = $this->getProjectResourceFileNames($project);

    foreach ($fileNamesArray as $fileName) {
      $fileName = $path . $fileName;
      if (file_exists($fileName)) {
        $localeArray = array_merge($localeArray, include($fileName));
      }
    }

    return $localeArray;
  }
  
  private function discoverMessagesFileType($filename) {
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    switch ($ext) {
      case 'php' :
        return 'PHP_ARRAY'; break;
      case 'strings' :
        return 'STRINGS'; break;
      case 'html' :
        return 'HTML'; break;
      case 'xml' :
        return 'ANDROID'; break;
      case '' :
        return 'HTML'; break;
      default:
        throw new Exception("Could not discover the message file type for Transifex from extension $ext");
    }
  }

  /**
   * Upload a new resource to transifex
   * @param $resourceArray - filenames Ex: ['test.php', 'test2.php']
   */
  public function uploadNewResourcesInTransifex($project, $resources) {

    $projectSlug = $this->getSlug($project);

    $this->tb->service = 'transifex';
    $this->tb->project = $projectSlug;
    $resourceDir = $this->configManager->getMessageDataPath($projectSlug)."/$this->resourceLang";

    foreach ($resources as $r) {
      $this->tb->messages_file = $resourceDir."/".$r;
      $this->tb->messages_file_type = $this->discoverMessagesFileType($r);
      $this->tb->required = array('project', 'messages_file');

      echo $this->translateService->createResource($this->tb);
    }
  }

  /**
   * Update the portal resources for transifex
   */
  public function uploadResourcesInTransifex($project) {
    $projectSlug = $this->getSlug($project);
    $messagesDir = $this->configManager->getMessageDataPath($projectSlug);

    /**
     * language, slug, and filename
     */
    $source_language_code = $this->getProjectDetails($project)->source_language_code;
    $resourcesToUpdate = array($source_language_code => $this->getProjectResourcesArray($project));

    foreach ($resourcesToUpdate as $language => $resourceSlugs) {
      $this->resourceLang = $language;
      $resourcePath = $messagesDir . '/' . $language;
      foreach ($resourceSlugs as $resourceSlug => $filename) {
        $messages_file = $resourcePath . '/' . $filename;
        $this->updateResource($projectSlug, $resourceSlug, $messages_file);
      }
    }

    return true;
  }

  /**
   * Pull the portal translation files from transifex
   */
  public function downloadTranslationsFromTransifex($project) {
    $projectSlug = $this->getSlug($project);
    $messagesDir = $this->configManager->getMessageDataPath($projectSlug);

    /**
     * language, slug, and filename
     */
    $languageTeams = $this->getProjectDetails($project)->teams; //same as language codes but mixed formats like es or es_MX
    $resources = $this->getProjectResourcesArray($project);

    $translationsToPull = [];
    foreach ($languageTeams as $t) {
      $translationsToPull[$t] = $resources;
    }

    foreach ($translationsToPull as $language => $resourceSlugs) {
      $languagePath = $messagesDir . '/' . $language;
      $languageCode = $language;
      foreach ($resourceSlugs as $resourceSlug => $filename) {
        $translation = $this->getTranslation($projectSlug, $resourceSlug, $languageCode);

        if (!$translation) {
          return "Check settings (such as transifex user/pass), failed to pull translation for Project: $projectSlug, Resource: $resourceSlug, LanguageCode: $languageCode";
        }
        $this->writeLanguageFile($languagePath, $filename, $translation);
      }
    }

    return true;
  }

  private function writeLanguageFile($languagePath, $filename, $translation) {
    if (!is_dir("$languagePath/")) {
      //the message dir doesn't exist, make it and let it be writable
      mkdir("$languagePath/", 0777, true);
    }
    file_put_contents($languagePath . '/' . $filename, $translation);
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
    $this->tb->project = $project;
    $this->tb->messages_file = $messages_file;
    $this->tb->resourceSlug = $resourceSlug;
    if (empty($project)) {
      $this->tb->project = $this->p->getGet('project');
    }
    if (empty($messages_file)) {
      $this->tb->messages_file = $this->p->getPut('messages_file');
    }
    if (empty($resourceSlug)) {
      $this->tb->resourceSlug = $this->p->getPut('resourceSlug');
    }
    $this->tb->messages_file_type = $this->discoverMessagesFileType($this->tb->messages_file);
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
