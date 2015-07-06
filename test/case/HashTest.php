<?php
namespace Nora\Base\Hash;
use Nora;
use Nora\Base;

class HashTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {

    }

    public function tearDown()
    {

    }

    public function testNewHash( )
    {
        $hash1 = Hash::newHash([], Hash::OPT_IGNORE_CASE|Hash::OPT_ALLOW_UNDEFINED_KEY_SET|Hash::OPT_ALLOW_UNDEFINED_KEY_GET);
        $hash2 = Hash::newHash([], Hash::OPT_ALLOW_UNDEFINED_KEY_SET|Hash::OPT_ALLOW_UNDEFINED_KEY_GET);
        $hash3 = Hash::newHash([], Hash::OPT_ALLOW_UNDEFINED_KEY_GET);
    }
}
# vim:set ft=php.phpunit :
