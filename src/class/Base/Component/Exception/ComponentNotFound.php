<?php
/**
 * Nora Project
 * ============
 *
 * @since 20150618
 * @author kurari@hajime.work
 */
Namespace Nora\Base\Component\Exception;
use Nora\Base\Exception;
use Nora;


class ComponentNotFound extends Exception
{
    public function __construct($comp, $name)
    {
        parent::__construct(Nora::message('コンポーネントが見つかりません %s, %s, %s', [
            $name,
            $comp->scope()->getNames(),
            get_class($comp),
        ]));
    }
}
