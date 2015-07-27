<?php

define("DB_HOST", 'localhost');
define("DB_NAME", 'wangdk');
define("DB_USER", 'root');
define("DB_PASS", '123456');
define("DB_DIRVER", 'mysql');
define("DB_PORT", '3306');

require ROOT_PATH.'/framework/drives/mysqli.php';
$db = new mysqliDrive(DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT);
echo '</pre>';

// 新增
// $data['user_name'] = 'wangdk';
// $data['avatar']    = 'wangdk_avatar';

// $sql = 'insert into user('.join(',', array_keys($data)).') values('.join(',', array_fill(0, count($data), '?')).')';
// $params = array_values($data);
// $id = $db->create($sql, $params);
// print_r($id);

//单条查询
// $sql = 'select id , user_name from user where  1 = ?';
// $params = array(1);

// $data = $db->getAll($sql, $params);
// print_r($data);

//单条查询
// $sql = 'select id , user_name from user where  1 = ?';
// $params = array(1);

// $data = $db->getOne($sql, $params);
// print_r($data);



// 更新
// $sql = 'update user set user_name = ? where id = ?';
// $params = array('haha1233334',1);
// $data = $db->update($sql, $params);
// var_dump($data);

// // 删除
// $sql = 'delete from user where 1 = ?';
// $params = array(1);
// $data = $db->delete($sql, $params);
// print_r($data);


// $sql = 'select count(*) from user where 1= ?';
// $params = array(1);
// $data = $db->getTotal($sql, $params);
// print_r($data);