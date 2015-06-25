<?php
/**
 * Nora Project
 * ============
 *
 * @since 20150618
 * @author kurari@hajime.work
 */
Namespace Nora\Util\Spec;

use Nora\Base\Hash\Hash;

/**
 * スペックを表す構造体
 *
 * {スキーマ}://{パス}?data=data
 */
class SpecLine
{
    private $_string;
    private $_parsed;

    static public function parse ($string)
    {
        $sl = new SpecLine($string);
        return $sl;
    }

    public function __construct($string)
    {
        $this->_parsed = Hash::newHash(parse_url($string), Hash::OPT_ALLOW_UNDEFINED_KEY);
    }

    public function scheme($default)
    {
        return $this->_parsed->getVal('scheme', $default);
    }

    public function host($default = 'localhost')
    {
        return $this->_parsed->getVal('host', $default);
    }

    public function path($default = '/')
    {
        return $this->_parsed->getVal('path', $default);
    }




}

/* vim: set foldmethod=marker st=4 ts=4 sw=4 et: */
