<?php

define("DB_HOST", 'localhost');
define("DB_NAME", 'wangdk');
define("DB_USER", 'root');
define("DB_PASS", '123456');
define("DB_DIRVER", 'mysql');

require 'framework/model.php';
$db = new model('user');

var_dump($db);
$table = 'user';
$select = '*';
$where  = 'where 1=1';
$sort = ' order by id desc';
$limit = '';

//$data = $db->getAll($table, $select, $where, $sort, $limit);
//print_r($data);

//$num = $db->getTotal($table, $select, $where);
// var_dump($num);

echo '<pre>';
// $info = $db->getOne($table, $where, $select, $sort);
// print_r($info);


// $table = 'user';
// $set = "last_time = '2012-12-12 00:00:00'";
// $where  = '';

// $nums = $db->update($table, $set, $where);
// echo $nums;
// echo '<br>';
//


// $table = 'user';
// $where  = ' where id = 233333 or id = 2334333';

// $nums = $db->delete($table, $where);
// echo $nums;
// echo '<br>';
//

// $table = 'user';
// $where  = ' where id = 2333334343xxx3 or id = 2334333';

// $nums = $db->delete($table, $where);
//echo $nums;
echo '<br>';



