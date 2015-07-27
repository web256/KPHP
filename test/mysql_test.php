<?php

define("DB_HOST", 'localhost');
define("DB_NAME", 'wangdk');
define("DB_USER", 'root');
define("DB_PASS", '123456');
define("DB_DIRVER", 'mysql');
define("DB_PORT", '3306');

require ROOT_PATH.'/framework/drives/mysql.php';
$db = new mysqlDrive(DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT);
echo '<pre>';

// $sql = 'insert into user(`user_name`) values(?)';
// $params = array('wangdkhaha');
// $data = $db->create($sql, $params);
// print_r($data);


// $sql = 'select * from user where 1 = ?';
// $params = array(1);
// $data = $db->getOne($sql, $params);
// print_r($data);


// $sql = 'select * from user where 1 = ?';
// $params = array(1);
// $data = $db->getAll($sql, $params);
// print_r($data);

// $sql = 'select count(*) from user where 1 = ?';
// $params = array(1);
// $data = $db->getTotal($sql, $params);
// print_r($data);

// $sql = 'delete from user where id = ?';
// $params = array(8);
// $nums = $db->delete($sql, $params);
// print_r($nums);

// $sql = 'update user set user_name= ? where id = ? ';
// $params = array("wangd12123123k", 7);
// $nums = $db->update($sql, $params);
// print_r($nums);