<?php
/**
 *  Response.php
 * 许可声明：这是一个开源程序，未经许可不得将本软件的整体或任何部分用于商业用途及再发布。
 * ============================================================================
 * $Author: 王德康 (wangdk369@gmail.com) $
 * $Date: 2015-8-20 下午4:51:43 $
 * $Id: Response.php 1674 2015-08-21 06:40:59Z wangdk $
 * 扩展Smarty模板类
 */

require FRAMEWORK_PATH.'/3rd/smarty-3.1.27/libs/Smarty.class.php';

class Response
{
    private static $tpl;
    private static $html;

    /**
     * 初始化Smarty
     */
    public static function initView()
    {
        self::$tpl = new Smarty();
    }

    /**
     * 开启smarty debug调试模式
     * @param unknown_type $flag
     */
    public static function debug($flag)
    {
        self::$tpl->debugging = $flag;
    }

    /**
     * 模板赋值变量
     * @param unknown_type $tpl_var
     * @param unknown_type $value
     */
    public static function assign($tpl_var, $value = null)
    {
       self::$tpl->assign($tpl_var, $value);
    }

    /**
     * 加载模板
     * @param unknown_type $tpl_name
     */
    public static function display($tpl_name)
    {
       self::$html = self::$tpl->fetch($tpl_name);
    }

    public static function flush()
    {
        echo self::$html;
    }

    /**
     * 设置编译目录
     * @param unknown_type $compile_dir
     */
    public static function setCompileDir($compile_dir)
    {
        return self::$tpl->setCompileDir($compile_dir);
    }

    /**
     * 设置模板目录
     * @param unknown_type $template_dir
     */
    public static function setTemplateDir($template_dir)
    {
        return self::$tpl->setTemplateDir($template_dir);
    }

    /**
     * 获取smarty实例
     * @return Smarty
     */
    public static function getSmartySelf()
    {
        return self::$tpl;
    }
}
?>