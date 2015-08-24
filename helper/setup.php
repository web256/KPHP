<?php
/**
 *  setup.php
 * 许可声明：这是一个开源程序，未经许可不得将本软件的整体或任何部分用于商业用途及再发布。
 * ============================================================================
 * $Author: 王德康 (wangdk369@gmail.com) $
 * $Date: 2015-8-3 上午10:31:48 $
 * $Id$
 */

/**
 * 开启session会话
 */
session_start();

/**
 * 打开全部错误信息
*/
error_reporting(E_ALL ^ E_NOTICE);

/**
 * 线上要关闭全部错误信息
 */
if (!ONDEV) error_reporting(0);


/**
 * 线上开启调试模式
 */
if (DEBUG && POWERBY) {
    error_reporting(E_ALL ^ E_NOTICE);
}


require ROOT_PATH.'/config/config.php';
require ROOT_PATH.'/helper/common.php';
require ROOT_PATH.'/Controller.php';

/**
 * 设置自定义错误处理
 */
set_error_handler('error_handler');

/**
 * 设置自定义异常处理
 */
set_exception_handler('exception_handler');

/**
 * 注册自动加载类
 */
spl_autoload_register('autoload_hanlder');

/**
 * 缓存对象
 */
$mc = new CacheWrapper();

/**
 * 初始化模板
 */
Response::initView();
Response::setCompileDir(ROOT_PATH.'/data');
if (ONDEV || (DEBUG && POWERBY)) {
    //Response::debug(true);
}

// smarty 自定义函数
require ROOT_PATH.'/helper/smarty.common.php';
/**
 * 路由解析
 */
Controller::dispatch();

Response::flush();
?>