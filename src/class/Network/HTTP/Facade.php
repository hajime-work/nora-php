<?php
namespace Nora\Network\HTTP;

use Nora\Base\Event;
use Nora\Base\Component\Component;
use Nora\Network\API\Twitter;

/**
 * HTTP Client
 */
class Facade extends Component
{
    protected function initComponentImpl( )
    {
    }

    public function client ( )
    {
        return Client::createComponent($this->scope()->newScope('Client'));
    }

    public function mime( )
    {
        return Mime::createComponent($this->scope()->newScope('Mime'));
    }

    public function checkUrl($url, &$status = null)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        return $status === 200;
    }
}
