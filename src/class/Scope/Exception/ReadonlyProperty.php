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


class ReadonlyProperty extends Exception
{
    public function __construct(ScopeIF $scope, $key)
    {
        parent::__construct(Nora::message('プロパティ %s は読み込み専用です。(%s:%s)', [$key, get_class($scope), $scope->getNames()]));
    }
}
