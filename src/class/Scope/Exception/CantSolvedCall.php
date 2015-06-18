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
use Nora\Nora;


class CantSolvedCall extends Exception
{
    public function __construct(ScopeIF $scope, $name, $params)
    {
        parent::__construct(Nora::message('コール %s は解決できません。(%s:%s) Argument: %s', [
            $name,
            get_class($scope),
            $scope->getNames(),
            Nora::debugParams($params)
        ]));
    }
}
