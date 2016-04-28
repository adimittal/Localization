<?php

use yii\db\Migration;

class m160411_210941_create_keys extends Migration
{
    public function up()
    {
        $sql = <<<HT
        CREATE TABLE `keys` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT,
`string_id` int(11) ,
`platform_id` int(11) ,
`translation_id` int(11) ,
`key` varchar(255) ,
`uxd_context` varchar(255) ,
`createdtime` TIMESTAMP NOT NULL DEFAULT 0,
`updatedtime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 ENGINE = INNODB;
HT;
    $this->execute($sql);
    }

    public function down()
    {
        $this->dropTable('keys');
    }
}
