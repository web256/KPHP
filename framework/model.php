<?php
/**
 *  model.php
 * 许可声明：这是一个开源程序，未经许可不得将本软件的整体或任何部分用于商业用途及再发布。
 * ============================================================================
 * $Author: 王德康 (wangdk369@gmail.com) $
 * $Date: 2015-7-21 上午11:02:51 $
 * $Id: model.php 381 2015-07-24 07:39:35Z wangdk $
 *
 */
class model
{
    private static $db;
    private $table;

    public function __construct($table)
    {
        $this->table = $table;

        if (!self::$db) {
            require ROOT_PATH.'/framework/drives/'.DB_DIRVER.'.php';
            $class_name = DB_DIRVER.'Drive';
            self::$db = new $class_name(DB_HOST, DB_NAME,DB_USER, DB_PASS, DB_PORT);
        }
    }


    /**
     * 新增一条记录
     * @param unknown_type $sql 绑定参数
     * @param unknown_type $params $params 绑定参数的值
     * $data['user_name'] = "aa";
     * $data['avatar']    = 'haa';
     */
    public function create($data)
    {
        if (!$data) return false;

        $key_list   = array_keys($data);
        $value_list = array_values($data);

        $sql = 'insert into '.$this->table.'('.join(',', $key_list).') values('.join(',', array_fill(0, count($value_list), '?')).')';
        return self::$db->create($sql, $value_list);
    }

    /**
     * 获取一条记录集
     * @param unknown_type $sql 绑定参数
     * @param unknown_type $params $params 绑定参数的值
     *
     * @example
     * $where = 'id = ?'
     * $params = array(1);
     *
     */
    public function read($where, $params, $fields = '*')
    {
        $sql = 'select '.$fields.' from '.$this->table.' '.$where.' limit 1';
        return self::$db->getOne($sql, $params);
    }

    /**
     * 获取多条记录集
     * @param unknown_type $sql 绑定参数
     * @param unknown_type $params $params 绑定参数的值
     */
    public function getList($where, $params, $fields = '*')
    {
        $sql = 'select '.$fields.' from '.$this->table.' '.$where;
        return self::$db->getAll($sql, $params);
    }

    /**
     * 获取记录数
     * @param unknown_type $sql 绑定参数
     * @param unknown_type $params $params 绑定参数的值
     */
    public function getTotal($where, $params, $fields = '*')
    {
        $sql = 'select count('.$fields.') from '.$this->table.' '.$where;
        return self::$db->getTotal($sql, $params);
    }


    /**
     * 更新记录集
     * @param unknown_type $sql 绑定参数
     * @param unknown_type $params $params 绑定参数的值
     */
    public function update($set, $where, $params = array())
    {
        if (!$where) return false;
        $sql = 'update '.$this->table.' '.$set.' '.$where;
        return self::$db->update($sql, $params);
    }

    /**
     * 删除记录集
     * @param unknown_type $sql 绑定参数
     * @param unknown_type $params $params 绑定参数的值
     */
    public function delete($where, $params = array())
    {
        if (!$where) return false;
        $sql = 'delete from '.$this->table.' '.$where;
        return self::$db->delete($sql, $params);
    }
}