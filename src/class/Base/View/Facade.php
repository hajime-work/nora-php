<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\View;

use Nora\Base\Component\Componentable;
use Nora\Base\Component\ComponentLoader;
use Nora\Base\FileSystem;
use Nora\Exception;
use Nora;

/**
 * View Facade
 *
 */
class Facade
{
    use Componentable;

    private $_vm;
    private $_types = ['twig', 'php', 'html'];

    protected function initComponentImpl( )
    {
        $this->scope()->setComponent([
            'ViewModel' => function ( ) {
                return new ViewModel();
            },
            'FileLoader' => function ( ) {
                return new FileSystem\FileLoader( );
            }
        ])->setTag('view');

        $this->_engines = ComponentLoader::createComponent($this->scope()->newScope('ViewComponentLoader'));
        $this->_engines->addNameSpace(__namespace__.'\\Engine');
    }

    /**
     * 描画エンジンを取得する
     */
    public function getEngine($name)
    {
        return $this->_engines->getComponent($name);
    }

    /**
     * Viewが扱うViewModelを定義する
     */
    public function setViewModel(ViewModel $vm)
    {
        $this->scope()->setComponent('ViewModel', $vm);
        return $this;
    }

    /**
     * ViewModel の取得
     *
     * @return ViewModel
     */
    public function ViewModel( )
    {
        return $this->scope()->ViewModel();
    }

    /**
     * テンプレートファイルを取得する
     */
    public function getTemplateFile($file)
    {
        foreach($this->_types as $type)
        {
            $cand = $file.".".$type;

            if( $this->scope()->FileLoader()->hasSource($cand))
            {
                $template = $this->scope()->FileLoader()->getFilePath($cand);
                $this->logDebug("Found: ".$template);
                return $template;
            }
        }
        return false;
    }


    /**
     * Viewを描画する
     */
    public function render($file, $params = [])
    {
        if (!$template = $this->getTemplateFile($file)) {
            throw new Exception\FileNotFound($file);
        }

        $ext = substr($template, strrpos($template,'.')+1);

        return $this->getEngine($ext)->render($file, $params);
    }
}
