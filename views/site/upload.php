<?php

use yii\widgets\ActiveForm;

$form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);
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
    return <<<HT
        <button>Submit</button>
HT;
  }


}
