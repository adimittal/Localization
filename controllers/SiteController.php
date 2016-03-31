<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\UploadForm;
use yii\web\UploadedFile;

class SiteController extends BaseController {

  public function behaviors() {
    return [
      'access' => [
        'class' => AccessControl::className(),
        'only' => ['logout'],
        'rules' => [
          [
            'actions' => ['logout'],
            'allow' => true,
            'roles' => ['@'],
          ],
        ],
      ],
      'verbs' => [
        'class' => VerbFilter::className(),
        'actions' => [
          'logout' => ['post'],
        ],
      ],
    ];
  }

  public function actions() {
    return [
      'error' => [
        'class' => 'yii\web\ErrorAction',
      ],
      'captcha' => [
        'class' => 'yii\captcha\CaptchaAction',
        'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
      ],
    ];
  }

  public function actionIndex() {
    return $this->render('index');
  }

  public function actionLogin() {
    if (!\Yii::$app->user->isGuest) {
      return $this->goHome();
    }

    $model = new LoginForm();
    if ($model->load(Yii::$app->request->post()) && $model->login()) {
      return $this->goBack();
    }
    return $this->render('login', [
        'model' => $model,
    ]);
  }

  public function actionLogout() {
    Yii::$app->user->logout();

    return $this->goHome();
  }

  public function actionContact() {
    $model = new ContactForm();
    if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
      Yii::$app->session->setFlash('contactFormSubmitted');

      return $this->refresh();
    }
    return $this->render('contact', [
        'model' => $model,
    ]);
  }

  public function actionAbout() {
    return $this->render('about');
  }

  public function actionUpload() {
    $model = new UploadForm();

    if (Yii::$app->request->isPost) {
      $model->messageFiles = UploadedFile::getInstances($model, 'messageFiles');
      if ($model->upload()) {
        print_r("success"); // file is uploaded successfully
        return;
      }
      else {
        print_r("Failed to upload.  Perhaps due to validation errors.");
        return;
      }
    }

    return $this->render('upload', ['model' => $model]);
  }

}
