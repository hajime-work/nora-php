<?php
/**
 * Nora Project
 * ============
 *
 * @since 20150618
 * @author kurari@hajime.work
 */
Namespace Nora\Util\Reflection;

use Closure;

/**
 * DocCommentを制御する
 */
class ReflectionDocComment
{
    private $_txt;
    private $_attrs;
    private $_comment;

    /**
     * DocCommentを取得する
     *
     * @param mixed $object
     * @return string 
     */
    static public function getDocCommentRaw($object)
    {
        if ($object instanceof ReflectionMethod || $object instanceof Injection\Spec)
        {
            return $object->getDocComment();
        }

        // オブジェクトを判定
        if ( is_callable($object) && is_array($object) )
        {
            $rs = new ReflectionMethod($object[0], $object[1]);
            return $rs->getDocComment();
        }

        if ($object instanceof Closure)
        {
            $rs = new ReflectionFunction($object);
            return $rs->getDocComment();
        }

        if (is_array($object) && is_callable($object[count($object)-1]))
        {
            return self::getDocCommentRaw($object[count($object)-1]);
        }

        if (is_object($object))
        {
            $rs = new ReflectionClass($object);
            return $rs->getDocComment();
        }

        throw new Exception\CantRetriveDocComment($object);
    }

    /**
     * 作成する
     *
     * @param string $text
     * @return DocComment
     */
    static public function create($text)
    {
        if (!is_object($text))
        {
            $dc = new ReflectionDocComment($text);
            return $dc;
        }

        return self::create(
            self::getDocCommentRaw($text)
        );


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
