<?php
/**
 *  index.php
 * 许可声明：这是一个开源程序，未经许可不得将本软件的整体或任何部分用于商业用途及再发布。
 * ============================================================================
 * $Author: 王德康 (wangdk369@gmail.com) $
 * $Date: 2015-8-19 下午12:52:29 $
 * $Id$
 */

class Action
{

    public function __call($action = '', $params = array())
    {
        var_dump('test/index.php/__call');
    }

    public function test3()
    {
        var_dump('test3/index.php/test3');
    }
}
?>