<?php
namespace Nora\Network\Social;

class SocialLink
{
    public function twitterShare($body, $url = null)
    {
        $base = 'https://twitter.com/share';

        $params = [
            'url' => $url,
            'text' => $body
        ];

        return $base.'?'.http_build_query($params);
    }
}
