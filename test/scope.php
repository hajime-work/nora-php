<?php
namespace Nora\Scope;
use Nora\Nora;
use Nora\Base;

require_once realpath(dirname(__dir__)).'/src/autoload.php';

$root = Scope::createScope('root');
$child = $root->newScope('child');
$gchild = $child->newScope('gchild');

var_dump($gchild->getNames());

$gchild->a = 'x';

// a は読み出せる
$gchild->a;
$gchild->markReadonlyProp('a');

try
{
    // b は読み出せない
    $gchild->b;
} catch (Exception\UndefinedProperty $e) {
    var_dump($e->getMessage());
}

try
{
    // 書き込めない
    $gchild->a = 'b';
} catch (Exception\ReadonlyProperty $e) {
    var_dump($e->getMessage());
}

var_dump($gchild->a);


// 解決できない呼び出し
try
{
    $gchild->a();
} catch (Exception\CantSolvedCall $e)
{
    var_dump($e->getMessage());
}

// ヘルパーを足す
$gchild->b = function( ) {
    return 'hello';
};

// 親の上書きできない
try
{
    $gchild->parent = 'a';
} catch (Exception\ReadonlyProperty $e) {
    var_dump($e->getMessage());
}


$gchild->getNames();

var_Dump($gchild->b());


// スコープ継承
$child->c = function( ) {
    return 'oya';
};
$gchild->addCallMethod($child);
var_Dump($gchild->c());

// スコープ継承2
$root->d = function( ) {
    return 'root';
};
$child->addCallMethod($root);

var_Dump($gchild->d());

$gchild->markNoOverwriteProp('xxx');
$gchild->xxx = 'a';

try
{
    $gchild->xxx = 'a';
}
catch (Exception\LockedProperty $e) {
    var_dump($e->getMessage());
}

// スコープで多重継承
$hoge = new Scope();
$hoge['a'] = function ( ) { return 'a'; };
$hoge['b'] = function ( ) { return 'b'; };

$gchild->addCallMethod($hoge);

$hoge2 = new Scope();
$hoge2['a'] = function() { var_Dump('実行されたよ'); return 'hoge2.a';};
$hoge2['hogehogehoge'] = function() { var_Dump('実行されたよ'); return 'hoge2.hogehogehoe';};
$hoge2['injection_call'] = ['scope', 'scope:aaa', 'scope:hogehogehoge()', 'DB', function ($s,$a,$b, $db) {var_Dump(func_get_args()); return $db;}];

$gchild->aaa = 'hoge';
$gchild->addCallMethod($hoge2);

echo '==========';


// コンポーネントローダーのシュミレーション
$compLoader = new Scope( );
$compLoader->instanaceManager = new Scope();
$compLoader->DB = function ( ) use ($compLoader) {
    if ($compLoader->instanaceManager->hasVal('DB'))
    {
        return $compLoader->instanaceManager->getVal('DB');
    }

    var_dump('DB作成');
    $compLoader->instanaceManager->setVal('DB', new \StdClass());
    return $compLoader->instanaceManager->getVal('DB');
};

// ルートスコープにコンポーネントローダを設定
$gchild->rootScope()->addCallMethod($compLoader);

// コンポーネントに値を入れる
$gchild->DB()->a = 1;
$gchild->DB()->b = 2;

var_dump($gchild->injection_call()->b);

