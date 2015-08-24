<?php
/**
 *  Pager.php
 * 许可声明：这是一个开源程序，未经许可不得将本软件的整体或任何部分用于商业用途及再发布。
 * ============================================================================
 * $Author: 王德康 (wangdk369@gmail.com) $
 * $Date: 2015-8-21 下午5:24:47 $
 * $Id: Pager.php 1821 2015-08-22 08:39:52Z wangdk $
 *
 */

class Page
{
    /**
     * 每页显示数量
     * @var unknown_type
     */
    private $per_page;

    /**
     * 总记录数量
     * @var unknown_type
     */
    private $total;

    /**
     * 总页码数
     * @var unknown_type
     */
    private $pages;

    /**
     * 当前页码
     * @var unknown_type
     */
    private $page;

    /**
     * 当前连接
     * @var unknown_type
     */
    private $link;

    /**
     * 是否已经生成分页，用来知道是否有最后一页
     * @var bool
     */
    private $isGenerated = false;

    public function __construct($per_page)
    {
        $this->per_page = $per_page;
    }

    /**
     * 获取默认分类页码
     * @param unknown_type $page
     * @return number
     */
    public function getDefaultPage($page)
    {
        if (!$page) {
            if (isset($_GET['page_no'])) {
                $page = intval($_GET['page_no']);
                $page < 1 && $page = 1;
            } else {
                $page = 1;
            }
        }
        return $page;
    }

    /**
     * 生成分页Limit
     * @param unknown_type $page 页码
     */
    public function getLimit($page)
    {
        if (!$page) {
            $page = $this->getDefaultPage($page);
            return 'LIMIT '.($page - 1 ) * $this->per_page.', '.$this->per_page;

        }

        $page = intval($page);

        // 最小
        if ($page < 1) $page = 1;

        // 最大
        if ($this->isGenerated && $page > $this->pages) $page = $this->pages;
        $this->page = $page;

        return 'LIMIT '.($this->page - 1) * $this->per_page.', '.$this->per_page;
    }

    /**
     * 根据总页数生成分页需要的数据
     * @param unknown_type $total
     * @param unknown_type $page
     * @param unknown_type $link
     */
    public function generate($total, $page = null, $link = null)
    {
        $page = $this->getDefaultPage($page);
        $this->link = $link;

        if (!$this->link) {
            // 防止有xss
            $uri = preg_replace('/&page_no=\d+/i', '', $_SERVER['REQUEST_URI']);
            $uri = htmlspecialchars($uri, ENT_QUOTES);
            $this->link = ltrim($uri, '/').'&amp;page_no=[#]';
        } else {

            // 不存在page_no=[#]
            if (stripos($link, '[#]') === false) {
                if (stripos($link, '?') === false) {
                    $this->link = $link .'&amp;page_no=[#]';
                } else {
                    $this->link = $link.'?page=[#]';
                }
            }
        }

        // 总记录数
        $this->total = $total;

        // 计算最大页码
        $this->pages = ceil($this->total/$this->per_page);
        $this->pages < 1 && $this->pages = 1;

        // 不能超出最大页码
        $page > $this->pages && $page = $this->pages;
        $this->page = $page;

        $this->isGenerated = true;
        return $this->pages > 1;
    }

    /**
     * 生成连续的页码 1 2 3 4 5
     * 例如：$num = 9;
     * 会生成9个页码显示，根据页码当前页码折半显示
     * @param unknown_type $num 显示几个页码
     */
    public function getPagesArray($num)
    {
        if (!$num) {
            // 生成所有页码
            return range(1, $this->pages);
        }
        // 计算前后偏移位置
        $pre_num = floor($num / 2);

        $min = ($this->page < $pre_num) ? 1 : ($this->page - $pre_num);
        $max = ($this->page + $pre_num) > $this->pages ? $this->pages : ($this->page + $pre_num);

        // 补齐后面分页
        if ($this->page < $pre_num) {
            $max += $pre_num - $this->page;
        }

        // 补齐前面分页
        if ($this->pages - $this->page < $pre_num) {
            $min = $min - ($pre_num - ($this->pages - $this->page));
        }

        return range($min, $max);
    }
    /**
     * 替换分页连接
     * @return mixed
     */
    public function link($page)
    {
        return str_replace('[#]', $page, $this->link);
    }

    /**
     * 下一页
     * @return Ambigous <number, unknown_type>
     */
    public function next()
    {
        return ($this->page + 1) >  $this->pages ? $this->pages : ($this->page + 1);
    }

    /**
     * 上一页
     * @return number
     */
    public function prev()
    {
        return ($this->page - 1) < 1  ? 1 : ($this->page - 1);
    }

    /**
     * 第一页
     * @return number
     */
    public function begin()
    {
        return 1;
    }

    /**
     * 当前页码
     * @return unknown_type
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * 每页显示的数据
     */
    public function getPerPage()
    {
        return $this->per_page;
    }

    /**
     * 总页码
     * @return unknown_type
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * 末页
     * @return unknown_type
     */
    public function end()
    {
        return $this->pages;
    }
}
?>