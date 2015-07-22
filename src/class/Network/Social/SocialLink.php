<?php
namespace Nora\Network\Social;

class SocialLink
{
    public function twitterShare($url, $body = null)
    {
        $base = 'https://twitter.com/share';

        $params = [
            'url' => $url,
            'text' => $body
        ];

        return $base.'?'.http_build_query($params);
    }

    public function facebookShare($url = null, $body = null)
    {
        $base = 'https://www.facebook.com/sharer/sharer.php';

        $params = [
            'u' => $url,
        ];

        return $base.'?'.http_build_query($params);
    }
}
