<?php
namespace Nora\Network\Mail;

use Nora\Network\Mail\Parser;

/**
 * Mailの構造体
 * <ex>
        $mail_file = TEST_PROJECT_PATH.'/sample.mail';

        $mail = Nora::mail_parse($mail_file);

        // 添付ファイルを埋め込んだHTMLを生成
        file_put_contents('a.html', $mail->toHtml());


        // 普通メールを作成する
        $mail = $module
            ->mail( )
            ->subject('あいうえおあいうえおあいうえおあいうえおあいうえおあいうえお')
            ->from('hajime@nora-worker.net', '松本　創')
            ->to('hajime.matsumoto@avap.co.jp', 'まつもと　はじめ')
            ->Cc('game.hajime.matsumoto@gmail.com', 'まつもと　はじめ')
            ->Bcc('hajime@nora-worker.net', 'まつもと　はじめ')
            ->html(file_get_contents('a.html'))
            ;

        $module->send($mail);
        
        // オルタナティブにする
        $mail = $module
            ->mail( )
            ->subject('オルタナティブ')
            ->from('hajime@nora-worker.net')
            ->to('hajime@nora-worker.net')
            ->alternative(
                [
                    'type' => 'plain',
                    'body' => 'テキスト'
                ],
                [
                    'type' => 'html',
                    'body' => file_get_contents('a.html'),
                ]
            );

        $module->send($mail);
        
        // マルチパートミクスドメールを送る
        $mail = $module
            ->mail( )
            ->subject('マルチパートミクスド')
            ->from('hajime@nora-worker.net')
            ->to('hajime@nora-worker.net')
            ->mixed(
                [
                    'type' => 'alternative',
                    'parts' => [
                        [
                            'type' => 'plain',
                            'body' => 'テキスト'
                        ],
                        [
                            'type' => 'html',
                            'body' => file_get_contents('a.html')
                        ]
                    ]
                ],
                [
                    'type' => 'attachment',
                    'body' => file_get_contents(__FILE__),
                    'name' => 'php',
                    'content-type' => 'text/php; charset=utf8'
                ],
                [
                    'type' => 'inline',
                    'body' => file_get_contents(__FILE__),
                    'id' => 'hoge',
                    'name' => 'php',
                    'content-type' => 'text/php; charset=utf8'
                ]
            );


        $module->send($mail);

        $mail = $module->parse($mail_file);

        // 宛先を変えて送信
        $module->send($mail, 'hajime@nora-worker.net', 'hajime@nora-worker.net');
 * </ex>
 */
class Mail extends Part
{
    private $_rcpt_list       = [];
    private $_mail_from       = '';
    private $_header_encoding = 'ISO-2022-JP';
    private $_size = 0;

    public function setSize($size)
    {
        $this->_size = $size;
        return $this;
    }

    public function getSize($size)
    {
        $this->_size = $size;
        return $this;
    }

    /**
     * 件名を取得する
     */
    public function decodeSubject ( )
    {
        return Parser::mimeHeaderDecode(
            $this->getHeaderRaw('Subject')
        );
    }

    /**
     * 件名ヘッダを設定する
     */
    public function subject($subject)
    {
        $this->addHeader('Subject', mb_encode_mimeheader(
            $subject,
            $this->_header_encoding
        ));
        return $this;
    }

    /**
     * Fromヘッダを設定する
     */
    public function from($email, $name = null)
    {
        $this->addHeader('From', $this->convertAddress($name, $email));
        $this->mailfrom($email);
        return $this;
    }

    public function mailfrom($email)
    {
        $this->_mail_from = $email;
        return $this;
    }

    public function getMailFrom( )
    {
        return $this->_mail_from;
    }

    public function getRcptTo( )
    {
        return $this->_rcptTo_list;
    }

    /**
     * Toヘッダを設定する
     */
    public function to ($email, $name = null)
    {
        $this->addHeader('To', $this->convertAddress($name, $email));
        $this->rcptTo($email);
        return $this;
    }

    /**
     * Ccヘッダを設定する
     */
    public function cc ($email, $name = null)
    {
        $this->addHeader('Cc', $this->convertAddress($name, $email));
        $this->rcptTo($email);
        return $this;
    }

    /**
     * Bccを設定する
     */
    public function Bcc ($email)
    {
        $this->rcptTo($email);
        return $this;
    }

    /**
     * 実際の送信先を設定する
     */
    public function rcptTo($email)
    {
        $this->_rcptTo_list[] = $email;
        return $this;
    }

    /**
     * Fromを取得する
     */
    public function getFrom($decode = false)
    {
        $from = $this->getHeaderRaw('From');

        if (preg_match('/(.+)<(.+)>/', $from, $m))
        {
            $name=$m[1];
            $mail=$m[2];
        }else{
            $name=null;
            $mail=$from;
        }

        $mail=trim(trim($mail), '><');

        if ($decode === true)
        {
            $name=mb_decode_mimeheader($name);
            return $name."<$mail>";
        }
        return $mail;
    }

    /**
     * Fromを取得する
     */
    public function getTo($decode = false)
    {
        $to = $this->getHeaderRaw('To');

        if (preg_match('/(.+)<(.+)>/', $to, $m))
        {
            $name=$m[1];
            $mail=$m[2];
        }else{
            $name=null;
            $mail=$to;
        }

        $mail=trim(trim($mail), '><');

        if ($decode === true)
        {
            $name=mb_decode_mimeheader($name);
            return $name."<$mail>";
        }
        return $mail;
    }

    public function getDate($decode = false, $format = 'Y-m-d G:i:s')
    {
        if ($decode === false)
        {
            return $this->getHeaderRaw('date');
        }
        return date($format,strtotime($this->getDate()));
    }


    private function convertAddress($name, $email)
    {
        if (empty($name)) return $email;
        return sprintf( "%s <%s>",
            mb_encode_mimeheader( $name, $this->_header_encoding ),
            $email
        );
    }

    /**
     * メールをHTMLに変換する
     */
    public function toHtml()
    {
        $result = $this->searchPartByContentType('text/html');

        if ($result === false)
        {
            $result = $this->searchPartByContentType('text/plain');
            if ($result === false)
            {
                $html = $this->getBody(true);
            }
            else
            {
                $html = $result[0]->getBody(true);
            }
            $html = nl2br($html);
        }else{
            $html = $result[0]->getBody();

            // cid:xxx を変換する
            $html = preg_replace_callback('/cid:([^"\'\s]+)/', function($m) {
                $result = $this->searchPart(function($part) use ($m) {
                    return $m[1] === trim($part->getHeader('Content-ID'),'<>');
                });
                if ($result !== false)
                {
                    $part = $result[0];
                    return sprintf(
                        'data:%s;base64,%s',
                        $part->getHeader('content-type'),
                        base64_encode($part->getBody())
                    );
                }
            }, $html);
        }
        return $html;
    }

    public function getImageParts()
    {
        if (!$parts = $this->getAttachParts())
        {
            $parts = $this->searchPart(function($part) {
                return 
                    0 === stripos($part->getHeader('Content-Disposition'),'inline') &&
                    0 === stripos($part->getHeader('Content-Type'), 'image/jpeg');
            });
        }
        return $parts;
    }

    public function getImageSources( )
    {
        $ret=[];

        if (!$parts = $this->getAttachParts())
        {
            $parts = $this->searchPart(function($part) {
                return 
                    0 === stripos($part->getHeader('Content-Disposition'),'inline') &&
                    0 === stripos($part->getHeader('Content-Type'), 'image/jpeg');
            });
        }
        // 添付ファイル
        foreach($parts as $p)
        {
            $ret[] = sprintf("data:%s;base64,%s",
                $p->getHeader('content-type'),
                base64_encode($p->getBody())
            );
        }
        return $ret;
    }

    public function readImage($offset = 0, $target_width = 100)
    {
        if (is_string($target_width))
        {
            switch($target_width){
            case 'xxsmall':
                $target_width = 50;
                break;
            case 'xsmall':
                $target_width = 100;
                break;
            case 'small':
                $target_width = 200;
                break;
            case 'lerge':
                $target_width = 200;
                break;
            case 'full':
                $target_width = -1;
                break;
            }

        }
        $list = $this->getImageParts();

        if( isset($list[$offset]) )
        {
            // 元イメージを取得
            $img = imagecreatefromstring($list[$offset]->getBody());

            // Width=X Height=Yを取得
            list($width, $height) = [imageSX($img), imageSY($img)];

            // 無縮小
            if ($target_width === -1)
            {
                header('Content-Type: image/jpeg');
                imagejpeg($img);
                imagedestroy($img);
                return;
            }

            // 縦横比の計算
            $proportion = $width / $height;

            // -になる場合縦長
            if ($proportion < 1) {
                // ベースは高さにする
                $target_height = $target_width;
                // 横幅を決める
                $target_width = $target_width * $proportion;
            }else{
                // 高さを決める
                $target_height = $target_width / $proportion;
            }

            // リサイズ後のベースになるイメージを作成
            $new_img = imagecreatetruecolor($target_width, $target_height);

            // 縮小サンプリング処理
            $result = imagecopyresampled(
                $new_img,
                $img,
                0,0,0,0,
                $target_width,
                $target_height,
                $width,
                $height
            );


            header('Content-Type: image/jpeg');
            imagejpeg($new_img, null,100);
            imagedestroy($img);
            imagedestroy($new_img);
            return;
        }
    }

    /**
     * 添付ファイルを取得する
     */
    public function getAttachParts( )
    {
        $result = $this->searchPart(function($part) {
            return 0 === stripos($part->getHeader('Content-Disposition'),'attachment');
        });

        return $result;
    }

    public function getSubject($decode = false)
    {
        if ($decode === true)
        {
            return mb_decode_mimeheader($this->getHeader('Subject'));
        }
        return $this->getHeader('Subject');
    }

    public function getMailBody ( )
    {
        $res = false;
        if (false !== $parts = $this->searchPartByContentType('text/html'))
        {
            $res = $parts[0];
        }elseif (false !== $parts = $this->searchPartByContentType('text/plain'))
        {
            $res = $parts[0];
        }
        if (false === $res || false === $res->getBody())
        {
            return false;
        }
        return $res->getBody();
    }
}
