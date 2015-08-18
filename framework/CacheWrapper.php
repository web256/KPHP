<?php
/**
 * 封装了Cache层 CacheWrapper.php
 * 许可声明：这是一个开源程序，未经许可不得将本软件的整体或任何部分用于商业用途及再发布。
 * ============================================================================
 * $Author: 王德康 (wangdk369@gmail.com) $
 * $Date: 2015-8-7 下午5:07:15 $
 * $Id: CacheWrapper.php 1266 2015-08-17 04:24:49Z wangdk $
 *
 * 缓存原理，类似构建数据库和表
 *     1、ns_project  项目key
 *     2、ns_project_val 项目val
 *     3、ns_table  表key
 *     4、ns_table_val  表val
 *     5、sql key
 * 策略：
 *     ns_project_val 对应的缓存存储着所有相关表的缓存Key, 也就是ns_table => ns_table_val
 *     ns_table_val   对应的缓存存储这所有真正缓存的Key, sql => data
 *
 *     清空表缓存可以直接更新user表即可
 *     清空项目缓存直接更新project即可
 *
 * @example
 *    test项目中，user表 wangdk 键 存储的值是 hahawangdkheihei
 *    $mc->setPS('test')->setNS('user')->setCache('wangdk', "hahawangdkheihei", 180)
 *
 *    test项目中，goods表 wangdk 键 存储的值是 wangdekang88888
 *    $mc->setPS('test')->setNS('goods')->setCache('wangdk', "wangdekang88888", 180)
 *
 *    删除test项目中 user表下 wangdk键对应的缓存
 *    $mc->setPS('test')->setNs('user')->deleteCache('wangdk');

 *    删除test项目中 user表下所有缓存
 *    $mc->setPS('test')->delNS('user');
 *
 *    删除test项目中所有缓存
 *    $mc->delPS('test');

 *    获取test项目中,goods表中,wangk 键的缓存数据
 *    $mc->setPS('test')->setNS('goods')->getCache('wangdk')
 *
 *     获取test项目中,user表中,wangk 键的缓存数据
 *    $mc->setPS('test')->setNS('user')->getCache('wangdk')
 */

class CacheWrapper
{

    /**
     * 缓存对象 memcache memcached
     * @var string
     *
     */
    private static $cache;

    /**
     * 项目key
     * @var string
     */
    private $ns_project;

    /**
     * 项目值val
     * @var string
     */
    private $ns_project_val;

    /**
     * 表key
     * @var string
     */
    private $ns_table;

    /**
     * 表值value
     * @var string
     */
    private $ns_table_val;

    /**
     * 开启DEBUG时候，存储缓存key和value
     * @var string
     */
    public static $debug;

    /**
     * 根据配置项目选择缓存驱动
     */
    public function __construct()
    {
        if (!self::$cache) {

            $mc_dirver = KConfig::get('mc_dirver');
            if (!$mc_dirver) {
                throw new KException('CacheWrapper->__construct mc_dirver is not empty!');
            }

            require FRAMEWORK_PATH .'/Cache/'.ucfirst($mc_dirver).'.php';

            $class_name = $mc_dirver.'Cache';
            self::$cache = new $class_name;
        }
    }

    /**
     * 设置表命名空间
     * @param unknown_type $table_name
     */
    public function setNS($table_name)
    {
       $project_list = array();

       // 表 key
       $this->ns_table = $table_name;

       // 表 value，这个value会作为和sql组合存储
       $this->ns_table_val = $table_name.'_'.$this->getTime();

       if (!$this->ns_project_val) return $this;

        // 读取表空间值，如果表存在就读出来，没有就存储，并赋值给变量 $this->ns_table_val
        $data = self::$cache->get($this->ns_project_val);
        if ($data) {

            $project_list = unserialize($data);

            if (array_key_exists($this->ns_table, $project_list)) {
                $this->ns_table_val = $project_list[$this->ns_table];

            } else {
                $project_list[$this->ns_table] = $this->ns_table_val;
                self::$cache->set($this->ns_project_val, serialize($project_list), 0);
            }
        } else {

            $project_list[$this->ns_table] = $this->ns_table_val;
            self::$cache->set($this->ns_project_val, serialize($project_list), 0);
        }

        return $this;
    }

    /**
     * 删除表名空间
     * @param unknown_type $table_name
     */
    public function delNS($table_name)
    {
        $project_list = array();

        // key
        $this->ns_table = $table_name;

        if (!$this->ns_project) return false;

        // 获取项目空间下值
        $data = self::$cache->get($this->ns_project_val);

        if ($data) {
            $project_list = unserialize($data);

            // 存在
            if (array_key_exists($this->ns_table, $project_list)) {

                // 删除存储
                unset($project_list[$this->ns_table]);
                self::$cache->set($this->ns_project_val, serialize($project_list), 0);

            }
        }

        return true;
    }


    private function getTime()
    {
        return date('Y-m-d H:i:s', time()).mt_rand(1, 10000);
    }


    /**
     * 获取项目空间
     * @param unknown_type $ps_name
     */
    public function getPS($ps_name = '')
    {
        if (!$ps_name) {
            $ps_name = KConfig::get('project_name');
        }

        $key = $ps_name;
        return $key;
    }

    /**
     * 设置表命名空间
     * @param unknown_type $ps_name
     */
    public function setPS($ps_name = '')
    {
        $this->ns_project     = $this->getPS($ps_name);

        $data = self::$cache->get($this->ns_project);
        if (!$data) {

            $this->ns_project_val = $this->ns_project.'_'.$this->getTime();
            self::$cache->set($this->ns_project, $this->ns_project_val, 0);

        } else {
            $this->ns_project_val = $data;
        }


        return $this;
    }

    /**
     * 清空表命名空间下所有缓存
     * @param unknown_type $ps_name
     */
    public function delPS($ps_name = '')
    {
        $this->ns_project = $this->getPS($ps_name);
        return self::$cache->delete($this->ns_project);
    }


    /**
     * 必须设置项目空间名，表空间名
     * 获取cache唯一的key
     * @param unknown_type $key
     * @return boolean|string
     */
    private function getKey($key)
    {
        if (!$this->ns_table_val) return false;
        if (!$this->ns_project_val) return false;

        $key = $this->ns_project_val.'_'.$this->ns_table_val.'_'.$key;
        return $key;
    }

    /**
     * 要求 项目空间名，表名同时都要设置
     * 设置缓存
     * @param unknown_type $key     设置键
     * @param unknown_type $val     设置值
     * @param unknown_type $expire  过期时间
     */
    public function setCache($key, $val, $expire)
    {
        $key = $this->getKey($key);
        if (!$key) return false;

        return self::$cache->set($key, serialize($val), $expire);
    }

    /**
     * 获取缓存
     * @param unknown_type $key
     */
    public function getCache($key)
    {

        $key = $this->getKey($key);
        if (!$key) return false;

        $data = self::$cache->get($key);

        if (DEBUG) self::$debug[$key] = serialize($data);
        if ($data) $data = unserialize($data);

        return $data;
    }

    /**
     * 删除缓存
     * @param unknown_type $key
     */
    public function deleteCache($key)
    {
        $key = $this->getKey($key);
        if (!$key) return false;

        return self::$cache->delete($key);
    }

    /**
     * 清空缓存
     */
    public function flushAll()
    {
        return self::$cache->flush();
    }
}
?>