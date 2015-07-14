<?php
namespace Nora\Base\Secure;

use Nora\Base\Component\Componentable;
use Nora;

/**
 * View Facade
 *
 */
class Auth
{
    private $_facade = null;

    public function __construct($facade)
    {
        $this->_facade = $facade;
    }

    /**
     * Basic認証
     *
     * @return string user
     */
    public function basic($cb, $message = 'AUTH')
    {
        if(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']))
        {
            $user = $_SERVER['PHP_AUTH_USER'];
            $pass = $_SERVER['PHP_AUTH_PW'];
            $hash = $cb($user);

            if($this->_facade->password()->verify($pass, $hash))
            {
                return $user;
            }
        }

        header('WWW-Authenticate: Basic realm="'.$message.'"');
        header('Content-Type: text/plain; charset=utf-8');
        die('このページを見るには認証が必要です');
    }
}
