<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Network\Mail;

/**
 * Mailの構造体
 */
class Part
{
    private $_body        = '';
    private $_headers     = [];
    private $_parts       = [];

    public function toMail( )
    {
        $mail = new Mail( );
        $mail->_body = $this->_body;
        $mail->_headers = $this->_headers;
        $mail->_parts = $this->_parts;
        return $mail;
    }

    public function addHeader($field, $value = null)
    {
        if (is_array($field))
        {
            foreach($field as $k=>$v) $this->addHeader($k, $v);
            return $this;
        }
        $this->_headers[$field] = $value;
        return $this;
    }

    public function getHeaders( )
    {
        return $this->_headers;
    }

    public function getHeader($field, &$attrs = null)
    {
        foreach($this->_headers as $k => $v)
        {
            if (strtolower($k) === strtolower($field))
            {
                $parts = explode(';', $v);

                if ($parts === 1)
                {
                    return $v;
                }
                $v = preg_replace('/\r\n\s/','',$v);

                $attrs = [];
                for($i=1; $i<count($parts); $i++)
                {
                    $line = $parts[$i];
                    list($key, $value) = explode('=', $line, 2);
                    $attrs[trim($key)] = trim($value, ' "\r\n\'');
                }

                return $parts[0];
            }
        }
    }
    public function getHeaderRaw($field)
    {
        foreach($this->_headers as $k => $v)
        {
            if (strtolower($k) === strtolower($field))
            {
                return preg_replace('/[\r\n\s]/','',$v);
            }
        }
    }

    public function isMultiPart (&$boundary = null)
    {
        $type = $this->getHeader('content-type', $attrs);
        if (0 === stripos($type, 'multipart'))
        {
            if (!isset($attrs['boundary']))
            {
                $type = $this->getHeaderRaw('content-type');
                var_dump($type);
                var_Dump($attrs);
                throw new \Exception('boundary not found');
            }
            $boundary = $attrs['boundary'];
            list($multi, $type) = explode('/', $type, 2);
            return $type;
        }
        return false;
    }

    public function addPart($part)
    {
        $this->_parts[] = $part;
    }

    public function mb_decode($body)
    {
        $type = $this->getHeader('Content-Type', $attrs);

        if (0 === stripos($type, 'text'))
        {
            $charset = isset($attrs['charset']) ? $attrs['charset']: false;
            if ($charset !== false)
            {
                $body = mb_convert_encoding(
                    $body, mb_internal_encoding(), $charset);
            }
        }
        return $body;
    }

    public function mb_encode($body)
    {
        $type = $this->getHeader('Content-Type', $attrs);

        if (0 === stripos($type, 'text'))
        {
            $charset = isset($attrs['charset']) ? $attrs['charset']: false;
            if ($charset !== false)
            {
                $body = mb_convert_encoding(
                    $body, $charset, mb_internal_encoding());
            }
        }
        return $body;
    }

    public function decode ($body)
    {
        $encode = $this->getHeader('Content-Transfer-Encoding');


        if (0=== stripos($encode,'quoted-printable'))
        {
            $body = $this->mb_decode(quoted_printable_decode($body));
        }elseif(0===stripos($encode,'base64')) {
            $body = $this->mb_decode(base64_decode($body));
        }elseif(
            0===stripos($encode,'binary') 
            ||
            0===stripos($encode,'7bit')
            ||
            0===stripos($encode,'8bit')) {
                $body = $this->mb_decode($body);
        }

        return $body;
    }

    public function encode ($body)
    {
        $encode = $this->getHeader('Content-Transfer-Encoding');


        if (0=== stripos($encode,'quoted-printable'))
        {
            $body = quoted_printable_encode($this->mb_encode($body));
        }elseif(0===stripos($encode,'base64')) {
            $body = chunk_split(base64_encode($this->mb_encode($body)));
        }elseif(
            0===stripos($encode,'binary') 
            ||
            0===stripos($encode,'7bit')
            ||
            0===stripos($encode,'8bit')) {
                // Not Encoded
        }

        return $body;
    }

    public function setBody ($body)
    {
        $this->_body = $body;
    }

    public function getBody ( )
    {
        return $this->_body;
    }


    public function status($cnt = 0)
    {
        $txt = str_repeat("\t", $cnt).$this->getHeaderRaw('content-type');
        $txt.= "\n";
        $cnt++;
        foreach($this->_parts as $part)
        {
            $txt.= $part->status($cnt);
        }
        return $txt;
    }

    public function searchPart($cb)
    {
        $result = [];

        foreach($this->_parts as $part)
        {
            if (false !== $res = $part->searchPart($cb))
            {
                foreach($res as $child)
                {
                    $result[] =  $child;
                }
            }
            if($cb($part))
            {
                $result[] =  $part;
            }
        }
        return count($result) === 0 ? false: $result;
    }

    public function searchPartByContentType($type)
    {
        return $this->searchPart(function($part) use ($type) {
            return 0 === stripos($part->getHeader('Content-Type'), $type);
        });
    }

    public function getParts ( )
    {
        return $this->_parts;
    }

    /**
     * HTMLメール
     */
    public function html($body)
    {
        $this->addHeader('Content-Type', 'text/html; charset=utf8');
        $this->addHeader('Content-Transfer-Encoding', 'quoted-printable');

        $this->_body = $body;
        return $this;
    }

    /**
     * Plainメール
     */
    public function plain($body)
    {
        $this->addHeader('Content-Type', 'text/plain; charset=utf8');
        $this->addHeader('Content-Transfer-Encoding', 'quoted-printable');

        $this->_body = $body;
        return $this;
    }

    /**
     * アタッチメント
     */
    public function attachment ($body, $name, $contentType)
    {
        $this->addHeader('Content-Type', $contentType);
        $this->addHeader('Content-Transfer-Encoding', 'base64');
        $this->addHeader('Content-Disposition', 'attachment; filename="'.$name.'"');

        $this->_body = $body;
        return $this;
    }

    /**
     * インラインアタッチメント
     */
    public function inline ($body, $name, $contentType, $id)
    {
        $this->addHeader('Content-Type', $contentType);
        $this->addHeader('Content-Transfer-Encoding', 'base64');
        $this->addHeader('Content-Disposition', 'inline; filename="'.$name.'"');
        $this->addHeader('Content-ID', '<'.$id.'>');

        $this->_body = $body;
        return $this;
    }

    /**
     * マルチパートミクスド
     */
    public function mixed ( )
    {
        $hash= md5(date('r', time()));
        $boundary = '=Mixed-'.$hash;

        $this->addHeader('Content-Type', 'multipart/mixed;'.
            "\n"."\tboundary=\"$boundary\"");

        foreach(func_get_args() as $spec)
        {
            $part = new Mail\Part( );

            if ($spec['type'] === 'html')
            {
                $part->html($spec['body']);
            }elseif($spec['type'] === 'plain') {
                $part->plain($spec['body']);
            }elseif($spec['type'] === 'alternative') {
                call_user_func_array([$part, 'alternative'], $spec['parts']);
            }elseif($spec['type'] === 'attachment') {
                $part->attachment($spec['body'], $spec['name'], $spec['content-type']);
            }elseif($spec['type'] === 'inline') {
                $part->inline($spec['body'], $spec['name'], $spec['content-type'], $spec['id']);
            }
            $this->addPart($part);
        }
        return $this;
    }

    /**
     * マルチパートオルタナティブ
     */
    public function alternative( )
    {
        $hash= md5(date('r', time()));
        $boundary = '=Alternative-'.$hash;

        $this->addHeader('Content-Type', 'multipart/alternative;'.
            "\n"."\tboundary=\"$boundary\"");

        foreach(func_get_args() as $spec)
        {
            if ($spec['type'] === 'html')
            {
                $part = new Mail\Part( );
                $part->html($spec['body']);
            }else{
                $part = new Mail\Part( );
                $part->plain($spec['body']);
            }
            $this->addPart($part);
        }

        return $this;
    }

    /**
     * メールテキストへ変換
     */
    public function __toString( )
    {
        $text = "";
        foreach($this->getHeaders() as $k => $v)
        {
            if (false === array_search(
                strtolower($k), ['x-original-to', 'delivered-to', 'received', 'return-path']))
            {
                $text.= sprintf("%s: %s\n", $k, $v);
            }
        }
        $text.="\n";

        if ($this->isMultiPart($boundary))
        {
            $text.= "This is multipart";
            $text.= "\n";
            $text.= "\n";
            foreach($this->_parts as $p)
            {
                $text.= "--".$boundary;
                $text.= "\n";
                $text.= (string) $p;
                $text.= "\n";
            }
            $text.= "--".$boundary."--";
        }else{
            $text.= $this->encode($this->_body);
        }

        return $text;
    }
}

