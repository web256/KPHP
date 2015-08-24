<?php
echo '<pre>';


// 新增
// $data['user_name'] = 'wangdk';
// $data['avatar']    = '王德康';
// $id = _model('user')->create($data);
// print_r($id);


// 更新
// $set = 'set `user_name` = ?';
// $where = ' where id = ?';
// $params = array('王硕vv1d1',13);
// $data = _model('user')->update($set, $where, $params);
// print_r($data);

// 单条查询
// $sql = 'where  id = ?';
// $params = array(13);

// $data =_model('user')->read($sql, $params);
// print_r($data);


// 多条查询
// $sql = 'where 1 = ? order by id DESC LIMIT 3';
// $params = array(1);
// $data = _model('user')->getList($sql, $params);
// print_r($data);


// 删除
// $sql = 'where id = ?';
// $params = array(5);
// $data = _model('user')->delete($sql, $params);
// print_r($data);

// $sql = 'where 1= ?';
// $params = array(1);
// $data = _model('user')->getTotal($sql, $params);
// print_r($data);

print_r(CacheWrapper::$debug);
print_r(Model::$debug);




