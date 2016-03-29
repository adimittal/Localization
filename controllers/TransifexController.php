<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers;

/**
 * Description of TransifexController
 *
 * @author adityamittal
 */
class TransifexController extends BaseController {
  
  private $model;

  public function init() {
    parent::init();
    $this->model = new \app\models\Transifex();
  }
  
  public function actionProjects() {
    return $this->model->getProjects();
  }
  
  
  
  
  
}
