<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\Database\Client\Base;

use Nora\Base\Component;
use Nora\Base\Database;
use Nora;

/**
 * DB Client用のFacade
 *
 */
abstract class Facade extends Component\Component
{
    private $_spec;

    public function __construct($spec)
    {
        $this->_spec = $spec;
    }

    protected function initComponentImpl( )
    {
        $this->initClient($this->_spec);
    }

    static public function make(Database\Facade $facade, $spec)
    {
        $class = get_called_class();

        $client = new $class($spec);
        $client->initComponent($facade->scope()->newScope($class));
        return $client;
    }

    abstract protected function initClient($spec);

}
