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
 * WEB用のページャ付きイテレータ
 */
class PagerIterator implements \IteratorAggregate
{
    public function __construct($data)
    {
        $this->_count = $data['count'];
        $this->_datas = $data['datas'];
    }

    public function getIterator( )
    {
        foreach($this->_datas as $k=>$v)
        {
            yield $k=>$v;
        }
    }
}

