<?php
/**
 *  mysqli.php
 * 许可声明：这是一个开源程序，未经许可不得将本软件的整体或任何部分用于商业用途及再发布。
 * ============================================================================
 * $Author: 王德康 (wangdk369@gmail.com) $
 * $Date: 2015-7-23 上午9:47:06 $
 * $Id: mysqli.php 670 2015-08-03 06:35:24Z wangdk $
 */
require ROOT_PATH."/framework/DB.php";
class mysqliDrive extends DBAbstract implements IDB
{
    private static $link;
    private $stmt = null;

    public function __construct($host, $db_name, $db_user, $db_password, $port)
    {
        if (!self::$link) {

             // 创建 mysqli实例
             self::$link = new mysqli($host, $db_user, $db_password, $db_name, $port);

             if (self::$link->connect_errno) {
                 $this->errorLog('MySqli->__construct new mysqli Fail');
             }

             // 设置连接编码
             $this->setCharSet('utf8');

             // 选择数据库
             $this->selectDB($db_name);
        }
    }

    /**
     * 数据库关闭
     */
    public  function __destruct()
    {
        // 关闭数据库对象
        if (self::$link) self::$link->close();
    }

    /**
     * 设置字符集
     * @param unknown_type $charset
     */
    public function setCharSet($charset)
    {
        self::$link->set_charset($charset);
    }

    /**
     * 选择数据库
     * @param unknown_type $db_name
     */
    private function selectDB($db_name)
    {
        if (!self::$link->select_db($db_name)) {
            $this->errorLog('MySqli->selectDB:selected db fail!');
        }
    }

    /**
     * mysql 错误打印
     * @param  [type] $log [description]
     * @return [type]      [description]
     */
    private function errorLog($log)
    {
        throw new KException($log, mysql_error());
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
            $this->errorLog('MySqli->query: query exec Fail');
        }

        return $result;
    }

    /**
     * 传值修改为传引用
     * @param unknown_type $arr
     * @return multitype:unknown
     */
    private function makeValuesReferenced(&$arr)
    {
        $refs = array();
        foreach($arr as $key => $value) {
            $refs[$key] = &$arr[$key];
        }
        return $refs;
    }

    /**
     * 获取参数的类型并组合为字符串
     * @param unknown_type $params
     * @return string
     */
    private function getParamsType($params)
    {
        $type = '';

        if ($params) {
            foreach ($params as  $val) {
                if (is_string($val)) {
                    $type .= 's';
                } else if (is_integer($val)) {
                    $type .= 'i';
                } else if (is_double($val)) {
                    $type .= 'd';
                }
            }
        }

        return $type;
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
                if (is_string($v)) $array[$k] = self::$link->real_escape_string($v);
            }
        }

        return $array;
    }

    /**
     * 绑定SQL参数和变量
     * @param unknown_type $sql
     * @param unknown_type $params
     * @return boolean
     */
    public function bindParams($sql, $params)
    {
        // 过滤数据
        $params = $this->removeFilterBadChar($params);

        // 过滤参数
        $this->stmt = self::$link->prepare($sql);
        if (!$this->stmt) {
            $this->errorLog('MySqli->bindParams:prepare sql fail'. $sql);
        }

        $params_type = $this->getParamsType($params);
        if (!$params_type) return false;

        // 组合所需要的参数 0=>类型，1=>参数1...
        array_unshift($params, $params_type);

        // 绑定所有参数
        call_user_func_array(array($this->stmt, 'bind_param'), $this->makeValuesReferenced($params));

        return $this->stmt->execute();
    }


    /**
     * 新增记录
     * @param string $sql
     */
    public function create($sql, $params)
    {
        $this->bindParams($sql, $params);

        $insert_id = $this->stmt->insert_id;
        $this->stmt->close();

        return $insert_id;
    }

    /**
     * 获取记字段信息
     * @param unknown_type $stmt
     * @return boolean|multitype:NULL
     */
    private function getResultFields($stmt)
    {
        $fields = array();

        // 从预处理中返回结果集原数据
        $result = $stmt->result_metadata();
        if (!$result) {
            return false;
        }

        while ($field = $result->fetch_field()) {
            $fields[] = $field->name;
        }

        return $fields;
    }

    /**
     * 查询多条记录
     * @param string $sql
    */
    public function getAll($sql, $params)
    {
        $data = array();

        // 绑定参数
        $this->bindParams($sql, $params);

        // 获取当前记录集的字段
        $params = $this->getResultFields($this->stmt);
        $fields = $params;

        // 绑定结果集字段
        call_user_func_array(array($this->stmt, 'bind_result'), $this->makeValuesReferenced($params));

        // 获取关联数组数据
        while ($this->stmt->fetch()) {

            foreach ($params as $key => $val) {
                $row[$fields[$key]] = $val;
            }

            $data[] = $row;
        }

        // 关闭数据集
        $this->stmt->close();

        return $data;
    }

    /**
     * 查询记录数
     * @param string $sql
    */
    public function getTotal($sql, $params)
    {
        $total = 0;
        $data = array();

        // 绑定参数
        $this->bindParams($sql, $params);

        // 获取当前记录集的字段
        $params = $this->getResultFields($this->stmt);

        // 绑定结果集字段
        call_user_func_array(array($this->stmt, 'bind_result'), $this->makeValuesReferenced($params));

        // 获取索引数组
        $this->stmt->fetch();

        $total = isset($params[0]) ? $params[0] : 0;

        // 关闭数据集
        $this->stmt->close();

        return $total;
    }

    /**
     * 查询单条记录
     * @param string $sql
    */
    public function getOne($sql, $params)
    {
        $data = array();

        // 绑定参数
        $this->bindParams($sql, $params);

        // 获取当前记录集的字段
        $params = $this->getResultFields($this->stmt);
        $fields = $params;

        // 绑定结果集字段
        call_user_func_array(array($this->stmt, 'bind_result'), $this->makeValuesReferenced($params));

        // 获取关联数组数据
        while ($this->stmt->fetch()) {
            foreach ($params as $key => $val) {

                $row[$fields[$key]] = $val;
            }
            $data = $row;
        }

        // 关闭数据集
        $this->stmt->close();

        return $data;
    }

    /**
     * 更新记录
     * @param string $sql
    */
    public function update($sql, $params)
    {
        // 绑定参数
        $this->bindParams($sql, $params);

        $affected_rows = $this->stmt->affected_rows;

        // 关闭数据集
        $this->stmt->close();

        return $affected_rows;
    }

    /**
     * 删除记录
     * @param string $table
    */
    public function delete($sql, $params)
    {
        // 绑定参数
        $this->bindParams($sql, $params);

        $affected_rows = $this->stmt->affected_rows;

        // 关闭数据集
        $this->stmt->close();

        return $affected_rows;
    }
}
?>