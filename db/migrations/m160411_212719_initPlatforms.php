<?php

use yii\db\Migration;

class m160411_212719_initPlatforms extends Migration {

  public function up() {
    $datetime = date("Y-m-d H:i:s");
    Yii::$app->db->createCommand()->batchInsert('platform', ['name', 'createdtime', 'updatedtime'], [
      ['ioclient', $datetime, $datetime],
      ['ioclient-temm-android', $datetime, $datetime],
      ['iosliteclient', $datetime, $datetime],
      ['itsonportal', $datetime, $datetime],
      ['my-account', $datetime, $datetime],
    ])->execute();
  }

  public function down() {
    echo "m160411_212719_initPlatforms cannot be reverted.\n";

    return false;
  }

  /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
   */
}
