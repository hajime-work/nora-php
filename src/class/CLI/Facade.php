<?php
namespace Nora\CLI;

use Nora\Base\Component\Componentable;

/**
 * CLI
 */
class Facade
{
    use Componentable;

    protected function initComponentImpl( )
    {
    }

    public function OptParser($description = null)
    {
        return $this->injection([
            'Environment',
            function($e) use ($description) {
                return new Option\Parser($description, $e);
            }
        ]);
    }

    public function message($message)
    {
        fwrite(STDERR, $message);
    }
}
