<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.1.0
 */
namespace Nora\Base\Logging\Writer;

use Nora\Base\Logging\Log;
use Nora\Base\Logging\LogLevel;
use Nora;
use Nora\Util\Spec\SpecLine;

/**
 * Fileライター
 */
class FileWriter
{
    public function __construct(SpecLine $spec)
    {
        $this->_path = $spec->path();

        // パスを環境変数でフォーマットする
        $this->_path = Nora::Environment()->info($this->_path);
    }

    public function write($log)
    {
        if (!file_exists($this->_path))
        {
            touch($this->_path);
        }
        if (!is_writable($this->_path))
        {
            throw new \Exception('ログファイルがオープンできません');
        }
        $fp = fopen($this->_path, 'a');
        fwrite($fp, $log."\n");
        fclose($fp);
    }
}
