<?php
namespace Nora\Network\HTTP;

use Nora\Base\Event;
use Nora\Base\Component\Component;
use Nora\Network\API\Twitter;

/**
 * Mime Utility
 */
class Mime extends Component
{
    const TEMP_DIR='/tmp';
    const BUF=1024;

    private $_finfo;

    protected function initComponentImpl( )
    {
        $this->injection([
            'Finfo', function($finfo) {
                $this->_finfo = $finfo;
            }
        ]);
    }

    public function parseFile($file)
    {
        $fp = fopen($file, 'r');
        $result = $this->parseFP($fp);
        fclose($fp);

        return $result;
    }

    public function parseFP($fp)
    {
        // 1行目はバウンダリ
        $boundary = trim(fgets($fp, self::BUF));

        // 1行毎に処理をする
        $isHeader = true;
        $isFirst = true;
        $i = 0;
        $datas = [];
        while(false !== $line = fgets($fp, self::BUF))
        {
            if ($isHeader)
            {
                $line = trim($line);
            }


            $isBoundary = $boundary === trim($line);
            $isEnd = $boundary.'--' === trim($line);

            if ($isEnd) {
                $fixMap = [
                    'true'      => true,
                    'false'     => false,
                    'null'      => null,
                    'undefined' => null,
                ];

                foreach($datas as &$v)
                {
                    if (is_resource($v['data'])) {
                        fclose($v['data']);

                        $v['data'] = [
                            'tempfile' => $v['tempfile'],
                            'filesize' => filesize($v['tempfile']),
                            'filename' => $v['filename'],
                            'type'     => $this->_finfo->getMimeType($v['tempfile'])
                        ];
                    }else{
                        $v['data'] = trim($v['data']);

                        if (array_key_exists(strtolower($v['data']), $fixMap))
                        {
                            $v['data'] = $fixMap[strtolower($v['data'])];
                        }

                    }
                }
                continue;
            }

            if ($isBoundary)
            {
                $isFirst = true;
                continue;
            }

            if ($isFirst)
            {
                $isFirst = false;
                $isHeader = true;
                $datas[$i] = [
                    'headers' => []
                ];
                $data =& $datas[$i];
                $i++;
            }

            // ヘッダーが終了した
            if ($isHeader && empty($line))
            {
                $isHeader = false;
                if(isset($data['headers']['content-disposition']))
                {
                    $attrs = $this->parseHeaderValues($data['headers']['content-disposition']);
                    foreach(['name','filename'] as $k)
                    {
                        if (isset($attrs[$k])) $data[$k] = $attrs[$k];
                    }
                }

                if(isset($data['headers']['content-type'])) // コンテントタイプがついてたら
                {
                    $data['content-type'] = $data['headers']['content-type'];
                    $file = tempnam(self::TEMP_DIR, 'files-');
                    $data['tempfile'] = $file;
                    $data['data'] = fopen($file, 'w');
                    $putter = function($v) use ($data) {
                        fwrite($data['data'], $v);
                    };
                }else{
                    $data['data'] = "";
                    $putter = function($v) use (&$data) {
                        $data['data'].= $v;
                    };
                }
                continue;
            }

            if ($isHeader)
            {
                // 開始ヘッダ
                if (preg_match('/^([^\s]+):(.+)/', $line, $m))
                {
                    $key = strtolower($m[1]);
                    $data['headers'][$key] = "";

                    $header =& $data['headers'][$key];
                    $header = trim($m[2]);
                }else{
                    $header.= trim($line);
                }
            }else{
                $putter($line);
            }
        }
        return $datas;
    }

    public function parseHeaderValues($string)
    {
        $values = [];
        $parts = array_map('trim', explode(';', $string));
        foreach($parts as $part)
        {
            if (false === $p = strpos($part, '='))
            {
                $key = $part;
                $val = true;
            }else{
                $key = substr($part, 0, $p);
                $val = trim(trim(substr($part, $p+1)), '"\'');
            }

            $values[strtolower($key)] = $val;
        }
        return $values;;
    }
}
