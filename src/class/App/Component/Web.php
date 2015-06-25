<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\App\Component;

use Nora\Base\Component\Componentable;
use Nora\Base\Configuration\Configure as Base;

/**
 * AppのWeb用コンポーネント
 */
class Web extends Base
{
    use Componentable;

    protected function initComponentImpl( )
    {
        $this->scope()->injection([
            'Environment',
            'FileSystem',
            'Configure',
            function ($e, $f, $c)
            {
                $this->initWeb($e, $f, $c);
            }
        ]);
    }

    public function initWeb($env, $fs, $conf)
    {
        $web = $fs->getPath('web/routing.php');
        var_dump($web);
    }

    public function __invoke($client, $params)
    {
        return $this;
    }
}
