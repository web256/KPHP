<?php
require ROOT_PATH."/framework/DB.php";

class mysqlDrive extends DBAbstract implements IDb
{
    private static $link;

    /**
     * 初始化mysql数据库
     * @param [type] $host           [localhost]
     * @param [type] $mysql_user     [description]
     * @param [type] $mysql_password [description]
     */
    public function __construct($host, $db_name, $db_user, $db_password, $prot)
    {
        if (!self::$link) {

            $host = empty($prot) ? $host : $host .':'.$prot;

            self::$link = mysql_connect($host, $db_user, $db_password);
            if (!self::$link) {
                $this->errorLog('mysql_connect:connect not mysql');
            }

            $this->setCharset('utf8');

            $this->selectDb($db_name);
        }
        return self::$link;
    }

    /**
     * 设置数据连接编码
     */
    public function setCharset($charset)
    {
        mysql_query("set names '".$charset."'", self::$link);
    }

    /**
     * 选择数据库
     * @return [type] [description]
     */
    private function selectDb($db_name)
    {
        if (!mysql_select_db($db_name)) {
            $this->errorLog('selectDb:mysql_select_db  db  fail !');
        }
    }


    /**
     * 绑定SQL参数
     * 主要作用:mysqli:parse方式防止注入
     * @param unknown_type $sql 必传参数
     * @param unknown_type $params  必川参数
     * @example
     *     select * from user where id = ?   array(1)
     *     select * from user where 1  = ?   array(1)
     */
    private function buildParams($sql, $params) {

        if (!strpos($sql, '?')) {
            throw new KException('buildParams:not find ? in sql ! '.$sql);
        }

        if (!$params ) {
            throw new KException('buildParams:params not empty!');
        }

        if (!is_array($params)) {
            throw new KException('buildParams:params must array!');
        }

        $index = 0;
        $pos = stripos($sql, '?');

        while( $pos!== false) {

            // 参数必须存在 // empty($params[$index]) 要允许0的存在
            if (!isset($params[$index])) {
                throw new KException('buildParams:params[$index] not empty!');
            }

            if (is_string($params[$index])) {
                // 过滤SQL非法字符，基础防注入
                $params[$index] = "'".mysql_real_escape_string($params[$index])."'";
            }

            // 替换?为参数
            $sql = substr_replace($sql, $params[$index], $pos, 1);

            // 查找下一个
            $pos = stripos($sql, '?');
            $index++;
        }

        return $sql;
    }


    /**
     * mysql 错误打印
     * @param  [type] $log [description]
     * @return [type]      [description]
     */
    private function errorLog($log)
    {
        throw new KException($log.' '.mysql_error());
    }

    /**
     * 执行Sql语句
     * @param  [type] $sql [description]
     * @return [type]      [description]
     */
    private function query($sql)
    {
        $res = mysql_query($sql, self::$link);
        if (!$res && mysql_errno(self::$link)) {
            throw new  KException('Query:invalid query: ' . mysql_error(self::$link).' '. $sql);
        }

        return $res;
    }

    /**
     * 查询记录多条
     * (non-PHPdoc)
     * @see IDb::getAll()
     */
    public function create($sql, $params)
    {
        $sql = $this->buildParams($sql, $params);
        $this->query($sql);
        return mysql_insert_id(self::$link);
    }

    /**
     * 查询记录多条
     * (non-PHPdoc)
     * @see IDb::getAll()
     */
    public function getAll($sql, $params)
    {
        $data = array();

        $sql = $this->buildParams($sql, $params);

        // 组合数据
        $result = $this->query($sql);
        if ($result) {

            while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
                $data[] = $row;
            }
        }

        mysql_free_result($result);
        return $data;
    }

    /**
     * 查询记录数
     * @param string $table
     * @param string $select
     * @param string $where
    */
    public function getTotal($sql, $params)
    {
        $row_num[0] = 0;
        $sql = $this->buildParams($sql, $params);

        $result = $this->query($sql);
        if ($result) {

            $row_num =  mysql_fetch_array($result, MYSQL_NUM);
            mysql_free_result($result);
        }

        return $row_num[0];
    }

    /**
     * 查询单条记录
     * @param string $table
     * @param string $select
     * @param string $where
     * @param array
    */
    public function getOne($sql, $params)
    {
        $info = array();

        $sql = $this->buildParams($sql, $params);
        $result = $this->query($sql);
        if ($result) {

            $info = mysql_fetch_array($result, MYSQL_ASSOC);
            mysql_free_result($result);
        }

        return $info;
    }

    /**
     * 更新记录
     * @param string $sql
    */
    public function update($sql, $params)
    {

        $rows = 0;

        $sql = $this->buildParams($sql, $params);
        $result = $this->query($sql);
        if ($result) {
            $rows = mysql_affected_rows(self::$link);
        }

        return $rows;
    }

    /**
     * 删除记录
     * @param string $sql
    */
    public function delete($sql, $params)
    {
        $rows = 0;

        $sql = $this->buildParams($sql, $params);
        $result = $this->query($sql);

        if($result) {
            $rows = mysql_affected_rows(self::$link);
        }

        return $rows;
    }

    /**
     * 关闭mysql连接
     */
    public function __destruct()
    {
        if (self::$link)  mysql_close(self::$link);
    }
}