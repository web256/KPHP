<?php
/**
 * 扩展PHP支持,主从数据库,缓存的支持 ModelRes.php
 * 许可声明：这是一个开源程序，未经许可不得将本软件的整体或任何部分用于商业用途及再发布。
 * ============================================================================
 * $Author: 王德康 (wangdk369@gmail.com) $
 * $Date: 2015-8-3 下午8:07:48 $
 * $Id: ModelRes.php 838 2015-08-04 08:21:44Z wangdk $
 */

class ModelRes
{

    private $table;

    public function __construct($table)
    {
        $this->setTable($table);
    }

    /**
     * 保存表名，支持表前缀
     * @param unknown_type $table
     */
    private function setTable($table)
    {
        $prefix = KConfig::get('db_table_prefix');
        $this->table = $prefix.$table;
    }

    /**
     * 获取缓存一条SQL的KEY
     *
     * @param unknown_type $sql
     * @param unknown_type $params
     */
    private function getCacheKey($sql, $params = array())
    {
        $params['sql'] = $sql;
        return serialize($params);
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
        // 主库
        $model = new Model($this->table);
        return $model->create($data);
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

        // 获取缓存 key
        $sql = Model::getWhereToSql($fields, $where,$this->table, 1);
        $key = $this->getCacheKey($sql, $params);
        // 要缓存啦

        // 从库
        $model = new Model($this->table, 2);
        return $model->read($where, $params, $fields);
    }

    /**
     * 获取多条记录集
     * @param unknown_type $sql 绑定参数
     * @param unknown_type $params $params 绑定参数的值
     */
    public function getList($where, $params, $fields = '*')
    {
        // 获取缓存 key
        $sql = Model::getWhereToSql($fields, $where, $this->table, 1);
        $key = $this->getCacheKey($sql, $params);
        // 要缓存啦

        // 从库
        $model = new Model($this->table, 2);
        return $model->getList($where, $params, $fields);
    }

    /**
     * 获取记录数
     * @param unknown_type $sql 绑定参数
     * @param unknown_type $params $params 绑定参数的值
     */
    public function getTotal($where, $params, $fields = '*')
    {
        // 获取缓存 key
        $sql = Model::getWhereToTotalSql($fields, $where, $this->table, 1);
        $key = $this->getCacheKey($sql, $params);
        // 要缓存啦

        // 从库
        $model = new Model($this->table, 2);
        return $model->getTotal($where, $params, $fields);
    }


    /**
     * 更新记录集
     * @param unknown_type $sql 绑定参数
     * @param unknown_type $params $params 绑定参数的值
     */
    public function update($set, $where, $params = array())
    {
        // 主库
        $model = new Model($this->table);
        return $model->update($set, $where, $params);
    }

    /**
     * 删除记录集
     * @param unknown_type $sql 绑定参数
     * @param unknown_type $params $params 绑定参数的值
     */
    public function delete($where, $params = array())
    {
        // 主库
        $model = new Model($this->table);
        return $model->delete($where, $params);
    }

}
?>