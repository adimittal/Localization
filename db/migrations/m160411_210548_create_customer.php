<?php

use yii\db\Migration;

class m160411_210548_create_customer extends Migration {

  public function up() {
    $sql = <<<HT
        CREATE TABLE `customer` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT,
`name` varchar(255) ,
`createdtime` TIMESTAMP NOT NULL DEFAULT 0,
`updatedtime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (`id`)
) ENGINE = INNODB;
HT;
    $this->execute($sql);
  }

  public function down() {
    $this->dropTable('customer');
  }

}
