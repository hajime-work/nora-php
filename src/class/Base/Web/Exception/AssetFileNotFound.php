<?php
/**
 * Nora Project
 * ============
 *
 * @since 20150618
 * @author kurari@hajime.work
 */
Namespace Nora\Base\Web\Exception;
use Nora;


class AssetFileNotFound extends NotFound
{
    public function __construct($filename)
    {
        parent::__construct(Nora::message('アセットファイル %s が見つかりません', $filename));
    }
}
