<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\Web\Routing;

use Nora\Base\Web\Controller;
use Nora\Base\Web\Request;
use Nora\Util\Util;

/**
 * ルートオブジェクト
 *
 */
class Route implements RouteIF
{
    public $pattern, $params, $methods = [], $matched = [];

    protected $_delimiter = '/';
    private $_pattern;
    private $_spec = [];
    private $_matched_params = [];
    private $_matched_uri = false;
    private $_methods = null;

    private $_regex_delimiter = '/';
    private $_regex           = null;
    private $_keys            = [];

    public function __construct($params)
    {
        if (count($params[0]) < 1)
        {
            throw Exception\InvalidParamater($params);
        }

        list($this->methods, $this->pattern) = $this->parsePattern(array_shift($params));
        $this->params = $params;
    }


    public function getSpec( )
    {
        return $this->params;
    }

    /**
     * 与えられたパターンをパースする
     *
     * @param string $string
     * @return string
     */
    static public function parsePattern($string)
    {
        if(preg_match('/^(?:GET|POST|DELETE|PUT)/', $string))
        {
            if (false === $p = strpos($string, ' '))
            {
                $pattern = '/';
            }else{
                $methods = explode('|', $string);
                $pattern = substr($string, $p+1);
            }
        }else{
            $methods = null;
            $pattern = $string;
        }
        return [$methods, $pattern];
    }

    /**
     * Compile Pattern To Regex
     *
     * @return array(string $regex, array $keys)
     */
    public function compile ( )
    {
        if ($this->_regex === null)
        {
            $this->_keys = [];

            $pattern = str_replace(['.', ')', '*'], ['\.', ')?', '.*?'], $this->pattern);
            $pattern = str_replace($this->_regex_delimiter, '\\'.$this->_regex_delimiter, $pattern);

            $safe_d = preg_quote($this->_delimiter, $this->_regex_delimiter);

            $regex_for_pattern = '%2$s\{([\w]+)(\:([^%1$s]*))?\}%2$s';
            $regex_for_pattern = sprintf($regex_for_pattern,
                $safe_d,
                $this->_regex_delimiter
            );

            $idx = [];
            $callback = function($m) use ($safe_d, &$idx){
                $idx[$m[1]] = null;
                if (isset($m[3])) return sprintf('(?<%s>%s)', $m[1], $m[3]);
                return sprintf('(?<%s>[^%s]+)', $m[1], $safe_d);
            };

            $this->_regex = preg_replace_callback($regex_for_pattern, $callback, $pattern);
            $this->_keys = array_keys($idx);
        }
        return [$this->_regex, $this->_keys];
    }

    

    /**
     * マッチ
     *
     * @param Request\Request $req
     * @return bool|Route
     */
    public function match(Request\Request $req)
    {
        list($regex,$keys) = $this->compile();
        $regex = sprintf('%1$s^%2$s$%1$si', $this->_regex_delimiter, $regex);


        if (!preg_match($regex, $req->url(), $m, PREG_OFFSET_CAPTURE))
        {
            return false;
        }

        $matched_params    = [];
        foreach ( $keys as $k )
        {
            if (isset($m[$k]) && !empty($m[$k][0])) {
                $matched_params[$k] = $m[$k][0];
            }
        }
        $this->matched = $matched_params;

        return $this;
    }
}
