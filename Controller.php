<?php
/**
 *  Controller.php
 * 许可声明：这是一个开源程序，未经许可不得将本软件的整体或任何部分用于商业用途及再发布。
 * ============================================================================
 * $Author: 王德康 (wangdk369@gmail.com) $
 * $Date: 2015-8-18 下午4:05:59 $
 * $Id: Controller.php 1718 2015-08-21 07:48:38Z wangdk $
 */

class Controller
{
    /**
     * 路由规则
     * @var unknown_type
     */
    private static $rules;

    /**
     * 记录路由解析规则
     * @var array
     */
    public static $debug;

    /**
     * 控制器分派
     * http://wangdk.cc/open/index.php?anu=user/test&debug=1
     * 1、优先加载    user/test1.php      __call
     * 2、次级加载    user/test/index.php __call
     * 3、再次级加载  user/index.php   test方法
     */
    public static function dispatch()
    {
        $controller = '';

        // 默认控制器
        self::$rules['defaultModule'] = 'home';


        $urls = self::parse_url();

        // 当前登录用户
        $user_id = 1;

        // 管理员列表
        $admin_users = KConfig::get('admin_users');

        foreach ($urls as $k => $v) {
            $arr = explode('/', $v);

            // 当前路径有访问到后台
            if (in_array('admin', $arr)) {
                if (!in_array($user_id, $admin_users)) {
                    exit('Not Found~');
                }
            }
        }

        if (DEBUG)  {
            echo '路由参数:<br>';
            self::echo_debug($urls);
            echo '路由解析:<br>';
        }

        // 加载控制器模块
        if (isset($urls['module']) && $urls['module']) {

            // 加载模块控制器
            $controller_dir = self::requireModule($urls);

            /**
             * 优先文件
             * 次级目录
             * 再次级index/方法
             */
            if (isset($urls['action']) && $urls['action']) {
                // 加载控制器方法
                $controller = self::requireController($controller_dir, $urls['action']);
            } else {
                // 加载默认控制器
                $controller = self::requireDefaultController($controller_dir);
            }
        }

        if (DEBUG)  {
            echo '<br>路由选择:';
            self::echo_debug($controller);
        }

        require $controller;
        $action = new Action();

        // 存在指定方法
        if (isset($urls['action']) && $urls['action']) {

            if (method_exists($action, $urls['action'])) {
                call_user_func_array(array($action, $urls['action']), array());

            } else {
                self::setDefaultController($action);
            }

        } else {
            self::setDefaultController($action);
        }

    }

    /**
     * 加载模块控制器
     * @param unknown_type $urls
     * @throws KException
     * @return string
     */
    public static function requireModule($urls)
    {
        // 模块目录
        $module_dir = ROOT_PATH.'/module/'.$urls['module'];
        if (DEBUG) self::echo_debug($module_dir);

        if (!file_exists($module_dir)) {
            throw new KException('Not Found Module Path!');
        }


        // 加载模块相关信息
        self::loadModuleInfo($module_dir.'/templates');

        // 控制器目录
        $controller_path = $module_dir.'/controller';
        if (isset($urls['controller']) && $urls['controller']) {

            $controller_dir = $controller_path . '/'. $urls['controller'];
            if (DEBUG) self::echo_debug($controller_dir);

            // 不存在或者不是目录
            if (!file_exists($controller_dir)) {
                // 文件不存在，目录也不存在
                $controller_dir = $controller_path;
                if (DEBUG) self::echo_debug($controller_dir);
            }
        } else {
            $controller_dir = $controller_path;
        }

        return $controller_dir;
    }

    /**
     * 加载默认控制器
     * @param unknown_type $controller_dir
     * @throws KException
     */
    public static function requireDefaultController($controller_dir)
    {
        // action 不存在
        $controller = $controller_dir.'/index.php';
        if (DEBUG) self::echo_debug($controller);

        if (!file_exists($controller)) {
            // 默认控制器也不存在，抛出异常
            if (DEBUG) self::echo_debug($controller);
            throw new KException('Not Found controller');
        }

        return $controller;
    }

    /**
     * 加载控制器方法
     * @param unknown_type $controller_dir 控制器绝对路径目录
     * @param unknown_type $action         控制器方法名
     * @throws KException
     */
    public static function requireController($controller_dir, $action)
    {
        $controller = $controller_dir.'/'.$action.'.php';
        if (DEBUG) self::echo_debug($controller);

        if (!file_exists($controller)) {

            //  ?anu=user/admin/aa
            $controller = $controller_dir.'/'.$action;
            if (DEBUG) self::echo_debug($controller);

            if (!file_exists($controller)) {
                // 目录也不存在

                // 默认控制器
                $controller = $controller_dir.'/index.php';
                if (DEBUG) self::echo_debug($controller);

                if (!file_exists($controller)) {
                    throw new KException('Not Found Controller');
                }

            } else {

                // 目录存在
                $controller = $controller_dir.'/'.$action.'/index.php';
                if (DEBUG) self::echo_debug($controller);

                if (!file_exists($controller)) {
                    throw new KException('Not Found Controller');
                }
            }
        }

        return $controller;
    }

    /**
     * 打印路径解析
     * @param unknown_type $controller
     */
    public static function echo_debug($controller)
    {
        echo '<br>';
        echo  $controller;

    }

    /**
     * 设置默认的控制器方法
     * @param unknown_type $action
     * @throws KException
     */
    public static function setDefaultController($action)
    {

        // 方法不存在，寻找默认__call
        if (method_exists($action, '__call')) {
            call_user_func_array(array($action, '__call'), array());
        } else {

            // 方法不存在，寻找默认index
            if (method_exists($action, 'index')) {
                call_user_func_array(array($action, 'index'), array());
            } else {
                throw new KException('Not Found Action');
            }
        }
    }


    /**
     * 解析URL路径
     */
    public static function parse_url()
    {
        $urls = array();

        $anu = htmlspecialchars((isset($_GET['anu']) ? $_GET['anu'] : ''), ENT_QUOTES);
        if (!$anu) {
            $anu = self::$rules['defaultModule'];
        }

        $urls_arr = explode('/', $anu);
        $count    = count($urls_arr);

        if ($count == 2) {
            $urls['module']     = array_shift($urls_arr);
            // $urls['controller'] = array_shift($urls_arr);

            $urls['action'] = array_shift($urls_arr);

        } else if ($count > 2) {
            $urls['module']     = array_shift($urls_arr);
            $urls['action']     = array_pop($urls_arr);
            $urls['controller'] = join('/', $urls_arr);

        } else {
            $urls['module']     = $anu;
        }

        return $urls;
    }

    /**
     * 加载模块
     * @param unknown_type $path
     */
    public static function loadModuleInfo($path)
    {
        Response::setTemplateDir($path);
    }
}
?>