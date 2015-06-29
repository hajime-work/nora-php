<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\Shell;

use Nora\Base\Component\Componentable;
use Nora\Base\Component\ComponentLoader;
use Nora\Base\FileSystem;
use Nora;

/**
 * Shellプロセス
 *
 */
class Proc
{
    private $_cmd;
    private $_options = [];
    private $_data = "";

    public function __construct($cmd)
    {
        $this->_cmd = $cmd;
    }

    public function setOptions( )
    {
        foreach(func_get_args() as $v)
        {
            if (is_array($v)) {
                foreach($v as $vv) $this->setOptions($vv);
            }else{
                $this->_options[] = $v;
            }
        }
        return $this;
    }

    public function write($data)
    {
        $this->_data .= $data;
        return $this;
    }

    public function build( )
    {
        $str = escapeshellcmd($this->_cmd);

        foreach($this->_options as $v)
        {
            $str.= ' '.escapeshellarg($v);
        }

        return $str;
    }

    public function execute( )
    {
        $proc = proc_open(
            $this->build(),
            [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w']
            ],
            $pipes
        );

        if (!is_resource($proc)) {
            throw new Exception\ProcCantOpen($this);
        }

        fwrite($pipes[0], $this->_data);

        fclose($pipes[0]);
        $stdout = tmpfile();
        $stderr = tmpfile();
        stream_copy_to_stream($pipes[1], $stdout);
        stream_copy_to_stream($pipes[2], $stderr);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $ret = proc_close($proc);
        if ($ret > 0)
        {
            rewind($stderr);
            throw new Exception\ProcError($ret, $this->build(), stream_get_contents($stderr));
        }

        rewind($stdout);
        $data = stream_get_contents($stdout);
        return $data;
    }
}
