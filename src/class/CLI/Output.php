<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
Namespace Nora\CLI;

/**
 * CLIツール:出力ツール
 */
class Output
{
    static public function title($string, $subtitle = null)
    {
        printf(PHP_EOL);
        printf(PHP_EOL);
        printf("--- $string ".($subtitle == null? '': '('.$subtitle.')')."---");
        printf(PHP_EOL);
    }

    static public function p($string)
    {
        printf(PHP_EOL);
        printf(PHP_EOL);
        $lines = preg_split('/[\r\n]/', $string);
        foreach($lines as $line)
        {
            printf("  ".$line.PHP_EOL);
        }

        printf(PHP_EOL);
        printf(PHP_EOL);
    }

    static public function table($list, $sp = ' | ')
    {
        $col_len = [];

        // 長さを測りつつ整形する必要がある(3行あったら)
        foreach($list as $v)
        {
            foreach($v as $k=>$v)
            {
                //$len = strlen($v);
                $lines = preg_split('/[\r\n]/', $v);
                foreach($lines as $line)
                {
                    $len = mb_strwidth($line);
                    $col_len[$k] = $len < $col_len[$k] ? $col_len[$k]: $len;
                }
            }
        }

        $total_len = 0;

        foreach($col_len as $v)
        {
            $total_len += $v;
        }


        // 出力する
        foreach($list as $v)
        {
            $raw = '';

            $col_cnt = 0;
            foreach($v as $k=>$v)
            {
                $lines = preg_split('/[\r\n]/', $v);

                $cnt = 0;
                foreach($lines as $line) {

                    if ($cnt === 0)
                    {
                        $raw.= sprintf('%-'.$col_len[$k].'s', $line);
                    }else{
                        $raw.= PHP_EOL.sprintf(str_repeat(' ', $offset).$sp.'%-'.$col_len[$k].'s', $line);
                    }
                    $cnt++;
                }
                if($col_cnt !== count($v))
                {
                    $raw.= $sp;
                }
                $col_cnt++;
                $offset = $col_len[$k];
            }
            $raw.= PHP_EOL;
            $raw.=str_repeat('-', $total_len);
            $raw.= PHP_EOL;
            echo $raw;
        }
    }

}
