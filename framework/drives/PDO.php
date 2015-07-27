<?php
/**
 *  mysqli.php
 * 许可声明：这是一个开源程序，未经许可不得将本软件的整体或任何部分用于商业用途及再发布。
 * ============================================================================
 * $Author: 王德康 (wangdk369@gmail.com) $
 * $Date: 2015-7-23 上午9:47:06 $
 * $Id: pdo.php 441 2015-07-24 11:51:37Z wangdk $
 */
require ROOT_PATH."/framework/DB.php";
class PDODrive extends DBAbstract implements IDB
{
    private static $link;
    private $PDOStatement = null;

    public function __construct($host, $db_name, $db_user, $db_password, $port = 3306)
    {
        if (!self::$link) {

             $dns = 'mysql:host='.$host.';port='.$port.';dbname='.$db_name;
             // $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'); // php5.36以上支持
             $options = array();

             try {
                 // 创建 PDO 实例
                 self::$link = new PDO($dns, $db_user, $db_password, $options);
             } catch(PDOException $e) {
                 $this->errorLog('PDO->__construct connect failed: ' . $e->getMessage());
             }

             // 设置连接编码
             $this->setCharSet('utf8');

        }
    }

    /**
     * 数据库关闭
     */
    public  function __destruct()
    {
        // 关闭PDO对象
        if (self::$link) self::$link=null;
    }

    /**
     * 设置字符集
     * @param unknown_type $charset
     */
    public function setCharSet($charset)
    {
        self::$link->query("set names '{$charset}'");
    }

    /**
     * mysql 错误打印
     * @param  [type] $log [description]
     * @return [type]      [description]
     */
    private function errorLog($log)
    {
        throw new Exception($log. self::$link->errorInfo());
    }

    /**
     * mysqli不推荐使用当前方法了
     * @param unknown_type $sql
     * @return mixed
     */
    private function query($sql)
    {
        $result = self::$link->query($sql);
        if (self::$link->errno) {
            $this->errorLog('PDO->query: query exec Fail');
        }

        return $result;
    }

    /**
     * 预编译SQL，并绑定参数值
     * @param unknown_type $sql
     * @param unknown_type $params
     */
    private function bindParams($sql, $params)
    {
        // 预编译的SQL，必须存在?
        if (!strpos($sql, '?')) {
            throw new Exception('PDO->buildParams:not find ? in sql ! '.$sql);
        }

        // 预编译的参数必须是数组
        if (!$params ) {
            throw new Exception('PDO->buildParams:params not empty!');
        }

        if (!is_array($params)) {
            throw new Exception('PDO->buildParams:params must array!');
        }

        $this->PDOStatement = self::$link->prepare($sql);
        return $this->PDOStatement->execute($params);
    }


    /**
     * 过滤 Filter 中的非法字符
     * @param mixed $array
     * @return mixed
     */
    private function removeFilterBadChar($array)
    {
        if ($array) {
            // mysql_real_escape_string
            foreach ($array as $k => $v) {
                if (is_string($v)) $array[$k] = addslashes($v);
            }
        }

        return $array;
    }

    /**
     * 新增记录
     * @param string $sql
     */
    public function create($sql, $params)
    {
        $this->PDOStatement = self::$link->prepare($sql);

        $result = $this->PDOStatement->execute($params);
        if (!$result) {
            $this->errorLog('PDO->create: insert data fail '.$sql);
        }

        return self::$link->lastInsertId();
    }

    /**
     * 查询多条记录
     * @param string $sql
    */
    public function getAll($sql, $params)
    {
        $data = array();

        $this->PDOStatement = self::$link->prepare($sql);

        $result = $this->PDOStatement->execute($params);
        if (!$result) {
            $this->errorLog('PDO->create: getOne data fail '.$sql);
        }

        $data = $this->PDOStatement->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }

    /**
     * 查询记录数
     * @param string $sql
    */
    public function getTotal($sql, $params)
    {
        $data[0] = 0;

        $result = $this->bindParams($sql, $params);
        if (!$result) {
            $this->errorLog('PDO->getTotal fail '.$sql);
        }

        $data = $this->PDOStatement->fetch(PDO::FETCH_NUM);
        return $data[0];
    }

    /**
     * 查询单条记录
     * @param string $sql
    */
    public function getOne($sql, $params)
    {

        $data = array();

        $result = $this->bindParams($sql, $params);
        if (!$result) {
            $this->errorLog('PDO->create: getOne data fail '.$sql);
        }

        $data = $this->PDOStatement->fetch(PDO::FETCH_ASSOC);
        return $data;
    }

    /**
     * 更新记录
     * @param string $sql
    */
    public function update($sql, $params)
    {
        $rows = 0;

        $result = $this->bindParams($sql, $params);
        if (!$result) {
            $this->errorLog('PDO->update: update data fail '.$sql);
        }

        $rows = $this->PDOStatement->rowCount();
        return $rows;
    }

    /**
     * 删除记录
     * @param string $table
    */
    public function delete($sql, $params)
    {
        $rows = 0;

        $result = $this->bindParams($sql, $params);
        if (!$result) {
            $this->errorLog('PDO->delete: delete fail '.$sql);
        }

        $rows = $this->PDOStatement->rowCount();

        return $rows;
    }
}
?>