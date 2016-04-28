<?php

use yii\db\Migration;

class m160411_210955_create_strings extends Migration
{
    public function up()
    {
        $sql = <<<HT
        CREATE TABLE `strings` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT,
`string` varchar(255) ,
`createdtime` TIMESTAMP NOT NULL DEFAULT 0,
`updatedtime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 ENGINE = INNODB;
HT;
    $this->execute($sql);
    }

    public function down()
    {
        $this->dropTable('strings');
    }
}
