<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\Database\Util;

use Nora\Base\Component;
use Nora\Base\Hash;
use Nora;

/**
 * データベース
 *
 */
class SpecLine extends Hash\Hash
{
    private $_raw;

    public function __construct($string)
    {
        $this->_raw = $string;
        $this->initValues(parse_url($this->_raw));
    }

    public function getScheme( )
    {
        return $this->getVal('scheme');
    }


}
