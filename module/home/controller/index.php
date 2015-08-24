<?php
/**
 *  index.php
 * 许可声明：这是一个开源程序，未经许可不得将本软件的整体或任何部分用于商业用途及再发布。
 * ============================================================================
 * $Author: 王德康 (wangdk369@gmail.com) $
 * $Date: 2015-8-20 下午2:43:35 $
 * $Id$
 */

class Action
{
    /**
     * home
     * @param unknown_type $action
     * @param unknown_type $params
     */
    public function __call($action = '', $params = array())
    {

        $val = home_helper::get_user_id();

        Response::assign('val', $val);
        Response::assign('songrd', array(1=>1, 2=>2));

        Response::display('index.html');
    }
}
?>