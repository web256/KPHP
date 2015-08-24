<?php
/**
 *  index.php
 * 许可声明：这是一个开源程序，未经许可不得将本软件的整体或任何部分用于商业用途及再发布。
 * ============================================================================
 * $Author: 王德康 (wangdk369@gmail.com) $
 * $Date: 2015-8-18 下午5:14:06 $
 * $Id$
 */

class Action
{
    public function __call($action = '', $params = array())
    {
        echo '<br>';
        echo __FILE__. __FUNCTION__;
        echo 'test';
    }

    public function test1()
    {
        var_dump('test1');
    }

}
?>