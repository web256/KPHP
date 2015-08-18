<?php
/**
 * 扩展PHP支持,主从数据库,缓存的支持 ModelRes.php
 * 许可声明：这是一个开源程序，未经许可不得将本软件的整体或任何部分用于商业用途及再发布。
 * ============================================================================
 * $Author: 王德康 (wangdk369@gmail.com) $
 * $Date: 2015-8-3 下午8:07:48 $
 * $Id: ModelRes.php 1426 2015-08-18 06:27:48Z wangdk $
 * 缓存：用户读取记录会自动缓存，删除和更新会自动删除缓存，如果频繁更新缓存效果不明显
 *      如果用户区域会频繁更新，需要自定义缓存,自己来管理缓存
 */

class ModelRes
{

    /**
     * 表名
     * @var unknown_type
     */
    private $table;

    /**
     * mc 缓存对象
     * @var unknown_type
     */
    private $mc;

    /**
     * 主库连接
     * @var unknown_type
     */
    private $master_db;

    /**
     * 从库
     * @var unknown_type
     */
    private $slave_db;

    public function __construct($table)
    {
        global $mc;
        if ($mc) $this->mc = $mc;

        $this->setTable($table);
    }

    /**
     * 获取主库的链接
     */
    private function getMasterDB($table)
    {
        // 主库
        $this->master_db = new Model($this->table);
    }

    /**
     * 获取从库的链接
     */
    private function getSlaveDB($table)
    {
        // 从库
        $this->slave_db = new Model($this->table, 2);
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

        // 连接主库
        if (!$this->master_db) $this->getMasterDB($this->table);
        $insert_id = $this->master_db->create($data);

        if ($insert_id) {
            // 清空表缓存
            if ($this->mc) $this->mc->setPS()->delNS($this->table);
        }

        return $insert_id;
    }

    /**
     * 获取一条记录集
     * @param unknown_type $sql 绑定参数
     * @param unknown_type $params 绑定参数的值
     *
     * @example
     * $where = 'id = ?'
     * $params = array(1);
     */
    public function read($where, $params, $fields = '*')
    {

        // 获取缓存 key
        $sql = Model::getWhereToSql($fields, $where,$this->table, 1);
        $key = $this->getCacheKey($sql, $params);

        if ((!CACHE) && $this->mc) {

            $data = $this->mc->setPS('')->setNS($this->table)->getCache($key);
            if (!$data) {

                // 从库
                if (!$this->slave_db) $this->getSlaveDB($this->table);
                $data = $this->slave_db->read($where, $params, $fields);

                // 缓存24小时
                $this->mc->setPS('')->setNS($this->table)->setCache($key, $data, 60 * 60 * 24);

                return $data;
            }
            return $data;
        }

        // 开启不读取缓存，要更新之前缓存的内容
        if (!$this->slave_db) $this->getSlaveDB($this->table);
        $data = $this->slave_db->read($where, $params, $fields);

        if ($this->mc) {
            // 缓存24小时
            $this->mc->setPS('')->setNS($this->table)->setCache($key, $data, 60 * 60 * 24);
        }

        return $data;
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
        if ((!CACHE) && $this->mc) {

            $data = $this->mc->setPS('')->setNS($this->table)->getCache($key);
            if (!$data) {

                // 从库
                if (!$this->slave_db) $this->getSlaveDB($this->table);
                $data = $this->slave_db->getList($where, $params, $fields);

                // 缓存24小时
                $this->mc->setPS('')->setNS($this->table)->setCache($key, $data, 60 * 60 * 24);

                return $data;
            }
            return $data;
        }

        // 从库
        if (!$this->slave_db) $this->getSlaveDB($this->table);
        $data = $this->slave_db->getList($where, $params, $fields);

        if ($this->mc) {
            // 缓存24小时
            $this->mc->setPS('')->setNS($this->table)->setCache($key, $data, 60 * 60 * 24);
        }

        return $data;
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
        if ((!CACHE) && $this->mc) {

            $data = $this->mc->setPS('')->setNS($this->table)->getCache($key);
            if (!$data) {

                // 从库
                if (!$this->slave_db) $this->getSlaveDB($this->table);
                $data = $this->slave_db->getTotal($where, $params, $fields);

                // 缓存24小时
                $this->mc->setPS('')->setNS($this->table)->setCache($key, $data, 60 * 60 * 24);

                return $data;
            }
            return $data;
        }

        // 从库
        if (!$this->slave_db) $this->getSlaveDB($this->table);
        $data = $this->slave_db->getTotal($where, $params, $fields);

        return $data;
    }


    /**
     * 更新记录集
     * @param unknown_type $sql 绑定参数
     * @param unknown_type $params $params 绑定参数的值
     */
    public function update($set, $where, $params = array())
    {
        // 主库
        if (!$this->master_db) $this->getMasterDB($this->table);
        $result =  $this->master_db->update($set, $where, $params);

        if ($result) {
            // 更新表缓存
            if ($this->mc) $this->mc->setPS('')->delNS($this->table);
        }

        return $result;
    }

    /**
     * 删除记录集
     * @param unknown_type $sql 绑定参数
     * @param unknown_type $params $params 绑定参数的值
     */
    public function delete($where, $params = array())
    {
        // 主库
        if (!$this->master_db) $this->getMasterDB($this->table);
        $result =  $this->master_db->delete($where, $params);

        if ($result) {
            // 更新表缓存
            if ($this->mc) $this->mc->setPS('')->delNS($this->table);
        }

        return $result;
    }

}
?>