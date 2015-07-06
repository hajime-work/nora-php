<?php
namespace Nora\Data\Model;

use Nora\Data\DataBase;
use Nora\Data\DataSource;

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
        // DBの準備
        $DB = new DataBase\Facade();
        $DB->setConnection([
            'test' => 'mongo://mongodb.fspot/test?replicaSet=fspot'
        ]);

        // データソースの準備
        $DS = new DataSource\Facade();
        $DS->setDBHandler($DB);
        $DS->setDataSource(['shop' => 'test://test?pkey=shop_id']);

        // データソースの初期化
        $DS->getDataSource('shop')->adapter()->drop();
        $DS->getDataSource('shop')->adapter()->createIndex([
            'shop_id' => 1
        ], [ 'unique' => true ]);


        // モデルの準備
        $Model = new Facade();
        $Model->setDataSourceHandler($DS);
        $Shop = $Model->getHandler('shop');


        // モデルを作ってから保存
        $model = $Shop->create([
            'shop_id' => 3,
            'name' => 'そうすけ'
        ]);
        $model->save();

        // 配列を直接保存
        $Shop->insert([
            'shop_id' => 1,
            'name' => 'はじめ'
        ]);

        // モデルを取得
        $m = $Shop->get(1);

        $this->assertFalse($m->isChanged());

        $m->name = 'まつもと';

        $this->assertTrue($m->isChanged());

        $this->assertEquals(['name' => 'まつもと'], $m->getChangedVars());

        $m->save();

        foreach($Shop->find()->limit(1)->order(['shop_id'=>-1])->query(['name' => 'まつもと']) as $r)
        {
            var_Dump($r);
        }
    }
}

# vim:set ft=php.phpunit :
