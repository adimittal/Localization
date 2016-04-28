<?php

namespace app\components;

use app\components\ConfigManager;
use yii\di\Instance;
use yii\db\Connection;

/**
 * Description of DB
 *
 * @author adityamittal
 */
class DB extends Connection {

  private $cm;
  public $db = 'db';

  public function init() {
    parent::init();
    $this->cm = new ConfigManager();
    $this->db = Instance::ensure($this->db, Connection::className());
    $this->db->getSchema()->refresh();
  }

  /**
   * Executes a SQL statement.
   * This method executes the specified SQL statement using [[db]].
   * @param string $sql the SQL statement to be executed
   * @param array $params input parameters (name => value) for the SQL execution.
   * See [[Command::execute()]] for more details.
   */
  public function execute($sql, $params = []) {
    echo "    > execute SQL: $sql ... with params:".print_r($params,true)." ... ";
    $time = microtime(true);
    $this->db->createCommand($sql)->bindValues($params)->execute();
    echo ' done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)<br />\n";
  }

}
