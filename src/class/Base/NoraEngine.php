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

/**
 * ノラのベースクラス
 *
 */
class NoraEngine extends Component\Component
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
        // スコープにコンポーネントローダを設定する
        $this->scope()->addCallMethod(
            $this->scope()->componentLoader =
                Component\ComponentLoader::createComponent(
                    $this->scope()->newScope('ComponentLoader')
                )
                ->addNameSpace('Nora\Component') // デフォルトのネームスペースをロード対象にする
            );

    }

    /**
     * ヘルプを表示する
     */
    public function help($object)
    {
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
