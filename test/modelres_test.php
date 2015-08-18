<?php

$db = new ModelRes('user');
echo '<pre>';

// 新增
// $data['user_name'] = 'wangdk';
// $data['avatar']    = '王德康';
// $id = $db->create($data);
// print_r($id);


// 更新
$set = 'set user_name = ?';
$where = ' where id = ?';
$params = array('王硕vv112221',13);
$data = $db->update($set,$where, $params);
print_r($data);

// 单条查询
$sql = 'where  id = ?';
$params = array(13);

$data = $db->read($sql, $params);
print_r($data);


// 多条查询
// $sql = 'where 1 = ? order by id DESC LIMIT 3';
// $params = array(1);
// $data = $db->getList($sql, $params);
// print_r($data);

// 更新
// $set = 'set user_name = ?';
// $where = ' where id = ?';
// $params = array('王硕vv',13);
// $data = $db->update($set,$where, $params);
// print_r($data);


// $sql = 'where  id = ?';
// $params = array(13);
// $data = $db->read($sql, $params);
// print_r($data);

// 删除
// $sql = 'where id = ?';
// $params = array(4);
// $data = $db->delete($sql, $params);
// print_r($data);

// $sql = 'where 1= ?';
// $params = array(1);
// $data = $db->getTotal($sql, $params);
// print_r($data);


print_r(CacheWrapper::$debug);
print_r(Model::$debug);
?>