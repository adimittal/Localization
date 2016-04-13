<?php

use yii\db\Migration;

class m160411_211039_create_translation extends Migration
{
    public function up()
    {
        $sql = <<<HT
        CREATE TABLE `translation` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT,
`string_id` int(11) ,
`platform_id` int(11) ,
`customer_id` int(11) ,
`language_code` varchar(255) ,
`translation` varchar(255) ,
`createdtime` TIMESTAMP NOT NULL DEFAULT 0,
`updatedtime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (`id`)
) ENGINE = INNODB;
HT;
    $this->execute($sql);
    }

    public function down()
    {
        $this->dropTable('translation');
    }
}
