<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Model;

use Nora\Base\Component;
use Nora\Base\Hash;
use Nora\Util\Spec\SpecLine;
use Nora;

/**
 * モデル
 */
class Facade extends Component\Component
{
    private $_ds;

    public function initComponentImpl( )
    {
        $this->_registered = Nora::Hash();

        $this->injection(['DataSource', function ($ds) {

            $this->_ds = $ds;

        }]);
    }

    /**
     * モデルを登録する
     *
     * @param string $name
     * @param array $spec
     * @return Facade
     */
    public function register($name, $spec = [])
    {
        if (is_array($name))
        {
            foreach($name as $k=>$v) $this->register($k, $v);
            return $this;
        }

        $this->_registered[$name] = $spec;
        return $this;
    }

    /**
     * @param string $name
     */
    public function getModelHandler($name)
    {
        // モデル用のデータソースを取得する
        $ds = $this->_ds->getDataSource($name);

        // 後ですげ替えられるようにクラス名を変数に一旦とる
        $class = __namespace__.'\\Base\\ModelHandler';

        // モデルハンドラを作成
        return new $class($this, $ds);
    }

    /**
     * @param string $name
     */
    public function __invoke($name)
    {
        return $this->getModelHandler($name);
    }
}
