<?php
interface IDb
{
    /**
     * 查询多条记录
     * @param string $table
     * @param string $select
     * @param string $where
     * @param string $sort
     * @param string $limit
     * @return array
     */
    public function getAll($table, $select, $where, $sort, $limit);

    /**
     * 查询记录数
     * @param string $table
     * @param string $select
     * @param string $where
     */
    public function getTotal($table, $where, $select);

    /**
     * 查询单条记录
     * @param string $table
     * @param string $select
     * @param string $where
     * @param string $sort
     * @param array
     */
    public function getOne($table, $where, $select, $sort);

    /**
     * 更新记录
     * @param string $table
     * @param string $select
     * @param string $where
     */
    public function update($table, $set, $where);

    /**
     * 删除记录
     * @param string $table
     * @param string $where
     */
    public function delete($table, $where);

}