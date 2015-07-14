<?php
namespace Nora\Data\CSV;

use IteratorAggregate;
use Nora\Base\Component\Component;

class Facade extends Component
{
    protected function initComponentImpl( )
    {
    }

    public function parse($file, $setting = [])
    {
        return Parser::parse($file, $settings);
    }
}

