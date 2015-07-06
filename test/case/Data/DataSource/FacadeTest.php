<?php
namespace Nora\Data\DataSource;
use Nora\Data\DataBase;

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
        // データベース
        $DB = new DataBase\Facade();
        $DB->setConnection([
            'test' => 'mongo://mongodb.fspot/test?replicaSet=fspot'
        ]);

        // データソース
        $DS = new Facade();
        $DS->setDBHandler($DB);
        $handler = $DS->create('test://test', [
            'pkey' => 'shop_id'
        ]);

        // データソースを削除
        $handler->adapter()->drop();

        // 重複データを作成
        $handler->insert([
            'shop_id' => 1,
            'cont' => 1
        ]);
        $handler->insert([
            'shop_id' => 1,
            'cont' => 2
        ]);

        $this->assertEquals(2, $handler->count());

        // 重複データを探す
        $res = $handler->adapter()->aggregate([
            [
                '$group' => [
                    '_id' => [
                        '$shop_id'
                    ],
                    'dups' => [
                        '$push' =>  [
                            'id' => '$_id',
                            'cont' => '$cont'
                        ]
                    ],
                    'count' =>  [
                        '$sum'  => 1
                    ]
                ],
            ],
            [
                '$match' => [
                    'count' => [
                        '$gt' => 1
                    ]
                ]
            ]
        ]);

        // 最後のひとつ以外を削除する
        foreach($res['result'] as $v)
        {
            $last = array_pop($v['dups']);

            foreach($v['dups'] as $vv)
            {
                $handler->delete([
                    '_id' => $vv['id']
                ]);
            }
        }

        // インデックスを作成
        $handler->adapter()->createIndex([
            'shop_id' => 1
        ], [
            'unique' => true
        ]);


        $this->assertEquals(1, $handler->count());
        $this->assertEquals(2, $handler->get(1)['cont']);


        // setDataSource と getDataSource
        $DS->setDataSource([
            'shop' => 'test://test?pkey=shop_id'
        ]);

        $handler = $DS->getDataSource('shop');
        $handler->insert([
            'shop_id' => 2,
            'name' => 'はじめ'
        ]);

        $this->assertEquals(2, $handler->count());
        $this->assertEquals('はじめ', $handler->get(2)['name']);
    }
}

# vim:set ft=php.phpunit :
