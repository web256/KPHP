<?php
/**
 *  setup.php
 * 许可声明：这是一个开源程序，未经许可不得将本软件的整体或任何部分用于商业用途及再发布。
 * ============================================================================
 * $Author: 王德康 (wangdk369@gmail.com) $
 * $Date: 2015-8-3 上午10:31:48 $
 * $Id$
 */

// 开发打开全部错误
error_reporting(E_ALL);

require ROOT_PATH.'/config/config.php';
require ROOT_PATH.'/helper/common.php';

// 设置自定义错误处理
set_error_handler('error_handler');

// 设置自定义异常处理
set_exception_handler('exception_handler');


// cache对象
$mc = new CacheWrapper();
?>