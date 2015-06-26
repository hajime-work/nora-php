<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\Web\Response;

use Nora;
use Nora\Util\Util;


/**
 * レスポンス制御
 */
class Response extends Base
{
    /**
     * キャッシュヘッダーを付与する
     */
    public function cache($expires = 60*10, $last = null)
    {
        if ($last != null)
        {
            $this->header('Last-Modified', date('r', $last));
        }
        $this->header('Expires', gmdate('D, d M Y H:i:s T', time() + $expires));
        $this->header('Cache-Control', 'private, max-age='.$expires);
        $this->header('Pragma', 'cache');
        return $this;
    }

    /**
     * ファイルを送信する
     *
     * @param string $file
     */
    public function sendFile($file)
    {
        $this->header('Content-Type', Util::getContentType($file));
        
        if (!header_sent()) {
            $this->sendHeaders();
        }

        readfile($file);

        $this->callExit();
    }

}


/* vim: set foldmethod=marker : */
