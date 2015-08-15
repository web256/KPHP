<?php
/**
 *  Memcached.php
 * 许可声明：这是一个开源程序，未经许可不得将本软件的整体或任何部分用于商业用途及再发布。
 * ============================================================================
 * $Author: 王德康 (wangdk369@gmail.com) $
 * $Date: 2015-8-6 上午9:46:37 $
 * $Id$
 */

require FRAMEWORK_PATH.'/Cache.php';

class MemcachedCache extends AbstractCache implements ICache
{
    /**
     * memcached 实例
     * @var unknown_type
     */
    private static $mcd;

    /**
     * 初始化memcached
     */
    public function __construct()
    {
        if (!self::$mcd) {

            self::$mcd = new Memcached();

            // 开启一致性哈希算法，采用libketama
            self::$mcd->setOption(Memcached::OPT_DISTRIBUTION,Memcached::DISTRIBUTION_CONSISTENT);

            // 开启开兼容的libketama类行为
            self::$mcd->setOption(Memcached::OPT_LIBKETAMA_COMPATIBLE, true);

            // 开启存储压缩
            self::$mcd->setOption(Memcached::OPT_COMPRESSION, true);

            // 更多选项 http://php.net/manual/zh/memcached.constants.php

            // 添加服务器
            $this->addServer();
        }
    }

    /**
     * 添加服务到连接池
     * @throws KException
     */
    public function addServer()
    {
        $server_config_list = KConfig::get('mc');
        if ($server_config_list) {

            foreach ($server_config_list as $key => $val) {
                self::$mcd->addServer($val['mc_host'], $val['mc_port']);
                $this->check_server_status(self::$mcd->getStats());
            }
        } else {
            throw  new KException('MemcachedCache->__construct mc config not empty!');
        }
    }

    /**
     * 检查服务器连接池中的server是否可以用
     * @param unknown_type $server_status_list
     * @throws KException
     */
    public function check_server_status($server_status_list)
    {
        if ($server_status_list) {
            foreach ($server_status_list as $key => $val) {
                // 服务器连连接失败
                if ($val['pid'] == -1) {
                    throw  new KException('MemcachedCache->__construct addServer Fail '.$key);
                }
            }
        }
    }

    /**
     * 设置缓存数据
     * @param unknown_type $key     缓存key
     * @param unknown_type $value   缓存的数据
     * @param unknown_type $exprie  过去时间
     */
    public function set($key, $value, $exprie)
    {
        $exprie = $this->randomExprieTime($exprie);

        // memcached key 不能有空格
        $key = md5($key);
        self::$mcd->set($key,$value, $exprie);

        return self::$mcd->getResultCode() == Memcached::RES_SUCCESS;
    }

    /**
     * 获取缓存数据
     * @param unknown_type $key
    */
    public function get($key)
    {
        $key = md5($key);
        $value = self::$mcd->get($key);

        if (self::$mcd->getResultCode() == Memcached::RES_NOTFOUND ) {
            return false;
        }

        return $value;
    }

    /**
     * 删除缓存
     * @param unknown_type $key
    */
    public function delete($key)
    {
           $key = md5($key);
           self::$mcd->delete($key);
           return self::$mcd->getResultCode() == Memcached::RES_SUCCESS;
    }

    /**
     * 清空缓存
     * @param unknown_type $key
    */
    public function flush()
    {
       return self::$mcd->flush();
    }
}
?>