<?php

use yii\db\Migration;

class m160411_211032_create_transifex_projects extends Migration
{
    public function up()
    {
        $sql = <<<HT
        CREATE TABLE `transifex_projects` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT,
`customer_id` int(11) ,
`project_name` varchar(255) ,
`file` varchar(255) ,
`language` varchar(255) ,
`createdtime` TIMESTAMP NOT NULL DEFAULT 0,
`updatedtime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 ENGINE = INNODB;
HT;
    $this->execute($sql);
    }

    public function down()
    {
        $this->dropTable('transifex_projects');
    }
}
