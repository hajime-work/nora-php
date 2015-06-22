<?php
/**
 * Nora Project
 * ============
 *
 * @since 20150618
 * @author kurari@hajime.work
 */
Namespace Nora\Util\Reflection;

/**
 * DocCommentを制御する
 */
trait ReflectionTrait
{
    private $_doc_comment;

    private function DocComment( )
    {
        if ($this->_doc_comment == null)
        {
            $this->_doc_comment = ReflectionDocComment::create($this);
        }
        return $this->_doc_comment;
    }

    /**
     * コメントだけを取り出す
     *
     * @return string
     */
    public function comment()
    {
        return $this->DocComment()->comment();
    }

    /**
     * アトリビュートがあるか
     *
     * @param string $name
     * @return bool
     */
    public function hasAttr($name)
    {
        return $this->DocComment()->hasAttr($name);
    }

    /**
     * アトリビュートを取得する
     *
     * @param string $name
     * @return bool
     */
    public function getAttr($name, $offset = null)
    {
        return $this->DocComment()->getAttr($name, $offset);
    }

}

/* vim: set foldmethod=marker st=4 ts=4 sw=4 et: */
