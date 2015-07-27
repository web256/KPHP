<?php
interface IDb
{
    /**
     * 新增记录
     * @param string $sql
     */
    public function create($sql, $params);

    /**
     * 查询多条记录
     * @param string $sql
     */
    public function getAll($sql, $params);

    /**
     * 查询记录数
     * @param string $sql
     */
    public function getTotal($sql, $params);

    /**
     * 查询单条记录
     * @param string $sql
     */
    public function getOne($sql, $params);

    /**
     * 更新记录
     * @param string $sql
     */
    public function update($sql, $params);

    /**
     * 删除记录
     * @param string $table
     */
    public function delete($sql, $params);

}

abstract class  DBAbstract
{


}