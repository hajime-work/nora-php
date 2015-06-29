<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\Cache;

use Nora\Base\Component;
use Nora\Base\Hash;
use Nora\Util\Spec\SpecLine;
use Nora;

/**
 * キャッシュ
 *
 * <code>
        $DB('fspot')->query('show tables')->each(function($raw) {
            var_Dump($raw);
        });
        $DB('fspot-mongo')->getCollection('xhamster');
        $DB('fspot-redis')->set('a','b');
        var_Dump($DB('fspot-redis')->get('aa'));

        $Cache->test->set('a', 'b');
        echo $Cache->useCache('aaa', function (&$st) {

            $st = true;

            return date('Y/m/d G:i:s');

        }, -1, fileatime(__file__));
 * </code>
 */
class Facade extends Client
{
    use Component\Componentable;

    public function __construct( )
    {
    }

    protected function initComponentImpl( )
    {
        $this->setPrefix('cache');
    }

    protected function initCache($conf)
    {
        $spec = new SpecLine($conf['spec']);
        $class = sprintf(__namespace__.'\\Handler\%s', ucfirst($spec->scheme()));

        $handler = new $class($spec);
        $handler->initComponent($this->scope()->newScope($class));
        $this->setHandler($handler);
    }

    public function __get($name)
    {
        return new Client($this, $name);
    }

}
