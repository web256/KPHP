<?php

define("DB_HOST", 'localhost');
define("DB_NAME", 'wangdk');
define("DB_USER", 'root');
define("DB_PASS", '123456');
define("DB_DIRVER", 'mysql');
define("DB_PORT", '3306');

require ROOT_PATH.'/framework/model.php';
$db = new model('user');
echo '</pre>';

// 新增
// $data['user_name'] = 'wangdk';
// $data['avatar']    = 'wangdk_avatar';

// $sql = 'insert into user('.join(',', array_keys($data)).') values('.join(',', array_fill(0, count($data), '?')).')';
// $params = array_values($data);
// $id = $db->create($sql, $params);
// print_r($id);

// 单条查询
$sql = 'select * from user where  id = ? and user_name = ?';
$params = array(233334333, "wangdk or id = 1251906501");

$data = $db->read($sql, $params);
var_dump($data);


// 多条查询
// $sql = 'select * from user where 1 = ?';
// $params = array(1);
// $data = $db->getList($sql, $params);
// print_r($data);

// 更新
// $sql = 'update user set user_name = ? where id = ?';
// $params = array('wangdekanghaha',25);
// $data = $db->update($sql, $params);
// print_r($data);

// 删除
// $sql = 'delete from user where id = ?';
// $params = array(0);
// $data = $db->delete($sql, $params);
// print_r($data);


// $sql = 'select count(*) from user where 1= ?';
// $params = array(1);
// $data = $db->getTotal($sql, $params);
// print_r($data);



