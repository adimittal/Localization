<?php

use yii\widgets\ActiveForm;
use app\components\ConfigManager;
use yii\helpers\Html;

$cm = new ConfigManager();

$projectNames = array_combine($cm->getProjectNames(), $cm->getProjectNames());

$form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);
echo $form->field($model, 'project')->dropDownList($projectNames, []);
$downloadForm = new DownloadForm();
echo $downloadForm->submitBtn();
ActiveForm::end();

/**
 * Description of UploadForm
 *
 * @author adityamittal
 */
class DownloadForm {
  
  public function submitBtn() {
    $submit = <<<HT
        <div class="form-group">
HT;
    $submit .= Html::submitButton('Submit', ['class' => 'btn btn-primary']);
    $submit .= "</div>";
    
    return $submit;
  }


}
