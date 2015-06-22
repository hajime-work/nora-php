<?php
/**
 * Nora Project
 * ============
 *
 * @since 20150618
 * @author kurari@hajime.work
 */
Namespace Nora\Scope;

use Nora\Base\Hash;

/**
 * スコープ:コールメソッドクラスIF
 */
interface CallMethodIF
{
    public function isCallable($name, $args, $client);
    public function call($name, $args, $client);
    public function help( );
}

/* vim: set foldmethod=marker st=4 ts=4 sw=4 et: */
