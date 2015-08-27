<?php
/**
 *  Upload.php
 * 许可声明：这是一个开源程序，未经许可不得将本软件的整体或任何部分用于商业用途及再发布。
 * ============================================================================
 * $Author: 王德康 (wangdk369@gmail.com) $
 * $Date: 2015-8-25 上午9:44:13 $
 * $Id$
 *
 * 文件上传是经常出现漏洞的地方，各种检测方法各种绕过
 * 最安全有效的方法，把图片目录权限降到最低，不允许执行动态程序。
 * 所有图片应该单独存放，不能和动态程序混合在一起。
 * 图片存储应该按照日期分目录，防止图片太多读取慢
 *
 * Nginx 应该添加：
 * # 防止上传脚本运行
    location ~ /(upload|static|data|html|images|css|js)/.*\.(php|php5)?$
    {
        deny all;
    }
 */

class Upload
{

    /**
     * 允许的文件类型
     * @var unknown_type
     */
    private $allowType;

    /**
     * 允许的最大文件
     * @var unknown_type
     */
    private $maxSize;

    /**
     * 错误信息提示
     * @var unknown_type
     */
    private $errorInfo;

    /**
     * 错误信息码
     * @var unknown_type
     */
    private $errorCode;

    /**
     * 文件扩展名
     * @var unknown_type
     */
    private $extFile;

    public function __construct()
    {
        $config_info = KConfig::get('upload_file');

        $this->allowType = $config_info['allow_type'];
        $this->maxSize   = $config_info['max_size'];
    }

    /**
     * 检测文件大小和文件mime类型
     * @param unknown_type $file
     * @return boolean
     */
    public function checkFile($file)
    {
        if ($file['error'] == UPLOAD_ERR_INI_SIZE) {
            $this->errorInfo = '上传的文件超过了php.ini中upload_max_filesize选项!';
            $this->errorCode = '10001';
            return false;

        } else if ($file['error'] == UPLOAD_ERR_FORM_SIZE ) {
            $this->errorInfo = '上传文件的大小超过了HTML表单中指定的MAX_FILE_SIZE指令!';
            $this->errorCode = '10002';
            return false;

        } else if ($file['error'] == UPLOAD_ERR_PARTIAL ) {
            $this->errorInfo = '上传的文件只有部分被上传!';
            $this->errorCode = '10003';
            return false;

        } else if ($file['error'] == UPLOAD_ERR_NO_FILE ) {
            $this->errorInfo = '缺少一个临时文件夹!';
            $this->errorCode = '10004';
            return false;

        } else if ($file['error'] == UPLOAD_ERR_CANT_WRITE ) {
            $this->errorInfo = '无法写入文件到磁盘!';
            $this->errorCode = '10005';
            return false;

        } else if ($file['error'] == UPLOAD_ERR_EXTENSION ) {
            $this->errorInfo = 'PHP扩展停止了文件上传!';
            $this->errorCode = '10006';
            return false;
        }

        if ($file['size'] > $this->maxSize) {
            $this->errorInfo = '文件上传超过最大文件限制!';
            $this->errorCode = '20001';
            return false;
        }

        if (in_array($file['type'], $this->allowType)) {
            $this->errorInfo = '上传的文件类型不允许!';
            $this->errorCode = '20002';
            return false;
        }

        return true;
    }

    public function uploadFile($file)
    {

        if (!$this->checkFile($file)) {
            return array('errorCode'=>$this->errorCode, 'errorInfo'=>$this->errorInfo);
        }

        if (!$this->getExtFileName($file['name'])) {
            return array('errorCode'=>$this->errorCode, 'errorInfo'=>$this->errorInfo);
        }

        // 检测目录
        $file_path = $this->makeUploadDir();

        // 生成文件名
        $file_name = $this->makeRandomFileName();

        $full_file_name = UPLOAD_PATH.$file_path.$file_name;
        if (move_uploaded_file($file['tmp_name'], $full_file_name)) {
            return array('file'=>$file_path.$file_name);
        }

        $this->errorInfo = '文件上传失败!';
        $this->errorCode = '20008';

        return array('errorCode'=>$this->errorCode, 'errorInfo'=>$this->errorInfo);
    }

    /**
     * 获取上传文件扩展名
     * @param unknown_type $file_name
     * @return boolean
     */
    public function getExtFileName($file_name)
    {
        $file_info = explode('.', $file_name);
        if (isset($file_info[1])) {
            $this->extFile = $file_info[1];
        } else {
            $this->errorInfo = '未检测到文件扩展名!';
            $this->errorCode = '20003';
            return false;
        }

        return true;
    }

    /**
     * 生成目录并修改权限
     * @return boolean
     */
    public function makeUploadDir()
    {
        $file_dir  = date('/Y/m/d/');

        if (!file_exists(UPLOAD_PATH.$file_dir)) {
            // 修改目录权限,非nginx用户执行使用
            // $old_umask = umask(0);
            mkdir(UPLOAD_PATH.$file_dir, 0755, true);
            // umask($old_umask);
        }

        return $file_dir;
    }

    /**
     * 生成随机名称
     * @return string
     */
    public function makeRandomFileName()
    {
        return md5(time().mt_rand(1, 1000)).'.'.$this->extFile;
    }
}
?>