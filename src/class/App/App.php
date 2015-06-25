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

class App extends Component 
{

    private $_env;
    private $_dir;
    private $_name = 'app:default';
    private $_config = [];
    private $_children = [];
    private $_enabled = [];

    public function __construct( )
    {
        $this->_children = new ObjectHash();
    }


    protected function initComponentImpl( )
    {
        // スコープにコンポーネントローダを設定する
        $this->scope()->addCallMethod(
            $this->scope()->componentLoader =
                ComponentLoader::createComponent(
                    $this->scope()->newScope('ComponentLoader')
                )
            );
    }

    /**
     * アプリケーション環境クラスを取得する
     */
    public function EnvFactory($name)
    {
        $class = sprintf('Nora\App\Env\%sEnv', ucfirst($name));
        return new $class();
    }


    static public function create ($name, $config)
    {
        $app = new App();
        $app->_name = $name;
        $app->_config = $config;
        return $app;
    }

    public function add(App $app)
    {
        $this->_children->add($app);
        return $this;
    }

    /**
     * アプリケーションを追加する
     */
    public function addApp($name, $config)
    {
        $app = App::create($name, $config);

        // ルートの子スコープを渡す
        $app->setScope($this->scope()->newScope());

        $this->add($app);
    }

    /**
     * アプリケーションをロードする
     */
    public function app($name)
    {
        return $this->enable($name);
    }

    public function enable($name)
    {
        if (array_key_exists($name, $this->_enabled))
        {
            return $this->_enabled[$name];
        }

        foreach($this->_children as $v)
        {
            if($v->_name === $name)
            {
                $v->onEnable();
                $this->_enabled[$name] = $v;
                return $v;
            }
        }
        throw new Exception\AppNotFound($name);
    }

    /**
     * 有効化された時の挙動
     */
    public function onEnable( )
    {
    }
}
/* vim: set foldmethod=marker st=4 ts=4 sw=4 et: */
