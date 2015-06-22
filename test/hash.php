<?php
namespace Nora\Base\Hash;
use Nora;
use Nora\Base;

require_once realpath(dirname(__dir__)).'/src/autoload.php';

$hash = Hash::newHash([], Hash::OPT_IGNORE_CASE|Hash::OPT_ALLOW_UNDEFINED_KEY_SET|Hash::OPT_ALLOW_UNDEFINED_KEY_GET);
$hash->AA = b;
Nora::debug($hash);

$hash = Hash::newHash([], Hash::OPT_ALLOW_UNDEFINED_KEY_SET|Hash::OPT_ALLOW_UNDEFINED_KEY_GET);
$hash->AA = b;
Nora::debug($hash);
Nora::debug($hash->AA);
Nora::debug($hash->BB);


// initValuesしたものだけが設定可能
$hash = Hash::newHash([], Hash::OPT_ALLOW_UNDEFINED_KEY_GET);
$hash->initValues(['BB' => null]);
$hash->BB = 'x';

try {
    $hash->AA = b;
} catch(\Exception $e) {
    var_dump('OK 1/3');
}

try {
    $hash->AA = b;
} catch(Base\Exception $e) {
    var_dump('OK 2/3');
}

try {
    $hash->AA = b;
} catch(Exception\SetOnNotAllowedKey $e) {
    var_dump('OK 3/3');
}
Nora::debug($hash);

// 設定されたものだけ取得可能
$hash = Hash::newHash(['a'=>'b']);

Nora::debug($hash);
try {
    Nora::debug($hash->AA);
} catch(Exception\HashKeyNotExists $e) {
    Nora::debug('OK');
}

var_Dump($hash->a);
var_Dump($hash['a']);


// イテレーション
foreach(Hash::newHash(['a','b','c','d']) as $v)
{
    var_Dump($v);
}

Hash::newHash(['a','b','c','d'])->each(function($v, $k) {
    printf("Key: %s, Value: %s".PHP_EOL, $k, $v);
});

Hash::newHash(['a','b','c','d'])->each([1,2], function($v, $k) {
    printf("Key: %s, Value: %s".PHP_EOL, $k, $v);
});

printf("フィルター\n");
Hash::newHash(['a','b','c','d'])->filter(function($v, $k) {
    return in_array($v, ['a','d']);
})->dump();


printf("トリガー\n");

$hash = Hash::newHash(['a'=>'b']);
$hash->registerSetValHandler(function($k, $v, $t) {
    printf("%s => %s\n", $t[$k], $v);
    return $v;
});
$hash->registerGetValHandler(function($k, $v) {
    printf("%s:%sを取り出し\n", $k, $v);
    return $v;
});

$hash->a = 1;
$hash->a = 2;
$hash->a = 3;

foreach($hash as $k=>$v)
{
    var_Dump($v);
}

var_Dump($hash->toArray());
