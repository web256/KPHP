<?php
/**
 *  smarty.common.php
 * 许可声明：这是一个开源程序，未经许可不得将本软件的整体或任何部分用于商业用途及再发布。
 * ============================================================================
 * $Author: 王德康 (wangdk369@gmail.com) $
 * $Date: 2015-8-21 下午12:43:11 $
 * $Id$
 */


/**
 * 包含根目录的公共模版
 * @param array $params
 * @param obj $smarty
 */
function smarty_function_include_www($params, &$smarty)
{
    if (empty($params['file'])) {
        throw new SmartyException('请输入包含的模板地址');
        return false;
    }

    Response::getSmartySelf()->assign($params);
    return Response::getSmartySelf()->fetch(ROOT_PATH."/templates/{$params['file']}");
}

?>