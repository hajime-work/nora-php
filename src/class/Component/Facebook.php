<?php
namespace Nora\Component;

use Nora\Base\Component\Componentable;
use Nora\Network\API\Facebook\Facade as Base;

/**
 * フェイスブック連携
 *
 * <code>
 * // 認証ページヘ飛ばす
 * $url = $fb->oauth()->requestTokenURL($fb->consumer('tsuguten'), 'http://dev01.fuzoku.gallery/facebook-cb', ['email','public_profile']);
 * header('Location: '.$url);
 * </code>
 *
 * <code>
 * // アクセストークンの取得
 * $code = $req->get()->getVal('code');
 * $token = $fb->oauth()->accessToken($fb->consumer('tsuguten'), $code,  'http://dev01.fuzoku.gallery/facebook-cb');
 *
 * // グラフAPIへのアクセス
 * json_decode($fb->graph( )->get($ses->facebook_token['access_token'], '/me')));
 * </code>
 */
class Facebook extends Base
{
    use Componentable;

    protected function initComponentImpl( )
    {
        parent::initComponentImpl();

        $this->injection([
            'Configure',
            'DataBase',
            function($c, $db) {
                // 設定からコンシューマを読み込む
                $this->setConsumer($c('api.facebook.consumers'));
            }
        ]);
    }
}
