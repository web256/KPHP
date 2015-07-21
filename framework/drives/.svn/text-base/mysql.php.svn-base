<?php
require ROOT_PATH."/framework/interface.php";

class mysqlDrive implements IDb
{
    private static $link;

    /**
     * 初始化mysql数据库
     * @param [type] $host           [localhost]
     * @param [type] $mysql_user     [description]
     * @param [type] $mysql_password [description]
     */
    public function __construct($host, $db_name, $db_user, $db_password)
    {
        if (!self::$link) {

            self::$link = mysql_connect($host, $db_user, $db_password);
            if (!self::$link) {
                 $this->error_log(' Could not connect mysql');
            }

            $this->selectDb($db_name);
            $this->setCharset();
        }
        return self::$link;
    }

    /**
     * 设置数据连接编码
     */
    private function setCharset()
    {
        mysql_query("set names 'utf8'", self::$link);
    }

    /**
     * 选择数据库
     * @return [type] [description]
     */
    private function selectDb($db_name)
    {
        if (!mysql_select_db($db_name)) {
            $this->error_log(' mysql_select_db  db  fail !');
        }
    }

    /**
     * mysql 错误打印
     * @param  [type] $log [description]
     * @return [type]      [description]
     */
    private function error_log($log)
    {
        throw new Exception($log, mysql_error());
    }

    /**
     * 执行Sql语句
     * @param  [type] $sql [description]
     * @return [type]      [description]
     */
    private function query($sql)
    {
        return mysql_query($sql, self::$link);
    }

    /**
     * 查询记录多条
     * (non-PHPdoc)
     * @see IDb::getAll()
     */
    public function getAll($table, $select, $where, $sort, $limit)
    {
        $data = array();

        $sql = " select {$select} from {$table} {$where} {$sort} {$limit}";

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
    public function getTotal($table, $select, $where)
    {
        $row_num = 0;

        $sql =  " select {$select} from {$table} {$where}";

        $result = $this->query($sql);
        if ($result) {

            $row_num =  mysql_num_rows($result);
            mysql_free_result($result);
        }

        return $row_num;
    }

    /**
     * 查询单条记录
     * @param string $table
     * @param string $select
     * @param string $where
     * @param array
    */
    public function getOne($table, $where, $select, $sort)
    {
        $info = array();

        $sql = " select {$select} from {$table} {$where} {$sort} limit 1";
        $result = $this->query($sql);

        if ($result) {
            $info = mysql_fetch_array($result, MYSQL_ASSOC);
            mysql_free_result($result);
        }

        return $info;
    }

    /**
     * 更新记录
     * @param string $table
     * @param string $select
     * @param string $where
    */
    public function update($table, $set, $where)
    {

        $rows = 0;
        $sql = "update {$table} set {$set} {$where}";

        $result = $this->query($sql);
        if ($result) {
            $rows = mysql_affected_rows(self::$link);
        }

        return $rows;
    }

    /**
     * 删除记录
     * @param string $table
     * @param string $where
    */
    public function delete($table, $where)
    {
        $rows = 0;

        $sql = "delete from {$table} {$where} ";
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