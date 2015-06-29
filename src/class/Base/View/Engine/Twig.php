<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\View\Engine;

use Twig_Autoloader;
use Twig_Environment;
use Twig_Lexer;
use Twig_SimpleFilter;

require_once 'Twig/Autoloader.php';
Twig_Autoloader::register();


/**
 * View Rendering Engine
 *
 */
class Twig extends Base
{
    public function initComponentImpl()
    {
        $this->_env = $this->scope()->injection([
            'FileLoader',
            'FileSystem',
            function($fl, $fs)
            {
                $isDebug = true;

                $env = new Twig_Environment(
                    new TwigLoaderAdapter($fl), [
                        'cache' =>  $fs->getPath('@cache/twig'),
                        'debug' => $isDebug
                    ]
                );

                $filter = new Twig_SimpleFilter('var_dump', function ($string) {
                    return var_dump($string, 1);
                });

                $env->addFilter($filter);

                $env->setLexer(
                    new Twig_Lexer($env, [
                        'tag_comment'  => ['{#', '#}'],
                        'tag_block'    => ['{%', '%}'],
                        'tag_variable' => ['{@', '@}']
                    ])
                );

                return $env;
            }
        ]);
    }

    public function render($file, $params = [])
    {
        return $this->_env->render($file, $params);
    }
}

