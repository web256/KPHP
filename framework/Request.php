<?php
/**
 * Request.php 输出
 * 许可声明：这是一个开源程序，未经许可不得将本软件的整体或任何部分用于商业用途及再发布。
 * ============================================================================
 * $Author: 王德康 (wangdk369@gmail.com) $
 * $Date: 2015-8-23 下午2:57:47 $
 * $Id: Request.php 1869 2015-08-24 10:17:12Z wangdk $
 */

class Request
{
    /**
     * 过滤xss 双引号和单引号
     * @param unknown_type $string
     * @return string
     */
    public static function htmlspecialchars($string)
    {
        return htmlspecialchars($string, ENT_QUOTES);
    }

    /**
     * 返回不同类型的数据，强制转换后的
     * @param unknown_type $val
     * @param unknown_type $type
     * @return number|unknown
     */
    private static function getTypeValue($val, $type)
    {
        if (is_string($type)) {
            return self::htmlspecialchars((string)$val);
        }

        if (is_integer($type)) {
            return intval($val);
        }

        if (is_double($type)) {
            return (double)$val;
        }

        return $type;
    }

    /**
     * 过滤数组的所有信息
     * @param unknown_type $val
     * @return string
     */
    private function getArrayValue($val)
    {
        foreach ($val as $k => $v) {
            $val[$k] = self::htmlspecialchars((string)$val);
        }
        return $val;
    }

    /**
     * 获取安全的GET参数
     * @param unknown_type $key  要获取的key
     * @param unknown_type $type 默认类型，参数会自动根据默认类型强转参数
     * @param unknown_type $flag 是否特殊处理，不过滤参数
     * @return boolean|string|unknown|Ambigous <number, unknown, number, unknown_type, string>
     */
    public static function get($key, $type = '', $flag = 0)
    {
        if (!isset($_GET[$key])) return $type;

        $val = $_GET[$key];

        if (!$flag) {
            return self::getTypeValue($val, $type);
        }

        return $val;
    }

    /**
     * 获取安全的POST参数
     * @param unknown_type $key  要获取的key
     * @param unknown_type $type 默认类型，参数会自动根据默认类型强转参数
     * @param unknown_type $flag 是否特殊处理，不过滤参数
     * @return boolean|string|unknown|Ambigous <number, unknown, number, unknown_type, string>
     */
    public static function post($key, $type = '', $flag = 0)
    {
        if (!isset($_POST[$key])) return $type;

        $val = $_POST[$key];

        if (is_array($type)) {
            return self::getArrayValue(array($val));
        }

        if ($flag) {
            return $val;
        }
        return self::getTypeValue($val, $type);
    }

    /**
     * 获取安全的当前窗口连接
     * @return string
     */
    public static function getRequestURI()
    {
        return self::htmlspecialchars(($_SERVER['REQUEST_URI']));
    }

    /**
     * 获取客户端IP,要是代理服务器还是考虑挺多的
     * @return string
     * 一、没有使用代理服务器的PHP获取客户端IP情况：

        REMOTE_ADDR = 客户端IP
        HTTP_X_FORWARDED_FOR = 没数值或不显示

       二、使用透明代理服务器的情况：Transparent Proxies

        REMOTE_ADDR = 最后一个代理服务器 IP
        HTTP_X_FORWARDED_FOR = 客户端真实 IP （经过多个代理服务器时，这个值类似：221.5.252.160, 203.98.182.163, 203.129.72.215）
        这类代理服务器还是将客户端真实的IP发送给了访问对象,无法达到隐藏真实身份的目的.

      三、使用普通匿名代理服务器的PHP获取客户端IP情况：Anonymous Proxies

        REMOTE_ADDR = 最后一个代理服务器 IP
        HTTP_X_FORWARDED_FOR = 代理服务器 IP （经过多个代理服务器时，这个值类似：203.98.182.163, 203.98.182.163, 203.129.72.215）
        这种情况下隐藏了客户端的真实IP,但是向访问对象透露了客户端是使用代理服务器访问它们的.

      四、使用欺骗性代理服务器的情况：Distorting Proxies

         REMOTE_ADDR = 代理服务器 IP
         HTTP_X_FORWARDED_FOR = 随机的 IP（经过多个代理服务器时,这个值类似：220.4.251.159, 203.98.182.163, 203.129.72.215）
         这种情况下同样透露了客户端是使用了代理服务器,但编造了一个虚假的随机IP（220.4.251.159）代替客户端的真实IP来欺骗它.

     五、使用高匿名代理服务器的PHP获取客户端IP情况：High Anonymity Proxies (Elite proxies)

        REMOTE_ADDR = 代理服务器 IP
        HTTP_X_FORWARDED_FOR = 没数值或不显示

        无论是REMOTE_ADDR还是HTTP_FORWARDED_FOR，这些头消息未必能够取得到,因为不同的浏览器不同的网络设备可能发送不同的IP头消息.因此PHP使用$_SERVER["REMOTE_ADDR"] 、$_SERVER["HTTP_X_FORWARDED_FOR"] 获取的值可能是空值也可能是“unknown”值.
     */
    public static function getClientIP()
    {
      $unknown = 'unknown';

      // 先检测代理服务器
      if ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], $unknown) ) {
          $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

      // 直接获取客户端的
      } elseif ( isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], $unknown) ) {
          $ip = $_SERVER['REMOTE_ADDR'];
      }

      return self::htmlspecialchars($ip);
    }

    /**
     * 获取过去安全的HOST
     * @return string
     */
    public static function getRequestHost()
    {
        return self::htmlspecialchars($_SERVER['HTTP_HOST']);
    }

    /**
     * 获取过安全的请求时间
     * @return string
     */
    public static function getRequestTime()
    {
        return self::htmlspecialchars($_SERVER['REQUEST_TIME']);
    }

    /**
     * 获取过安全的服务器IP
     * @return string
     */
    public static function getServerIP()
    {
        return self::htmlspecialchars($_SERVER['SERVER_ADDR']);
    }

    /**
     * 判断是否ajax请求，支持jQuery框架，原生要自己构造特殊请求头
     */
    public static function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == strtolower('XMLHttpRequest');
    }

    /**
     * 获取请求来源ref
     * @return Ambigous <string, unknown>
     */
    public static function isRef()
    {
        return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    }
}
?>