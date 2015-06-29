<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\Database\Client\Mysqli\Exception;

use Nora;

class QueryFailed extends Exception
{
    public function __construct($facade, $result, $query)
    {
        $error = $facade->getError();

        parent::__construct(Nora::Message("[Mysqli:Error] %s", $error));
    }
}
