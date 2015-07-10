<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Component;

use Nora\Base\Component\Componentable;
use Nora\Data\Cache\Facade as Base;

/**
 * キャッシュコンポーネント
 */
class Cache extends Base
{
    use Componentable;

    protected function initComponentImpl( )
    {
        $this->injection([
            'Configure',
            'DataBase',
            'FileSystem',
            function($c, $db, $fs) {
                $conf = $c('cache', []);
                $this->setDBHandler($db);

                if (!empty($conf['spec']))
                {
                    $this->connect($conf['spec']);
                }else{
                    $this->connect('dir://'. $fs->getPath('@var/session'));
                }
            }
        ]);
    }
}
