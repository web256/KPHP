<?php
/**
 * 缓存接口类 Cache.php
 * 许可声明：这是一个开源程序，未经许可不得将本软件的整体或任何部分用于商业用途及再发布。
 * ============================================================================
 * $Author: 王德康 (wangdk369@gmail.com) $
 * $Date: 2015-8-5 上午10:05:11 $
 * $Id: Cache.php 937 2015-08-06 05:00:51Z wangdk $
 */
Interface ICache
{
    /**
     * 设置缓存数据
     * @param unknown_type $key     缓存key
     * @param unknown_type $value   缓存的数据
     * @param unknown_type $exprie  过去时间
     */
    public function set($key, $value, $exprie);

    /**
     * 获取缓存数据
     * @param unknown_type $key
     */
    public function get($key);

    /**
     * 删除缓存
     * @param unknown_type $key
     */
    public function delete($key);

    /**
     * 清空缓存
     * @param unknown_type $key
     */
    public function flush();
}

abstract  class AbstractCache
{
    /**
     * 获取mc配置信息
     * @throws KException
     * @return Ambigous <>
     */
    public function getMcConfig()
    {
        $mc_config_list = KConfig::get('mc');
        if (!isset($mc_config_list[0])) {

            throw new KException('MemcacheCache->getMcConfig config_Info not empty!');
        }

        return $mc_config_list[0];
    }

    /**
     * 获取mc 其他Server配置信息
     * @throws KException
     * @return Ambigous <>
     */
    public function getMcServerConfigList()
    {
        $mc_config_list = KConfig::get('mc');
        if (!isset($mc_config_list)) {

            return false;
        }

        array_shift($mc_config_list);
        return $mc_config_list;
    }

    /**
     * 缓存时间随机分布
     * @param unknown_type $exprie
     * @return number
     */
    public function randomExprieTime($exprie)
    {
        if ($exprie) $exprie += mt_rand(0, 60);
        return $exprie;
    }
}
?>