<?php
namespace Nora\Network\Mail;

/**
 * メールパーサー
 */

class Parser 
{
    static public function parse($mail, $only_header = false)
    {
        $part = static::parsePart($mail, $only_header);
        $mail =  $part->toMail();
        $mail->setSize(strlen($mail));
        return $mail;
    }


    private static function parsePart($text, $only_header = false)
    {
        list($headers, $body) = static::parseHeaderBody($text);


        $part = new Mail\Part();
        foreach($headers as $k=>$v) $part->addHeader($k, $v);

        if ($part->isMultiPart($boundary))
        {
            foreach(
                static::parseMultiPart($boundary, $body, $only_header)
                as $child
            ) {
                $part->addPart($child);
            }
        }else{
            if ($only_header === false)
            {
                $part->setBody($part->decode($body));
            }
        }

        return $part;
    }

    private static function parseMultiPart($boundary, $body, $only_header = false)
    {
        $start = '--'.$boundary;
        $end   = '--'.$boundary.'--';

        $lines = preg_split("/(\r\n|\r|\n)/", $body);

        $started = false;
        $start_text = "";
        $count=-1;
        $parts = [];
        for($i=0;$i<count($lines);$i++)
        {
            $line = $lines[$i];

            if (trim($line) === $end)
            {
                yield static::parsePart($parts[$count], $only_header);
                break;
            }

            if (trim($line) === $start)
            {
                if ($count > -1)
                {
                    yield static::parsePart($parts[$count], $only_header);
                }
                $count++;
                continue;
            }

            if ($count === -1)
            {
                $start_text .= $line;
                continue;
            }

            if (isset($parts[$count]))
            {
                $parts[$count] .= "\n".$line;
            }else{
                $parts[$count] = $line;
            }
        }
    }
    public static function mimeHeaderDecode ($text)
    {
        foreach(imap_mime_header_decode($text) as $elem) {
            $charset = ($elem->charset == 'default') ? 'US-ASCII' : $elem->charset;
            $ret =  iconv($charset, "UTF-8//TRANSLIT", $elem->text);
        }
        return $ret;
    }

    private static function parseHeaderBody ($mail, $only_header = false)
    {
        // 改行コードのノーマライズ
        $lines = preg_split("/(\r\n|\r|\n)/", $mail);

        $isHeader = true;

        $body = "";
        $headers = [];
        $field = null;
        $value = null;

        for($i=0;$i<count($lines);$i++)
        {
            $line = $lines[$i];

            if ($isHeader === true && static::isEmpty($line))
            {
                $isHeader = false;
                $headers[$field] = $value;
                continue;
            }

            if ($isHeader) {
                if (static::isLineStartingWithPrintableChar($line))
                {
                    if(!preg_match('/^([^\s:]+): ?(.*)$/', $line, $matches))
                    {
                        continue;
                    }

                    if ($field !== null)
                    {
                        $headers[$field] = $value;
                    }

                    $field = null;
                    $value = null;

                    $field = $matches[1];
                    $value = $matches[2];
                }else{
                    $value.= "\n".$line;
                }
                continue;
            }

            if (empty($body))
            {
                if ($only_header === true)
                {
                    return [
                        $headers, ''
                    ];
                }
                $body = $line;
            }else{
                $body.= "\n".$line;
            }
        }
        return [
            $headers,
            $body
        ];
    }

    /**
     * 空行のチェック
     */
    static private function isEmpty($line)
    {
        $line = trim($line);
        return strlen($line) === 0;
    }

    /**
     * 継続行のチェック
     */
    static private function isLineStartingWithPrintableChar($line)
    {
        return preg_match('/^[A-Za-z]/', $line);
    }
}
