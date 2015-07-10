<?php
namespace Nora\Data\Session;

use Nora\Data\DataBase;
use Nora\Base\Component;
use Nora\Base\Hash\Hash;
use Nora\Util\Spec\SpecLine;
use Nora;

/**
 *  セッション
 */
class Facade extends Hash
{
    use Component\Componentable;

    const SESSION_LENGTH = 32;
    const SESSION_KEY    = 'NORA_SID';

    private $_secure;
    private $_cookie;
    private $_kvs;

    private $_session_id;
    private $_storage;

    public function __construct( )
    {
        $this->set_hash_option(Hash::OPT_ALLOW_UNDEFINED_KEY);
    }

    /**
     * ストレージを作成する
     */
    protected function createStorage()
    {
        return $storage = $this->_kvs->getStorage('dir:///tmp/session');
    }


    protected function initComponentImpl( )
    {
        $this->injection([
            'Secure',
            'Cookie',
            'KVS',
            function($secure, $cookie, $kvs)
            {
                $this->_secure = $secure;
                $this->_cookie = $cookie;
                $this->_kvs = $kvs;
            }
        ]);

        // セッションを開始する
        $this->start();
    }

    public function start( )
    {
        if (!$this->retriveSessionID())
        {
            // セッションIDがなければ作る
            $this->_session_id = $this->genSessionID();

            $this->_cookie->set(self::SESSION_KEY, $this->_session_id);
        }

        register_shutdown_function([$this, 'save']);
    }

    /**
     * セッションIDの再生成
     */
    public function regen( )
    {
        if (headers_sent())
        {
            $this->logWarning('すでにヘッダーが送信されています');
            return false;
        }
        // 現在のセッションIDを破棄
        $this->storage()->delete($this->_session_id);

        // 生成
        $this->_session_id = $this->genSessionID();

        // クッキーを発行する
        $this->_cookie->set(self::SESSION_KEY, $this->_session_id);

        return $this->_session_id;
    }

    /**
     * ストレージを取得する
     */
    protected function storage()
    {
        if($this->_storage === null)
        {
            $this->_storage = $this->createStorage();
        }
        return $this->_storage;
    }

    /**
     * 規定時間過ぎたセッションを削除する
     */
    public function swipe($time = 172800) // 60*60*48
    {
        $this->storage()->swipte($time);
    }

    /**
     * セッションを保存する
     */
    public function save( )
    {
        // セッションID
        $sid = $this->sid();

        // データ
        $array = $this->toArray();

        $this->storage( )->set($sid, $array);
    }

    /**
     * 現在のセッションIDを取得
     */
    public function sid( )
    {
        return $this->_session_id;
    }

    /**
     * セッションを復元する
     */
    public function retriveSessionID( )
    {
        if($this->_cookie->has(self::SESSION_KEY))
        {
            $sid = $this->_cookie->get(self::SESSION_KEY);

            if (!$this->storage( )->has($sid)) return false;

            $this->initValues($this->storage()->get($sid));

            $this->_session_id = $sid;
            return true;
        }
        return false;
    }

    /**
     * セッションIDを生成する 
     */
    protected function genSessionID( )
    {
        do {
            // 規定バイト数のセッションIDを作成
            $id = $this->_secure->randomString(self::SESSION_LENGTH);

        }while($this->isExistsSessionID($id)); // 被ってたら取直す処理

        // 予約
        $this->storage()->ensure($id);

        return $id;
    }

    /**
     * セッションIDの存在を確認する
     */
    protected function isExistsSessionID($id)
    {
        return false;
    }
}
