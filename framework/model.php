<?php
/**
 *  支持主从数据库 封装了CURD 方法 model.php
 * 许可声明：这是一个开源程序，未经许可不得将本软件的整体或任何部分用于商业用途及再发布。
 * ============================================================================
 * $Author: 王德康 (wangdk369@gmail.com) $
 * $Date: 2015-7-21 上午11:02:51 $
 * $Id: Model.php 732 2015-08-03 11:58:03Z wangdk $
 *
 */
class Model
{
    /**
     * 当前使用的数据库实例，可能是主库，也可能是从库
     * @var unknown_type
     */
    private static $db;

    /**
     * 主库实例
     * @var unknown_type
     */
    private static $masterDb = null;
    /**
     * 从库实例
     * @var unknown_type
     */
    private static $slaveDb = null;

    /**
     * 当前操作的表名
     * @var unknown_type
     */
    private $table;

    /**
     * DEBUG 开启的时候，记录所有的SQL
     * @var unknown_type
     */
    public static $debug;

    /**
     * DEBUG 开启的时候，记录所有的SQL的数据
     * @var unknown_type
     */
    public static $debug_vals;

    /**
     * 初始化Model类
     * @param unknown_type $table  要操作的表名
     * @param unknown_type $connect_type  连接主库，还是从库，默认主库 1=主库，2=从库
     */
    public function __construct($table, $connect_type = 1)
    {
        // 表前缀，在ModelRes 设置
        $this->table = $table;

        $db_dirver = KConfig::get('db_dirver');
        if (!$db_dirver) throw  new KException('Model->__construct config db_dirver not empty!');

        $class_name = $db_dirver.'Drive';
        require_once FRAMEWORK_PATH.'/drives/'.$db_dirver.'.php';

        if ($connect_type  == 1) {
            // 主库
            self::$db = $this->masterDbConnect($class_name);
        } else if ($connect_type == 2) {
            // 从库
            self::$db = $this->slaveDbConnect($class_name);
        }
    }


    /**
     *  主库连接
     * @param unknown_type $class_name
     * @return unknown_type
     */
    private function masterDbConnect($class_name)
    {
        $config_info = array();

        if (!self::$masterDb) {

            // 主库
            $config_info = $this->getMasterConfig();

            // 检查数据完整性
            $this->checkConfigInfo($config_info);

            self::$masterDb = new $class_name($config_info['db_host'], $config_info['db_name'],$config_info['db_user'], $config_info['db_password'], $config_info['db_port']);
        }

        return self::$masterDb;
    }


    /**
     * 从库连接
     * @param unknown_type $class_name
     * @return unknown_type
     */
    private function slaveDbConnect($class_name)
    {
        $config_info = array();

        if (!self::$slaveDb) {

            // 主库
            $config_info = $this->getSlaveConfig();

            // 检查数据完整性
            $this->checkConfigInfo($config_info);

            self::$slaveDb = new $class_name($config_info['db_host'], $config_info['db_name'],$config_info['db_user'], $config_info['db_password'], $config_info['db_port']);
        }

        return self::$slaveDb;
    }

    /**
     * 获取主库的配置项目
     * @throws KException
     * @return Ambigous <string, multitype:>
     */
    private function getMasterConfig()
    {
        $db_config = KConfig::get('db');
        if (!isset($db_config[0])) {
            throw  new KException('Model->getMasterConfig not empty!');
        }

        return $db_config[0];
    }

    /**
     * 获取从库的配置项目
     * @throws KException
     * @return Ambigous <string, multitype:>
     */
    private function getSlaveConfig()
    {
        $slave_config_info = array();
        $db_config = KConfig::get('db');

        if (!isset($db_config[0])) {
            throw  new KException('Model->getSlaveConfig not empty!');
        }

        $count = count($db_config);
        if ($count > 1) {

           // 随机主库
           $index = mt_rand(1, $count - 1);
           $slave_config_info = $db_config[$index];

           return $slave_config_info;
        }

        // 只写了从库
        $slave_config_info = $db_config[0];
        return $slave_config_info;
    }

    /**
     * 检查数据库配置信息的完整性
     * @param unknown_type $config_info
     */
    private function checkConfigInfo($config_info)
    {

        if (!isset($config_info['db_host'])) {
            throw new KException('Model->checkConfigInfo not find db_host');
        }

        if (!isset($config_info['db_user'])) {
            throw new KException('Model->checkConfigInfo not find db_user');

        }

        if (!isset($config_info['db_password'])) {
            throw new KException('Model->checkConfigInfo not find db_password');

        }

        if (!isset($config_info['db_port'])) {
            throw new KException('Model->checkConfigInfo not find db_port');

        }
    }

    /**
     * 开启DEBUG的时候，记录所有的SQL
     * @param unknown_type $sql
     * @package unknown_type $params
     * @param unknown_type $data
     */
    public static function addDeBug($sql, $params, $data)
    {
       if (DEBUG)  {
           $info[$sql]['data']   = $data;
           $info[$sql]['params'] = $params;
           self::$debug[] = $info;
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

        $sql = 'insert into `'.$this->table.'`('.join(',', $this->addBackticksParams($key_list)).') values('.join(',', array_fill(0, count($value_list), '?')).')';
        $insert_id = self::$db->create($sql, $value_list);

        self::addDeBug($sql, $value_list, $insert_id);
        return $insert_id;
    }

    /**
     * 组合where条件
     * @param unknown_type $fields 要获取的字段
     * @param unknown_type $where  where 条件
     * @param unknown_type $table  表名
     * @param unknown_type $is_limit 是否要增加limit
     * @return string
     */
    public static function getWhereToSql($fields, $where, $table, $is_limit = 0)
    {
        if ($is_limit) {
            $sql = 'select '.$fields.' from `'.$table.'` '.$where.' limit 1';
        } else {
            $sql = 'select '.$fields.' from `'.$table.'` '.$where;
        }

        return $sql;
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
        $sql = self::getWhereToSql($fields, $where, $this->table, 1);
        $info = self::$db->getOne($sql, $params);

        self::addDeBug($sql, $params, $info);
        return $info;
    }

    /**
     * 获取多条记录集
     * @param unknown_type $sql 绑定参数
     * @param unknown_type $params $params 绑定参数的值
     */
    public function getList($where, $params, $fields = '*')
    {
        $sql  = self::getWhereToSql($fields, $where, $this->table);
        $list = self::$db->getAll($sql, $params);

        self::addDeBug($sql, $params, $list);
        return $list;
    }

    /**
     * 获取记录数，组合total where条件
     * @param unknown_type $fields 要获取的字段
     * @param unknown_type $where  where 条件
     * @return string
     */
    public static function getWhereToTotalSql($fields, $where, $table)
    {
        $sql = 'select count('.$fields.') from `'.$table.'` '.$where;
        return $sql;
    }


    /**
     * 获取记录数
     * @param unknown_type $sql 绑定参数
     * @param unknown_type $params $params 绑定参数的值
     */
    public function getTotal($where, $params, $fields = '*')
    {
        $sql = self::getWhereToTotalSql($fields, $where, $this->table);
        $info = self::$db->getTotal($sql, $params);

        self::addDeBug($sql, $params, $info);
        return $info;
    }

    /**
     * 更新记录集
     * @param unknown_type $sql 绑定参数
     * @param unknown_type $params $params 绑定参数的值
     */
    public function update($set, $where, $params = array())
    {
        if (!$where) return false;
        $sql = 'update `'.$this->table.'` '.$set.' '.$where;
        $result = self::$db->update($sql, $params);

        self::addDeBug($sql, $params, $result);
        return $result;
    }

    /**
     * 删除记录集
     * @param unknown_type $sql 绑定参数
     * @param unknown_type $params $params 绑定参数的值
     */
    public function delete($where, $params = array())
    {
        if (!$where) return false;
        $sql = 'delete from `'.$this->table.'` '.$where;
        $result = self::$db->delete($sql, $params);

        self::addDeBug($sql, $params, $result);
        return $result;
    }

    /**
     * 为必要的SQL添加反引号
     * @param unknown_type $data
     */
    public function addBackticksParams($data)
    {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = '`'.$v.'`';
            }
        } else if (is_string($data)) {
            $data = '`'.$data.'`';
        }

        return $data;
    }
}