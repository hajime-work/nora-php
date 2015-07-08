<?php
Namespace Nora\Network\HTTP\Exception;

use Nora\Exception as NoraException;

class RequestException extends NoraException
{
    public function __construct($req)
    {
        parent::__construct('<pre>'.var_export($req->getInfo(), 1).(string) $req.'</pre>');
    }
}
