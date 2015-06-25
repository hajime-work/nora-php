<?php
/**
 * Nora Project
 * ============
 *
 * @since 20150618
 * @author kurari@hajime.work
 */
Namespace Nora\App;

use Nora\Base\Component\Component;
use Nora\Base\Component\ComponentLoader;
use Nora\Base\Hash\ObjectHash;

class App extends Base
{

    protected function initComponentImpl( )
    {
        parent::initComponentImpl();

        $this
            ->scope()
            ->componentLoader
            ->addNameSpace('Nora\App\Component'); // アプリケーション用のネームスペースをロード対象にする
    }

    /**
     * 有効化された時の挙動
     */
    public function onEnable( )
    {
        $this->initComponent();

        // オートローダの設定
        $this->AutoLoader(
            $this->Configure()->read('app_autoload', [])
        );
    }
}
/* vim: set foldmethod=marker st=4 ts=4 sw=4 et: */
