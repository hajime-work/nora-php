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
use Nora\Base\Component\Component as Base;

class FileSystem extends Base
{
    protected function initComponentImpl( )
    {
        $this
            ->scope()
            ->makeHelpers($this);

        $this->scope()->initValues([
            '_alias' => []
        ]);
    }

    public function status($show = false)
    {
        $status =  [
            'id' => spl_object_hash($this),
            'alias' => $this->_alias
        ];

        if ($show === false) return $status;

        var_export($status);
    }

    /**
     * エイリアスを設定する
     *
     * @helper
     */
    private function alias($name, $path)
    {
        $this->_alias[$name] = $path;
        return $this;
    }
}
