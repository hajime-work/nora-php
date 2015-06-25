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


class HelperNotDefined extends Exception
{
    public function __construct(ScopeIF $scope, $name, $params)
    {
        parent::__construct(Nora::message('ヘルパー %s は定義されていません (%s:%s)', [
            $name,
            get_class($scope),
            $scope->getNames()
        ]));
    }
}
