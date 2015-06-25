<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base;

use Nora\Scope;
use Nora\CLI\Output;
use Nora\Util\Reflection\ReflectionClass;
use Nora\App\App;

/**
 * ノラのベースクラス
 *
 */
class NoraEngine extends App
{
    public function __construct( )
    {
        parent::__construct();
        // メインのスコープの名称を[Nora]にする
        $this->initComponent(Scope\Scope::createScope('Nora'));
    }

    /**
     * スコープの設定
     */
    protected function initComponentImpl( )
    {
        parent::initComponentImpl();
        $this->scope()->componentLoader->addNameSpace('Nora\Component'); // デフォルトのネームスペースをロード対象にする
    }

    /**
     * 初期化する
     *
     * @param string $dir
     * @param string $env
     */
    public function initialize($dir, $env)
    {
        $this->timer('start');

        $this->_dir = $dir;

        // 設定を読み込む
        $this->Configure()->write('root', $dir);
        $this->Configure()->load($dir.'/config', [
            'default',
            $env
        ]);

        $this->timer('configure');

        // 全体的な設定
        mb_language($this->Configure('lang', 'ja'));
        mb_internal_encoding($this->Configure('encoding', 'utf-8'));

        // アプリケーション環境を起動
        $this->_env = $this->EnvFactory($env);
        $this->_env->setup($this);

        // ロガーをアタッチしてハンドラを登録
        $this->Environment()->attach($this->Logger())->register();
        $this->scope()->attach($this->Logger());

        // サブアプリケーションを設定する
        foreach($this->Configure('app', []) as $name => $config)
        {
            $this->addApp($name, $config);
        }

        // ファイルシステムを設定
        $this->FileSystem( )
            ->setRoot($this->Configure('filesSystem.root', $dir))
            ->alias($this->Configure('fileSystem.aliases',[]));

       
        $this->componentLoader->addNameSpace(
            $this->Configure('component.ns', [])
        );

        $this->timer('initialize');
    }

    /**
     * ファイルパスを取得
     */
    public function getFilePath( )
    {
        return call_user_func_array([$this->FileSystem(),'getPath'], func_get_args());
    }

    /**
     * ヘルプを表示する
     */
    public function help($object)
    {
        if (! is_object($object) )
        {
            var_Dump($object);
            return;
        }

        $rc = new ReflectionClass($object);
        $list = [];
        foreach($rc->getPublicMethods() as $m)
        {
            if($m->hasAttr('NoHelp')) continue;

            $list[] = [
                " ".$m->toString(),
                $m->comment()
            ];
        }
        Output::title($rc->getName(), 'help');
        Output::p($rc->comment());
        Output::table($list);
    }
}