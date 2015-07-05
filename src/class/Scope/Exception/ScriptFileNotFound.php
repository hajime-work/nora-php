<?php
/**
 * Nora Project
 * ============
 *
 * @since 20150618
 * @author kurari@hajime.work
 */
Namespace Nora\Scope\Exception;
use Nora\Scope\ScopeIF;
use Nora;


class ScriptFileNotFound extends Exception
{
    public function __construct(ScopeIF $scope, $path)
    {
        parent::__construct(Nora::message('スクリプトファイル %s が存在しません',[
            $path,
            get_class($scope),
            $scope->getNames()
        ]));
    }
}
