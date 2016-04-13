<?php

use yii\db\Migration;

class m160411_220057_initCustomer extends Migration {

  public function up() {
    $datetime = date("Y-m-d H:i:s");
    Yii::$app->db->createCommand()->batchInsert('customer', ['name', 'createdtime', 'updatedtime'], [
      ['zact', $datetime, $datetime],
      ['tefmx', $datetime, $datetime],
      ['sapphire', $datetime, $datetime],
      ['sprint', $datetime, $datetime],
    ])->execute();
  }

  public function down() {
    echo "m160411_220057_initCustomer cannot be reverted.\n";

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
