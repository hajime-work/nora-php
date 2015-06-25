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
use Nora;

/**
 * ベース
 *
 */
abstract class Base extends Component 
{

    private $_env;
    private $_dir;
    private $name = 'app:default';
    private $config = [];
    private $_children = [];
    private $_enabled = [];

    public function __construct( )
    {
        $this->_children = new ObjectHash();
    }

    protected function initComponentImpl( )
    {
        $this->scope()->setTag('app');
        $this->scope()->appName = $this->name;
        $this->scope()->appConfig = $this->config;

        // スコープにコンポーネントローダを設定する
        $this->scope()->addCallMethod(
            $this->scope()->componentLoader =
                ComponentLoader::createComponent(
                    $this->scope()->newScope('ComponentLoader')
                )
            );
    }

    static public function create($name, $config)
    {
        $class = get_called_class();
        $app = new $class();
        $app->name = $name;
        $app->config = $config;
        return $app;
    }


    /**
     * アプリケーション環境クラスを取得する
     */
    public function EnvFactory($name)
    {
        $class = sprintf('Nora\App\Env\%sEnv', ucfirst($name));
        return new $class();
    }


    public function add(Base $app)
    {
        $this->_children->add($app);
        return $this;
    }

    /**
     * アプリケーションを追加する
     */
    public function addApp($name, $config)
    {
        $class = get_called_class( );

        $app = $class::create($name, $config);

        // ルートの子スコープを渡す
        $app->setScope($this->scope()->newScope($name));

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
            if($v->name === $name)
            {
                $v->onEnable();
                $this->_enabled[$name] = $v;
                return $v;
            }
        }
        throw new Exception\AppNotFound($name);
    }

}
/* vim: set foldmethod=marker st=4 ts=4 sw=4 et: */
