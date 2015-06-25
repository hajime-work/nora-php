<?php
/**
 * Nora Project
 * ============
 *
 * @since 20150618
 * @author kurari@hajime.work
 */
Namespace Nora\App\Env;

class DevelEnv extends DefaultEnv
{

    public function setup( )
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    }
}


/* vim: set foldmethod=marker st=4 ts=4 sw=4 et: */
