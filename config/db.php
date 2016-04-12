<?php

$host = getenv('mysql_host');
$user = getenv('mysql_user');
$pass = getenv('mysql_pass');


return [
    'class' => 'yii\db\Connection',
    'dsn' => "mysql:host=$host;dbname=saas_loc",
    'username' => $user,
    'password' => $pass,
    'charset' => 'utf8',
];
