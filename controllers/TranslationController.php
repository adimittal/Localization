<?php

namespace app\controllers;

use app\models\Translation;

/**
 * Description of TranslationController
 *
 * @author adityamittal
 */
class TranslationController extends BaseController {

  private $model;

  public function init() {
    parent::init();
    $this->model = new Translation();
  }

  /**
   * Get the details of the project
   * @return type
   */
  public function actionProjectdetails() {
    $project = $_GET['project'];
    return json_encode($this->model->getProjectDetails($project));
  }
  
  /**
   * Get the details of the portal project
   * @return type
   */
  public function actionPortalresources() {
    return json_encode($this->model->getProjectResources('portal'));
  }
  /**
   * Get the details of the portal project
   * @return type
   */
  public function actionPortalresourcefilenames() {
    return json_encode($this->model->getProjectResourceFileNames('portal'));
  }
  
  /**
   * Download translations
   * Query parameter project: portal, myaccount, iosliteclient, temmandroid, ioclient
   */
  public function actionDownload() {
    $project = $_GET['project'];
    return $this->model->downloadTranslationsFromTransifex($project);
  }
  
  /**
   * Upload translations
   * Query parameter project: portal, myaccount, iosliteclient, temmandroid, ioclient
   */
  public function actionUpload() {
    $project = $_GET['project'];
    return $this->model->uploadResourcesInTransifex($project);
  }

}
