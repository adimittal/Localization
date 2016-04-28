<?php

use yii\db\Migration;

class m160428_000611_utf8 extends Migration {

  //make sure we're on utf8 schema
  public function up() {
    $sql = <<<HT
  ALTER SCHEMA `saas_loc`  DEFAULT CHARACTER SET utf8 ;
HT;
    $this->execute($sql);
  }

  public function down() {
    echo "m160428_000611_utf8 cannot be reverted.\n";

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
