<?php

use yii\widgets\ActiveForm;
use app\components\ConfigManager;
use yii\helpers\Html;

$cm = new ConfigManager();

$projectNames = array_combine($cm->getProjectNames(), $cm->getProjectNames());

$form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);
echo $form->field($model, 'project')->dropDownList($projectNames, []);
echo $form->field($model, 'messageFiles[]')->fileInput(['multiple' => true, 'accept' => 'application/xml, text/html, text/strings, text/php']);
$uploadForm = new UploadForm();
echo $uploadForm->submitBtn();
ActiveForm::end();

/**
 * Description of UploadForm
 *
 * @author adityamittal
 */
class UploadForm {
  
  public function submitBtn() {
    $submit = <<<HT
        <div class="form-group">
HT;
    $submit .= Html::submitButton('Submit', ['class' => 'btn btn-primary']);
    $submit .= "</div>";
    
    return $submit;
  }


}
