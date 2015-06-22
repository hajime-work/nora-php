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
use Nora\Base\Component\Component;
use Nora\Base\FileSystem\FileSystem as Base;

class FileSystem extends Component
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

    /**
     * 実体を取り出す
     */
    private function getFileSystem($client)
    {
        if (!isset($client->fileSystem))
        {
            $client->scope()->setWriteOnceProp('fileSystem', new Base());
        }
        return $client->scope()->fileSystem;
    }

    /**
     * エイリアスを設定する
     *
     * @helper
     * @inject scope
     */
    private function alias($scope, $name, $path)
    {
        $this->getFileSystem($scope->owner)->alias($name, $path);
        return $this->getFileSystem($scope->owner);
    }

    /**
     * ファイルパスを取得する
     *
     * @helper
     * @inject scope
     */
    private function getFilePath($scope)
    {
        return $this->getFileSystem($scope->owner)->getPath(
            array_slice(func_get_args(), 1)
        );
    }

    public function __invoke($client, $params)
    {
        if (empty($params))
        {
            return $this;
        }

        return call_user_func_array([$this->getFileSystem($client), 'getPath'], $params);
    }
}
