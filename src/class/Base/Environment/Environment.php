<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\Environment;

use Nora\Base\Hash\ObjectHash;
use Nora\Base\Hash\Hash;
use Nora\Base\Event;

/**
 * 環境クラス
 *
 * イベントパターンを適用する
 */
class Environment
{
    use Event\SubjectTrait;

    public function __construct( )
    {
        $this->_detector = new ObjectHash(); 
        $this->addDetector('Basic');

        $this->_env_vars = Hash::newHash($_ENV + $_SERVER, Hash::OPT_IGNORE_CASE|Hash::OPT_ALLOW_UNDEFINED_KEY);
    }

    /**
     * 環境検知器を足す
     */
    public function addDetector($name)
    {
        if (is_array($name))
        {
            foreach($name as $v) $this->addDetector($v);
            return $this;
        }

        $class = sprintf(__namespace__.'\Detector\%sDetector', ucfirst($name));
        $this->_detector->add(new $class($this));
    }

    /**
     * 環境を判定する
     *
     * @param string $name
     * @return bool
     */
    public function is($name)
    {
        foreach($this->_detector as $d)
        {
            if( $d->has($name) )
            {
                return $d->is($name);
            }
        }

        throw new Exception\DetectorNotFound($name);
    }

    /**
     * 環境変数を取得する
     *
     * @param string $name
     * @param mixed $default
     */
    public function getEnv($name = null, $default = null)
    {
        if ($name === null) return $this->_env_vars;
        return $this->_env_vars->getVal($name, $default);
    }

    /**
     * 環境変数を設定する
     *
     * @param string|array $name
     * @return Environment
     */
    public function setEnv($name, $value = null)
    {
        if (is_array($name)) {
            foreach ($name as $k=>$v) {
                $this->setEnv($k, $v);
            }
            return $this;
        }

        $this->_env_vars->setVal($name, $value);

        return $this;
    }

    /**
     * クライアントのIPを取得
     *
     * @return string
     */
    public function getClientIP( )
    {
        if ($ip = $this->getEnv('HTTP_X_REAL_IP'))
        {
            return $ip;
        }

        return $this->getEnv('REMOTE_ADDR');
    }

    /**
     * プロセスのオーナを取得
     *
     * @return string
     */
    public function getPosixUser( )
    {
        return posix_getpwuid(posix_getuid())['name'];
    }

    /**
     * ホスト名を取得
     */
    public function getHost()
    {
        return gethostname();
    }

    public function getDate( )
    {
        return date("Y-n-d G:i:s");
    }

    public function getUserAgent( )
    {
        return $this->getEnv('HTTP_USER_AGENT', 'cli');
    }

    /**
     * システム情報を取得する
     *
     * %host = ホスト
     * %user = ユーザ
     * %date = datetime
     * %ua = UserAgent
     */
    public function info($format = "%h%u")
    {
        return preg_replace_callback('/%\{*([a-zA-Z_]+)\}*/', function ($m) {

            switch($m[1])
            {
            case 'host':
                return $this->host();
                break;
            case 'user':
                return $this->posixUser();
                break;
            case 'date':
                return $this->getDate();
                break;
            case 'ua':
                return $this->userAgent();
                break;
            case 'Y':
                return date('Y');
                break;
            case 'm':
                return date('m');
                break;
            case 'd':
                return date('d');
                break;
            case 'H':
                return date('H');
                break;
            case 'G':
                return date('G');
                break;
            case 'i':
                return date('i');
                break;
            case 's':
                return date('s');
                break;
            default:
                return $m[0];
            }

        }, $format);
    }


    /**
     * 各種ハンドラの登録
     */
    public function register ( )
    {
        // ハンドラ系の登録
        set_error_handler([$this, 'phpErrorHandler']);
        set_exception_handler([$this, 'phpExceptionHandler']);
        register_shutdown_function([$this, 'phpShutdownHandler']);
        return $this;
    }

    /**
     *  int $errno , string $errstr [, string $errfile [, int $errline [, array $errcontext ]
     */
    public function phpErrorHandler($errno, $errstr, $errfile, $errline, $errcontext)
    {
        $this->fire('php.error', [
            'errno'      => $errno,
            'errstr'     => $errstr,
            'errfile'    => $errfile,
            'errline'    => $errline,
            /*            'errcontext' => $errcontext */
        ]);
    }

    /**
     */
    public function phpExceptionHandler($exception)
    {
        $this->fire('php.exception', [
            'exception' => $exception
        ]);
    }

    /**
     */
    public function phpShutdownHandler( )
    {
        $error = error_get_last();
        if ($error['type'] > 0) {
            $this->phpErrorHandler(
                $error['type'],
                $error['message'],
                $error['file'],
                $error['line'],
                []
            );
        }
        $this->fire('php.shutdown');
    }


    public function __call($name, $params = null)
    {
        static $res;

        $method = 'get'.ucfirst($name);

        if (!method_exists(__class__, $method))
        {
            throw new \Nora\Exception\UndefinedMethod($this, $name);
        }

        if (!isset($res[$method]))
        {
            $res[$method] = call_user_func([$this, $method]);
        }
        return $res[$method];
    }
}
