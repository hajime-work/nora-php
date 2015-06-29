<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\Database\Client\Mongo\Exception;

use Nora;

class CollectionNotFound extends Exception
{
    public function __construct($col, $name)
    {
        parent::__construct(Nora::Message("[Mongo:Error] Collection %s は見つかりませんでした", $name));
    }
}
