<?php
namespace Nora\Network\API\Twitter;
use Nora\Network\API\OAuth;
use Nora\Network\HTTP;

class FacadeTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {

    }

    public function tearDown()
    {

    }

    public function testMain()
    {
        // 正常に起動出来ているか
        $scope = new \Nora\Scope\Scope('TwitterTest');
        $scope->setComponent('HTTP', ['scope', function($s) {
            return HTTP\Facade::createComponent($s->newScope('HTTP'));
        }]);
        $Twitter =  Facade::createComponent($scope->newScope('Twitter'));
        $this->assertEquals('TwitterTest.Twitter', $Twitter->scope()->getNames());

        // アカウントを作成する
        $key      = 'SNdkOlmviIdbonkCXnCPAnm4O';
        $secret   = '0fub5Hxw2DeNHBfpk1oWdyJZeq0gs1QQncmLOry6XlvxclSnj9';

        $Account = $Twitter->account($key, $secret);

        vaR_dump( $Twitter->getRequestTokenURL($Account));

        /*


        $url = 'https://api.twitter.com/oauth/request_token';
        $req = new OAuth\Request($key, $secret, $url);
        $req->initComponent($scope);
        $result =  $req->execute( );
        var_dump($result->getInfo());

        echo $result;


        parse_str($result->body(), $v);
        print("<a 
            href=\"http://api.twitter.com/oauth/authorize?oauth_token={$v['oauth_token']}\">Twitter</a>");

        var_dump($v);
         */

        die();






        //$Account->
        //http://api.twitter.com/oauth/request_token 

        $param_array = array();
        $param_array["oauth_consumer_key"] = "XXXXXXXXX";       // twitterで取得したConsumer key
        $param_array["oauth_nonce"] = md5(uniqid(rand(), true));    // ランダムな英数字のみからなる文字列32文字を設定
        $param_array["oauth_signature_method"] = "HMAC-SHA1";       // HMAC-SHA1固定
        $param_array["oauth_timestamp"] = mktime();         // いわゆるUNIXタイムスタンプ
        $param_array["oauth_version"] = "1.0";              // 1.0固定

        // パラメータ名の昇順でソートされていないといけないためソート
        ksort($param_array);

        // シグネチャ作成用にパラメータの文字列を生成
        // oauth_consumer_key=XXXXXXXXX&oauth_nonce=xxxxx...のようなパラメータ文字列を生成
        $param = "";
        foreach ($param_array as $key => $value) {
            if($param != "") $param .="&";
            $param .= $key . "=" . $value;
        }

        // シグネチャの生成
        // 「HTTPのメソッド名&URLエンコードされたAPIのURL&URLエンコードされた昇順に並べたパラメータ文字列」を作ります
        $signature_param = "GET&" . urlencode($api_url) . "&" . urlencode($param);

        // SHA1ハッシュを取得し、BASE64エンコードします。
        // これでoauth_signatureの値が完成です。
        $oauth_signature = base64_encode(hash_hmac("sha1", $signature_param, urlencode($oauth_consumer_secret) . "&", true));


        /**

        // Bearer Tokenを取得する
        $Twitter->getBearerToken($Account);

        // Invalidates
        // 出来ないっぽい
        //$Twitter->invalidateBearerToken($Account);

        $Twitter->userTimeline($Account, [
            'count' => 100,
            'screen_name' => 'hajime_mat'
        ]);

        **/






/**
* Invalidates the Bearer Token
* Should the bearer token become compromised or need to be invalidated for any reason,
* call this method/function.
function invalidate_bearer_token($bearer_token){
    $encoded_consumer_key = urlencode(CONSUMER_KEY);
    $encoded_consumer_secret = urlencode(CONSUMER_SECRET);
    $consumer_token = $encoded_consumer_key.':'.$encoded_consumer_secret;
    $base64_encoded_consumer_token = base64_encode($consumer_token);
    // step 2
    $url = "https://api.twitter.com/oauth2/invalidate_token"; // url to send data to for authentication
    
    $ch = curl_init();  // setup a curl
    curl_setopt($ch, CURLOPT_URL,$url);  // set url to send to
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // set custom headers
    curl_setopt($ch, CURLOPT_POST, 1); // send as post
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return output
    curl_setopt($ch, CURLOPT_POSTFIELDS, "access_token=".$bearer_token.""); // post body/fields to be sent
    $header = curl_setopt($ch, CURLOPT_HEADER, 1); // send custom headers
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $retrievedhtml = curl_exec ($ch); // execute the curl
    curl_close($ch); // close the curl
    return $retrievedhtml;
}
*/


        /*
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint.$api);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            sprintf(
                "Authorization: Basic %s", base64_encode(urlencode($key).':'.urlencode($secret))
            )
        ]);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return output
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials"); // post body/fields to be sent
        $header = curl_setopt($ch, CURLOPT_HEADER, 1); // send custom headers
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $retrievedhtml = curl_exec ($ch); // execute the curl
        curl_close($ch);
        echo $retrievedhtml;
        */

    }
}

# vim:set ft=php.phpunit :
