<?php

namespace app\models;

use app\components\ConfigManager;
use app\models\baseHttp\Invoker;
use app\models\baseHttp\InvokerData;

abstract class AbstractModel {
  
  protected $configManager;
  protected $invokerData;
  protected $invoker;

  public function __construct() {
    $this->configManager = new ConfigManager();
    $this->invokerData = new InvokerData();
    $this->invoker = new Invoker();
    $this->init();
  }
  
  public function invoke(\InvokerData $invokerData) {
    return $this->invoker->invoke($invokerData);
  }

}
