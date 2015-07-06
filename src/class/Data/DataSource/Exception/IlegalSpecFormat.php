<?php
Namespace Nora\Data\DataSource\Exception;

use Nora\Exception as NoraException;

class IlegalSpecFormat extends NoraException
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
