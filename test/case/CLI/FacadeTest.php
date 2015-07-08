<?php
namespace Nora\CLI;

use Nora;

class FacadeTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {

    }

    public function tearDown()
    {

    }

    public function testMain()
    {
        $cli = Nora::CLI();
        $env = Nora::Environment();

        $env->setArgv([
            'file.php',
            '-v',
            '--name',
            'hajime',
            '--age=31',
            'hoge',
            'hoge2'
        ]);


        $parser = $cli->OptParser('テストコマンド v0.1');

        $parser->addOption('version', [
            'short_name'  => '-v',
            'long_name'   => '--version',
            'action'      => true,
            'description' => 'バージョンを表示'
        ]);

        $parser->addOption('name', [
            'short_name'  => '-n',
            'long_name'   => '--name',
            'action'      => '=',
            'description' => '名前'
        ]);

        $parser->addOption('age', [
            'short_name'  => '-a',
            'long_name'   => '--age',
            'action'      => '=',
            'description' => '年齢'
        ]);

        $parser->addArgument('file', [
            'description' => '処理ファイル'
        ]);
        $parser->addArgument('file2', [
            'description' => '処理ファイル'
        ]);
        var_dump(
            $parser->options()
        );

        var_dump(
            $parser->args()
        );

        echo $parser;


            // オプションの追加
            // $parser->addOption('date', array(
            //   'short_name'  => '-d',
            //     'long_name'   => '--date',
            //       'action'      => 'StoreString',
            //         'help_name'   => 'YYYYMMDD',
            //           'description' => '対象となる日付',
            //           ));



    }
}

# vim:set ft=php.phpunit :
