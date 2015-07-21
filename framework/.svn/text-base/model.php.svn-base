<?php
/**
 *  model.php
 * 许可声明：这是一个开源程序，未经许可不得将本软件的整体或任何部分用于商业用途及再发布。
 * ============================================================================
 * $Author: 王德康 (wangdk369@gmail.com) $
 * $Date: 2015-7-21 上午11:02:51 $
 * $Id$
 * model 主要用能是分离让传入数据库参数更简化
 */
class model
{
    private static $db;
    private $table;

    public function __construct($table)
    {
        $this->table = $table;

        if (!self::$db) {
            require ROOT_PATH.'/framework/drives/'.DB_DRIVE.'.php';
            $class_name = DB_DRIVE.'Drive';
            self::$db = new $class_name(DB_HOST, DB_NAME,DB_USER, DB_PASS);
        }
    }

    /**
    * where 条件数组转为 where 语句
    * @param unknown_type $where
    * 支持形式：
    *     array('id'=>array(1,2,3,4,5))   => where id IN (1,2,3,4,5)
    *     array('id'=>array(1,2,3,4,5), 'id2' => array(1,23,4,56))  => where `id` IN (1,2,3,4,5) AND `id2` IN(1,23,4,56)
    *     array('id'=>1, 'name'='wangdk') => where `id` = 1 AND `name` = 'wangdk'
    *     array('id'=>1, 'OR name'='wangdk') => where `id` = 1 OR `name` = 'wangdk'
    *     array('id'=>1, 'name !='='wangdk') => where `id` = 1 and `name` != 'wangdk'
    *     array('id'=>1, 'OR name !='='wangdk') => where `id` = 1 OR `name` != 'wangdk'
    */
    private function arrayToWhere($where)
    {
            $sql = '';

            if (!$where) {
                throw new Exception('arrayToWhere args is not empty!');
            }

            if (!is_array($where)) {
               throw new Exception('arrayToWhere args is not array');
            }

             foreach ($where as $key => $value) {
                if ($sql) $sql.= ' AND ';
                if (is_array($value)) {
                    $sql .= '`'.$key.'` IN('.join(',', array_fill(0, count($value), '?')).') ';
                }
            }
            echo $sql;
      }

    /**
     * fields 数组转换为字段
     * @param unknown_type $fields
     */
    public function arrayToFields($fields)
    {

    }


    /**
     * 获取一条数据记录集
     * @param unknown_type $where
     * @param unknown_type $sql
     * @param unknown_type $fields
     */
    public function read($where, $sql, $fields)
    {
        return self::$db->getOne($this->table, $this->arrayToWhere($where), $this->arrayToFields($fields), $sql);
    }
}