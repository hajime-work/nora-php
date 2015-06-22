<?php
/**
 * Nora Project
 * ============
 *
 * @since 20150618
 * @author kurari@hajime.work
 */
Namespace Nora\Util;

use ReflectionMethod;
use ReflectionFunction;
use ReflectionClass;

/**
 * DocCommentを制御する
 */
class DocComment
{
    private $_txt;
    private $_attrs;
    private $_comment;

    /**
     * 作成する
     *
     * @param string $text
     * @return DocComment
     */
    static public function create($text)
    {
        $dc = new DocComment($text);
        return $dc;
    }

    /**
     * テキストを指定してDocCommentを起動する
     *
     * @param string $text
     */
    public function __construct($text)
    {
        $this->_txt = $text;
        $lines = preg_split("/\r|\n/", $text);

        $comment = [];

        for($i=0; $i<count($lines); $i++)
        {
            $line = trim($lines[$i]);
            if (in_array($line,['/**','/*','*/','**/'])) continue;
            $line = ltrim($line, '* ');
            if ($line[0] == '@') break;
            $comment[] = $line;
        }

        $attrs = [];
        for(; $i<count($lines); $i++)
        {
            $line = trim($lines[$i]);
            $line  = ltrim($line, '* ');
            if (empty($line) || $line === '/') continue;

            $key   = substr(strtok($line, ' '),1);
            $value = strtok("\n");
            if ($value === false) $value = null;

            if(!isset($attrs[$key]))
            {
                $attrs[$key] = [];
            }
            array_push($attrs[$key], $value);
        }

        $this->_comment = $comment;
        $this->_attrs = $attrs;
    }


    /**
     * コメントだけを取り出す
     *
     * @return string
     */
    public function comment()
    {
        return trim(
            implode(
                "\n",
                $this->_comment
            )
        );
    }

    /**
     * アトリビュートがあるか
     *
     * @param string $name
     * @return bool
     */
    public function hasAttr($name)
    {
        return array_key_exists($name, $this->_attrs);
    }

    /**
     * アトリビュートを取得する
     *
     * @param string $name
     * @return bool
     */
    public function getAttr($name, $offset = null)
    {
        $data = isset($this->_attrs[$name]) ?  $this->_attrs[$name]: [];

        if ($offset === null)
        {
            return $data;
        }

        return isset($data[$offset]) ?
            $data[$offset]:
            null;
    }



    /**
     * 文字列にする
     *
     * @return string
     */
    public function __toString( )
    {
        return $this->_txt;
    }
}

/* vim: set foldmethod=marker st=4 ts=4 sw=4 et: */
