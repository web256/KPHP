<?php
/**
 * 配置类 KConfig.php
 * 许可声明：这是一个开源程序，未经许可不得将本软件的整体或任何部分用于商业用途及再发布。
 * ============================================================================
 * $Author: 王德康 (wangdk369@gmail.com) $
 * $Date: 2015-8-3 下午9:09:35 $
 * $Id: KConfig.php 741 2015-08-03 13:11:20Z wangdk $
 */
class KConfig
{
    static $data = array();

    public static function set($key, $val) {
        self::$data[$key] = $val;
    }

    public static function get($key) {
        return isset(self::$data[$key]) ? self::$data[$key] : '';
    }
}
?>