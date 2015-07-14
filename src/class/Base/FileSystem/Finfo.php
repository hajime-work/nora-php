<?php
namespace Nora\Base\FileSystem;

use Nora\Base\Component\Component;
use Nora\Base\Component\Componentable;


class Finfo extends Component
{
    private $_finfo_mime;

    protected function initComponentImpl( )
    {
        $this->_finfo_mime = finfo_open(FILEINFO_MIME);
    }

    public function getMimeType($arg)
    {
        return finfo_file($this->_finfo_mime, $arg);
    }

    public function getExt($file)
    {
        $type = strtok($this->getMimeType($file), ';');
        return $this->type_to_ext($type);
    }

    static public function type_to_ext($type)
    {
        $type = strtolower($type);

        switch($type)
        {
        case 'image/jpeg':
        case 'image/jpg':
            return 'jpeg';
            break;
        case 'image/png':
            return 'png';
            break;
        case 'image/gif':
            return 'gif';
            break;
        }

        throw new \Exception(Nora::message('サポートされてないタイプです'));
    }
}
