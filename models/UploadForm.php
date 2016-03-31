<?php

namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model {

  /**
   * @var UploadedFile[]
   */
  public $messageFiles;

  public function rules() {
    return [
      //not sure why the extension check is causing it to fail for php array files
      [['messageFiles'], 'file', 'skipOnEmpty' => false, 'extensions' => null, 'maxFiles' => 20],
    ];
  }

  public function upload() {
    $project = "/" . $_GET['project'];
    $cm = new \app\components\ConfigManager();
    $uploadPath = $cm->getUploadPath() . $project;
    if ($this->validate()) {
      foreach ($this->messageFiles as $file) {
        $this->saveFileInUploads($file, $uploadPath, $file->baseName . '.' . $file->extension);
      }
      return true;
    }
    else {
      return false;
    }
  }

  private function saveFileInUploads($file, $uploadPath, $filename) {
    if (!is_dir("$uploadPath/")) {
      //the message dir doesn't exist, make it and let it be writable
      mkdir("$uploadPath/", 0777, true);
    }
    $file->saveAs($uploadPath . "/" . $filename);
  }

}
