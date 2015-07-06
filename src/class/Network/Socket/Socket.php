<?php
namespace Nora\Network\Socket;


/**
 * Socket
 */
class Socket 
{
    public static function readBuffer($resource)
    {
        $buffer               = '';
        $buffersize           = 3;
        $meta['unread_bytes'] = 0;

        while(!feof($resource))
        {
            $result = fread($resource, $buffersize);

            if ($result === false) break;

            $buffer.= $result;
            $meta= stream_get_meta_data($resource);
            $buffersize = ($meta['unread_bytes'] > $buffersize) ? $buffersize: $meta['unread_bytes'];


            if ($meta['unread_bytes'] < 1) break;
        };

        return $buffer;
    }

    public static function writeBuffer($resource, $string)
    {
        $len = strlen($string);

        for($done = 0; $done < $len; $done += $written)
        {
            $written = @fwrite($resource, substr($string, $done));
            if ($written === false) return false;
            elseif($written === 0) return false;
        }

        return $done;
    }
}
