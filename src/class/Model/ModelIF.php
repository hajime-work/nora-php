<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Model;




/**
 * モデル
 */
interface ModelIF
{
    /**
     * @return bool
     */
    public function isChanged();
}
