<?php
/**
 *  index.php
 * 许可声明：这是一个开源程序，未经许可不得将本软件的整体或任何部分用于商业用途及再发布。
 * ============================================================================
 * $Author: 王德康 (wangdk369@gmail.com) $
 * $Date: 2015-8-21 下午12:35:08 $
 * $Id$
 */

class Action
{
    private $pre_num = 10;

    public function __call($action = '', $params = array())
    {

        $page_no = Request::get('page_no', 0);

        $where = 'where 1 = ? ';
        $params = array(1);

        $total = _model('user')->getTotal($where, $params);
        if ($total) {

            $pager = new Page($this->pre_num);
            $pager->generate($total);

            $user_list = _model('user')->getList($where.$pager->getLimit($page_no), $params);
            Response::assign('user_list', $user_list);

            Response::assign('pager', $pager);
            Response::assign('total', $total);
        }

        Response::display('admin/user_list.html');
    }

    public function add()
    {
        Response::display('admin/user_add.html');
    }

    public function save()
    {
        $data = array();

        $upload = new Upload();
        $file_info = $upload->uploadFile($_FILES['file']);

        if (isset($file_info['errorCode'])) {
            exit('上传文件失败!');
        }

        $data['avatar'] = $file_info['file'];
        $data['user_name'] = Request::post('user_name', '');

        if (_model('user')->create($data)) {
           return '保存成功!';
        }

        return array('url'=>'user/admin', 'msg'=>'添加失败', 'type'=>'error');
    }
}
?>