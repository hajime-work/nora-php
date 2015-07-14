<?php
namespace Nora\Component;

use Nora\Base\Component;
use Nora\Data\Session\Facade as Base;

/**
 * セッション
 */
class Session extends Base
{
    protected function initComponentImpl( )
    {
        parent::initComponentImpl();

        $this->injection([
            'Configure',
            'FileSystem',
            function($c, $fs) {
                $conf = $c('session', []);

                if (!empty($conf['spec']))
                {
                    $this->connect($conf['spec']);
                }else{
                    $this->connect('dir://'. $fs->getPath('@cache'));
                }
            }
        ]);

        $this->start();
    }
}
