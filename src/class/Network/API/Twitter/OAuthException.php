<?php
namespace Nora\Network\API\Twitter;


class OAuthException extends \Nora\Exception
{
    public function __construct($msg)
    {
        parent::__construct($msg);
    }
}

