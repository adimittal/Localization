<?php

use yii\db\Migration;

class m160411_211019_create_transaction extends Migration
{
    public function up()
    {
        $sql = <<<HT
        CREATE TABLE `transaction` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT,
`platform_key` varchar(255) ,
`string` varchar(255) ,
`filename` varchar(255) ,
`language` varchar(255) ,
`timestamp` varchar(255) ,
`createdtime` TIMESTAMP NOT NULL DEFAULT 0,
`updatedtime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (`id`)
) ENGINE = INNODB;
HT;
    $this->execute($sql);
    }

    public function down()
    {
        $this->dropTable('transaction');
    }
}
