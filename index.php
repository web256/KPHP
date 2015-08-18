<?php
/**
 * 定义是否在开发服务器上
 */
defined('ONDEV') || define('ONDEV', stripos($_SERVER['HTTP_HOST'], '232.243'));

/**
 * 定义网站url
 */
define('SITE_URL', 'http://'.$_SERVER['HTTP_HOST']);


/**
 * 定义线上调试模式
 */
define('POWERBY', (isset($_GET['powerby']) && $_GET['powerby'] == 'wangdk'));

/**
 * 定义线上调试模式
 */
define('DEBUG', (isset($_GET['debug']) && $_GET['debug'] == 1) && POWERBY);


/**
 * 定义线上调试模式
 */
define('CACHE', (isset($_GET['cache']) && $_GET['cache'] == 0) && POWERBY);

/**
 * 网站根目录
 */
define('ROOT_PATH', dirname(__FILE__));


/**
 * 静态文件url
*/
define('STATIC_URL', SITE_URL);


/**
 * 开启session会话
 */
session_start();

/**
 * 打开全部错误信息
 */
error_reporting(E_ALL);

/**
 * 线上要关闭全部错误信息
 */
if (!ONDEV) error_reporting(0);

/**
 * 线上要关闭全部错误信息
 */
if (DEBUG && POWERBY) error_reporting(E_ALL);

define('FRAMEWORK_PATH', ROOT_PATH.'/framework');

require FRAMEWORK_PATH.'/Init.php';
require ROOT_PATH.'/helper/setup.php';
require 'test.php';

?>