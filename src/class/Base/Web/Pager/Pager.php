<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\Web\Pager;

use Nora\Core\Component;

/**
 * WEB用のページャ
 */
class Pager
{
    private $_limit = 15;
    private $_currentPage = 1;
    private $_total = 0;
    private $_pagerMax = 5;
    private $_jumpMax = 2;
    private $_sep = '<li class="pagerSep">...</li>'.PHP_EOL;
    private $_next = '<li class="pageNext"><a href="%s" class="pagerNext">NEXT</a></li>'.PHP_EOL;
    private $_prev = '<li class="pagePrev"><a href="%s" class="pagerPrev">PREV</a></li>'.PHP_EOL;
    private $_numberFormat = '<li class="page"><a href="%1$s" class="page">%2$s</a></li>'.PHP_EOL;
    private $_currentNumberFormat = '<li class="page pageCurrent">%2$s</li>'.PHP_EOL;

    public function setLimit($num)
    {
        $this->_limit = $num;
        return $this;
    }

    public function setCurrentPage($num)
    {
        $this->_currentPage = $num;
        return $this;
    }


    public function setPagerMax($num)
    {
        $this->_pagerMax = $num;
    }

    public function setTotal($num)
    {
        $this->_total = $num;
    }

    public function setUrlFormat($format)
    {
        $this->_urlFormat = $format;
    }

    public function setJumpMax($num)
    {
        $this->_jumpMax = $num;
    }

    public function render( )
    {
        // 設定値を読み込む
        $pageCurrent = $this->_currentPage;
        $pageTotal   = ceil($this->_total / $this->_limit);
        $pagerMax    = $this->_pagerMax;
        $jumpMax     = $this->_jumpMax;
        $sep         = $this->_sep;
        $next        = $this->_next;
        $prev        = $this->_prev;

        // 操作で使う値
        $starting    = [];
        $ending      = [];
        $useStartSep = false;
        $useEndSep   = false;
        $useNext     = false;
        $usePrev     = false;
        $numbers     = [];
        $helf        = floor($pagerMax / 2);
        $start       = $pageCurrent - $helf;
        $end         = $pageCurrent + $helf;

        // ページャマックス以下ならマックスにする
        if ($end < $pagerMax)
        {
            $end = $pagerMax;
        }

        // 終わりがトータルを超える場合
        // 終わりをトータルにして始まりを増やす
        if ($end > $pageTotal)
        {
            $end = $pageTotal;
            $start = $pageTotal - $pagerMax;
        }

        // 0以下ならスタートを1にする
        if ($start <= 0) {
            $start = 1;
        }


        // 終わりがページトータル以下なら最後を表示
        if ($end < $pageTotal)
        {
            for($i=0; $i<$jumpMax; $i++)
            {
                if ( ($pageTotal - $i) > $end )
                {
                    $ending[] = $pageTotal - $i;
                }
            }
            sort($ending);
        }

        // 開始が1出なかったら最初を表示
        if ($start > 1)
        {
            for($i=1; $i<=$jumpMax; $i++)
            {
                if ($i >= $start) break;
                $starting[] = $i;
            }
        }

        // メインとなる数字
        for($i=$start; $i<=$end; $i++)
        {
            $numbers[] = $i;
        }

        // セパレータフラグ
        if (!empty($starting) && $numbers[0] != ($starting[count($starting)-1]) +1)
        {
            $useStartSep = true;
        }
        if (!empty($ending) && $numbers[count($numbers)-1] != ($ending[0]-1))
        {
            $useEndSep = true;
        }

        // 次へ
        if ($pageCurrent+1 <= $pageTotal)
        {
            $useNext = true;
        }
        // 前へ
        if ($pageCurrent-1 > 0)
        {
            $usePrev = true;
        }

        $html = '';

        if ($usePrev)
        {
            $html .= $this->makelink($pageCurrent - 1, $prev);
        }
        foreach($starting as $v) {
            $html .= $this->makeLink($v);
        }
        if ($useStartSep) {
            $html.= $sep;
        }
        foreach($numbers as $v)
        {
            $html .= $this->makeLink($v);
        }
        if ($useEndSep) {
            $html.= $sep;
        }
        foreach($ending as $v) {
            $html .= $this->makeLink($v);
        }
        if ($useNext)
        {
            $html .= $this->makelink($pageCurrent + 1, $next);
        }

        return $html;
    }

    public function makeLink($v, $format = null)
    {
        if ($format == null) $format = $this->_numberFormat;

        $url ='';

        if ($v == $this->_currentPage)
        {
            return sprintf($this->_currentNumberFormat, $url, $v);
        }
        $url .= str_replace('__NUM__',  $v, $this->_urlFormat);
        return sprintf($format, $url, $v);
    }

    public function setNumberFormat($format)
    {
        $this->_numberFormat = $format;
    }


    public function currentOffset( )
    {
        return $this->_limit * ($this->_currentPage - 1);
    }

    static public function create($options = [])
    {
        $pager = new Pager();

        foreach($options as $k=>$v)
        {
            $pager->{"set".$k}($v);
        }
        return $pager;
    }
}

