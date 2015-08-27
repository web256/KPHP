<?php
/**
 * 全局公用函数 common.php
 * 许可声明：这是一个开源程序，未经许可不得将本软件的整体或任何部分用于商业用途及再发布。
 * ============================================================================
 * $Author: 王德康 (wangdk369@gmail.com) $
 * $Date: 2015-8-3 上午10:35:10 $
 * $Id: common.php 708 2015-08-03 07:40:57Z wangdk $
 */

/**
 *
 * @param unknown_type $url
 * @param unknown_type $flag
 */
function setUrl($url, $flag = '')
{
   if ($flag) $url = $url.$flag;
   return SITE_URL.'/?anu='.$url;
}

/**
 * 自动加载类函数
 * @param unknown_type $class_name
 */
function autoload_hanlder($class_name)
{
    $type  = '';
    $class = '';

    $arr = explode('_', $class_name);
    if ($arr) {

        if (isset($arr[1]) && $arr[1]) {
            $type   = $arr[1];
            $class  = $arr[0];
        }

        // 加载 helper
        if ($type == 'helper') {
            require ROOT_PATH.'/module/'.$class.'/helper/'.$class.'.php';
        }

        // _widget
    }
}

/**
 * 设置默认错误处理
 * @param unknown_type $errno  错误号
 * @param unknown_type $errstr 错误信息
 * @param unknown_type $errfile 错误所在文件
 * @param unknown_type $errline 错误所在行
 * @return void|boolean 返回值
 */
function error_handler($errno, $errstr, $errfile, $errline){

    // 获取错误号
    $errno = $errno & error_reporting();
    if($errno == 0) return;

    if(!defined('E_STRICT'))            define('E_STRICT', 2048);
    if(!defined('E_RECOVERABLE_ERROR')) define('E_RECOVERABLE_ERROR', 4096);

    print "<pre>\n<b>";

    switch($errno){
        case E_ERROR:               print "Error";                  break;
        case E_WARNING:             print "Warning";                break;
        case E_PARSE:               print "Parse Error";            break;
        case E_NOTICE:              print "Notice";                 break;
        case E_CORE_ERROR:          print "Core Error";             break;
        case E_CORE_WARNING:        print "Core Warning";           break;
        case E_COMPILE_ERROR:       print "Compile Error";          break;
        case E_COMPILE_WARNING:     print "Compile Warning";        break;
        case E_USER_ERROR:          print "User Error";             break;
        case E_USER_WARNING:        print "User Warning";           break;
        case E_USER_NOTICE:         print "User Notice";            break;
        case E_STRICT:              print "Strict Notice";          break;
        case E_RECOVERABLE_ERROR:   print "Recoverable Error";      break;
        default:                    print "Unknown error ($errno)"; break;
    }

    print ":</b> <i>$errstr</i> in <b>$errfile</b> on line <b>$errline</b>\n";
    if(function_exists('debug_backtrace')){

        // 回溯跟踪
        $backtrace = debug_backtrace();
        array_shift($backtrace);

        foreach($backtrace as $i=>$l){
            // OOP 和 非OOP
            $class = isset($l['class']) ? $l['class'] : '';
            $type = isset($l['type']) ? $l['type'] : '';

            print "[$i] in function <b>{$class}{$type}{$l['function']}</b>";
            if($l['file']) print " in <b>{$l['file']}</b>";
            if($l['line']) print " on line <b>{$l['line']}</b>";
            print "\n";
        }
    }

    print "\n</pre>";

    // 返回true 屏蔽PHP标准错误
    return true;
}

/**
 * 自定义异常处理
 * @param unknown_type $exception
 */
function exception_handler($exception)
{
   // $exception->showError();
    KException::showError2($exception);
}

/**
 * 异常处理类
 * @author wangdk
 *
 */
class KException extends Exception
{
//     public function showError()
//     {
//         echo "<pre>\n";
//         print "<b>KException：{$this->getMessage()}</b> in <b>{$this->getFile()}</b> on line <b>{$this->getLine()}</b>\n";

//         foreach($this->getTrace() as $i=>$l){
//             // OOP 和 非OOP
//             $class = isset($l['class']) ? $l['class'] : '';
//             $type = isset($l['type']) ? $l['type'] : '';

//             print "[$i] in function <b>{$class}{$type}{$l['function']}</b>";
//             if($l['file']) print " in <b>{$l['file']}</b>";
//             if($l['line']) print " on line <b>{$l['line']}</b>";
//             print "\n";
//         }
//         echo "\n</pre>";
//     }

    public static function showError2($obj)
    {
        echo "<pre>\n";
        print "<b>KException：{$obj->getMessage()}</b> in <b>{$obj->getFile()}</b> on line <b>{$obj->getLine()}</b>\n";

        foreach($obj->getTrace() as $i=>$l){
        // OOP 和 非OOP
            $class = isset($l['class']) ? $l['class'] : '';
            $type = isset($l['type']) ? $l['type'] : '';

                    print "[$i] in function <b>{$class}{$type}{$l['function']}</b>";
                    if(isset($l['file'])) print " in <b>{$l['file']}</b>";
                    if(isset($l['line'])) print " on line <b>{$l['line']}</b>";
                            print "\n";
        }
        echo "\n</pre>";
    }
}
?>