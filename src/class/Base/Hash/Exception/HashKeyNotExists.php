<?php
/**
 * Nora Project
 * ============
 *
 * @since 20150618
 * @author kurari@hajime.work
 */
Namespace Nora\Base\Hash\Exception;
use Nora\Base\Exception;
use Nora\Base\Hash\HashIF;
use Nora;

class HashKeyNotExists extends Exception
{
    public function __construct(HashIF $hash, $key)
    {
        parent::__construct(Nora::message('ハッシュキー %s は定義されていません。(%s)', [$key, get_class($hash)]));
    }
}
