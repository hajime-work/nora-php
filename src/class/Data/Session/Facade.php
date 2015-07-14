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
    private $_lifetime = 60 * 60 * 3;
    //private $_lifetime = 5;

    public function __construct( )
    {
        $this->set_hash_option(Hash::OPT_ALLOW_UNDEFINED_KEY);
    }


    /**
     * キャッシュを使用開始する
     */
    public function connect($spec)
    {
        $this->_storage = $this->injection(['KVS', function ($kvs) use ($spec) {
            return $kvs->getStorage($spec);
        }]);
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
        $array['saved_at'] = time();

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

            Nora::logDebug('Session From Cookie: '.$sid);

            if (!$this->storage( )->has($sid)) {
                Nora::logDebug('Session Not Found: '.$sid);
                return false;
            }

            $data = $this->storage()->get($sid);

            if (is_array($data) && isset($data['saved_at']) && (time() - $data['saved_at']) > $this->_lifetime)
            {
                Nora::logDebug(sprintf('Session Expired: saved_at:%s lifetime:%s limit:%s', $data['saved_at'], time()-$data['saved_at'], $this->_lifetime));
                return false;
            }

            $this->initValues($data);
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
            $id = $this->_secure->random()->string(self::SESSION_LENGTH);

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
