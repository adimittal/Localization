<?php

namespace app\controllers;

use Yii;
use app\models\Translation;
use app\models\UploadForm;
use app\models\DownloadForm;
use yii\web\UploadedFile;

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
   * Download translations from transifex
   * Query parameter project: portal, myaccount, iosliteclient, temmandroid, ioclient
   * curl -X GET 'http://localization.dev.itsonsaas.net:8000/translation/downloadfromtransifex?project=myaccount' -v
   */
  public function actionDownloadfromtransifex() {
    $project = $_GET['project'];
    $error = new \app\components\Error();
    
    if($this->model->downloadTranslationsFromTransifex($project)) {
      return json_encode($error);
    }
    $error->fail("Failed to download the files from transifex to localization repo");
    return json_encode($error);
  }
  
  /**
   * Upload translations to transifex
   * Query parameter project: portal, myaccount, iosliteclient, temmandroid, ioclient
   * curl -X GET 'http://localization.dev.itsonsaas.net:8000/translation/uploadtotransifex?project=myaccount' -v
   */
  public function actionUploadtotransifex() {
    $project = $_GET['project'];
    return json_encode($this->model->uploadResourcesInTransifex($project));
  }
  
  /**
   * Upload translations to this localization repo
   * curl -X POST -H "Content-type: multipart/form-data" -H "Accept: application/json" -F 'UploadForm[project]=myaccount' -F UploadForm[messageFiles][]=@/git/saas-my-adi/yii/messages/myAccount/en/content_locale.php -F UploadForm[messageFiles][]=@/git/saas-my-adi/yii/messages/myAccount/en/forms_locale.php http://localization.dev.itsonsaas.net:8000/translation/upload?project=myaccount
   * @return string
   */
    public function actionUpload() {
    $model = new UploadForm();
    $error = new \app\components\Error();

    if (Yii::$app->request->isPost) {
      $model->load(Yii::$app->request->post());
      $model->messageFiles = UploadedFile::getInstances($model, 'messageFiles');
      if ($model->upload()) {
        if(Yii::$app->request->headers->get('Accept') == 'application/json') {
          return json_encode($error);
        }
        return $this->render('upload-confirm', ['model' => $model]);
      }
      $error->fail("Failed to upload the files");
      return json_encode($error);
    }
    else {
      // either the page is initially displayed or there is some validation error
      return $this->render('upload', ['model' => $model]);
    }
  }
  
  /**
   * This action allows downloading the translations of a given project, it can be used from command line curl like so:
   * curl -X POST -d 'DownloadForm[project]=itsonportal' 'http://localization.dev.itsonsaas.net:8000/translation/download' -o itsonportal.zip -v
   * 
   * Post data: Array ( [_csrf] => dEtxWGJueGgdPh8JBBYbPDoRJm0NDR8FRiQ4Bwg6IThALBgyEgcrPA== [DownloadForm] => Array ( [project] => iosliteclient ) )
   * @return string
   */
  public function actionDownload() {
    $model = new DownloadForm();

    if (Yii::$app->request->isPost) {
      $model->load(Yii::$app->request->post());
      return json_encode($model->download());
    }
    else {
      // either the page is initially displayed or there is some validation error
      return $this->render('download', ['model' => $model]);
    }
  }

}
