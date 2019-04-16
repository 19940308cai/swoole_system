<?php
/**
 * Created by PhpStorm.
 * User: caijiang
 * Date: 2019/3/21
 * Time: 下午4:47
 */

namespace lib;

#对象转数组函数
function objectToArray($obj)
{
    $arr = is_object($obj) ? get_object_vars($obj) : $obj;
    if (is_array($arr)) {
        return array_map(__FUNCTION__, $arr);
    } else {
        return $arr;
    }
}

#数组转对象
function arrayToObject($arr)
{
    if (is_array($arr)) {
        return (object)array_map(__FUNCTION__, $arr);
    } else {
        return $arr;
    }
}

class Pager
{
    public $count;      #结果总数
    public $page;       #当前页
    public $pagesize;   #每页结果数
    public $pagecount;  #翻页数
    public $baseurl;    #url
    public $result;     #结果数组集
    public $pagelist;   #每翻页数
    public $limit;
    public $offset;

    #构造函数
    function __construct($count, $page, $pagesize, $pagelist=10, $baseurl = false)
    {
        $this->count = $count;
        $this->page = $page;
        $this->pagesize = $pagesize;
        $this->baseurl = ($baseurl != false) ? $baseurl : $this->geturl();
        $this->pagelist = $pagelist;
    }

    #获得当前url
    function geturl()
    {
        return preg_replace("/(^|&)page={($this->page)}/", "", $_SERVER['PHP_SELF']);
    }

    #获得分页列表
    function getpagelist()
    {
        $this->result['count'] = $this->count;
        $this->result['page'] = $this->page;
        $this->result['pagesize'] = $this->pagesize;
        #总数除以每页结果数得到页数
        $this->result['pagecount'] = ceil($this->count / $this->pagesize);

        if ($this->result['pagecount'] <= 1) //只有一页以下
        {
            $this->result['pagelist'] = array(array('page' => 1));
            #前一页，第一页的算法
            $this->result['first'] = 1;
            $add_num = ($this->page == 1) ? 0 : 1;
            $this->result['pre'] = $this->page-$add_num;
            #后一页，最后一页的算法
            $delete_num = ($this->page == $this->result['pagecount']) ? 0 : 1;
            $this->result['next'] = $this->page+$delete_num;
            $this->result['last'] = $this->result['pagecount'];
        } else {
            if ($this->result['pagecount'] < $this->pagelist) { #如果总页数小于设置的页数，
                $this->result['pagelist'] = $this->result['pagecount'];
            } else {
                $this->result['pagelist'] = $this->pagelist;
            };//一页以上
            #前一页，第一页的算法
            $this->result['first'] = 1;
            $add_num = ($this->page == 1) ? 0 : 1;
            $this->result['pre'] = $this->page-$add_num;
            #后一页，最后一页的算法
            $delete_num = ($this->page == $this->result['pagecount']) ? 0 : 1;
            $this->result['next'] = $this->page+$delete_num;
            $this->result['last'] = $this->result['pagecount'];
            #起始
            $pagearray = array();
            $range = floor($this->result['pagelist'] / 2);
            if (($this->page) > $range && ($this->result['pagecount'] >= $this->pagelist && $this->result['pagelist'] >= 3)) {
                $start = $this->page - $range;
                for ($i = $range; $i < $this->pagelist + $range; $i++) {
                    if (($start + $i - $range) <= $this->result['pagecount']) {
                        $pagearray[$i]['page'] = $start + $i - $range;

                        if (($start + $i - $range) != $this->page) {
                            $pagearray[$i]['link'] = 1;
                        }
                    }
                }
            } else {
                $start = floor(($this->page - 1) / $this->pagelist) * ($this->pagelist) + 1;
                for ($i = 0; $i < $this->result['pagelist']; $i++) {

                    if (($start + $i) <= $this->result['pagecount']) {
                        $pagearray[$i]['page'] = $start + $i;

                        if (($start + $i) != $this->page) {
                            $pagearray[$i]['link'] = 1;
                        }
                    }
                }
            }
            #分页导航列表
            $this->result['pagelist'] = $pagearray;
            $this->result['baseurl'] = $this->baseurl;
        }
        $this->offset = ($this->page-1) * $this->pagesize;
        $this->limit = $this->pagesize;
    }
}