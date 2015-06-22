<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\Configuration\Exception;

use Nora\Base\Exception;
use Nora;

/**
 * 設定管理アイテム[Single]
 */
class CantSetArrayToSingle extends Exception
{
    public function __construct( )
    {
        parent::__construct(Nora::message("ItemSingleに配列をセットできません"));
    }
}

