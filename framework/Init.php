<?php
/**
 * 框架入口 Init.php
 * 许可声明：这是一个开源程序，未经许可不得将本软件的整体或任何部分用于商业用途及再发布。
 * ============================================================================
 * $Author: 王德康 (wangdk369@gmail.com) $
 * $Date: 2015-8-4 下午5:15:38 $
 * $Id$
 */

date_default_timezone_set("PRC");

require FRAMEWORK_PATH.'/KConfig.php';
require FRAMEWORK_PATH.'/CacheWrapper.php';

require FRAMEWORK_PATH.'/Model.php';
require FRAMEWORK_PATH.'/ModelRes.php';



/**
 * model 数据库操作
 * @param unknown_type $table
 */

function _model($table) {

    // 单例
    static $model;
    if (!$model) $model = new ModelRes($table);

    return $model;
}

?>