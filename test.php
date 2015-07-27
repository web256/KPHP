<?php
define("DB_HOST", 'localhost');
define("DB_NAME", 'wangdk');
define("DB_USER", 'root');
define("DB_PASS", '123456');
define("DB_DIRVER", 'PDO');
define("DB_PORT", '3306');

require ROOT_PATH.'/framework/model.php';
$db = new model('user');
echo '<pre>';

var_dump($db);

// 新增
// $data['user_name'] = 'wangdk';
// $data['avatar']    = '王德康';
// $id = $db->create($data);
// print_r($id);

// 单条查询
// $sql = 'where  id = ?';
// $params = array(13);

// $data = $db->read($sql, $params);
// print_r($data);


// 多条查询
// $sql = 'where 1 = ? order by id DESC';
// $params = array(1);
// $data = $db->getList($sql, $params);
// print_r($data);

// 更新
// $set = 'set user_name = ?';
// $where = ' where id = ?';
// $params = array('wangdekanghah2342343a',85);
// $data = $db->update($set,$where, $params);
// print_r($data);

// 删除
// $sql = 'where id = ?';
// $params = array(85);
// $data = $db->delete($sql, $params);
// print_r($data);


$sql = 'where 1= ?';
$params = array(1);
$data = $db->getTotal($sql, $params);
print_r($data);