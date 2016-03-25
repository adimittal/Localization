<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\components;

/**
 * Description of ConfigManager
 *
 * @author adityamittal
 */
class ConfigManager {
  
  public function getTransifexUrl() {
    return \Yii::$app->params['transifexUrl'];
  }
  
  public function getTransifexSettings() {
    return $settings = ['user' => "", 'password' => ""];
  }
  
  
  
}
