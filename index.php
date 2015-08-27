<?php
/**
 * 定义是否在开发服务器上
 */
defined('ONDEV') || define('ONDEV', stripos($_SERVER['HTTP_HOST'], 'wangdk.cc') !== FALSE);
/**
 * 定义网站url
 */
define('SITE_URL', 'http://'.$_SERVER['HTTP_HOST'].'/open');


/**
 * 定义线上调试模式
 */
define('POWERBY', ONDEV || (isset($_GET['powerby']) && $_GET['powerby'] == 'wangdk'));

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
 * 文件上传目录
 */
define('UPLOAD_PATH', ROOT_PATH.'/static/upload');


/**
 * 模块根目录
 */
define('MODULE_PATH', ROOT_PATH.'/module');


define('FRAMEWORK_PATH', ROOT_PATH.'/framework');

require FRAMEWORK_PATH.'/Init.php';
require ROOT_PATH.'/helper/setup.php';
require 'test.php';

?>