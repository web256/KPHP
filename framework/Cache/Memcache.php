<?php
/**
 *  Memcache.php
 * 许可声明：这是一个开源程序，未经许可不得将本软件的整体或任何部分用于商业用途及再发布。
 * ============================================================================
 * $Author: 王德康 (wangdk369@gmail.com) $
 * $Date: 2015-8-5 上午10:26:01 $
 * $Id$
 */

require FRAMEWORK_PATH.'/Cache.php';

class MemcacheCache extends AbstractCache implements ICache
{

    /**
     * memcache 静态对象
     * @var unknown_type
     */
    public static $mc;

    public function __construct()
    {
        if (!self::$mc) {

            self::$mc = new Memcache;

            $mc_info = $this->getMcConfig();

            // 连接缓存主服务器
            $bool = self::$mc->connect($mc_info['mc_host'], $mc_info['mc_port']);
            if (!$bool) {

               throw  new KException('MemcacheCache->MemcacheCache mc connect Fail!');
               exit;
            }

            // 其他服务器增加到缓存池
            $this->addServer();
        }
    }

    /**
     * 关闭服务器
     */
    public function __destruct()
    {
       if (self::$mc) self::$mc->close();
    }

    /**
     *  添加其他缓存服务器
     *  如果增加失败，暂时没有提示
     */
    private function addServer()
    {
        // 获取其他Server配置信息
        $server_list = $this->getMcServerConfigList();

        if ($server_list) {
            foreach ($server_list as $key => $val) {

                // 添加其他缓存服务器，如果配置错误，这里不会有提示，使用的时候才会有提示
                self::$mc->addServer($val['mc_host'], $val['mc_port']);
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
        // 增加随机秒，分散缓存
        $exprie = $this->randomExprieTime($exprie);

        // 防止key 出现空格非法字符
        $key = md5($key);

        return self::$mc->set($key, $value,MEMCACHE_COMPRESSED, $exprie = 1800);
    }

    /**
     * 获取缓存数据
     * @param unknown_type $key
     */
    public function get($key)
    {
        // 防止key 出现空格非法字符
        $key = md5($key);

        return self::$mc->get($key);
    }

    /**
     * 删除指定的缓存元素
     * @param unknown_type $key
     */
    public function delete($key)
    {
        $key = md5($key);
        return self::$mc->delete($key, 0);
    }

    /**
     * 所有缓存元素全部失效
     */
    public function flush()
    {
        return self::$mc->flush();
    }
}
?>