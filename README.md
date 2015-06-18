Nora目指せ最終バージョン
==========================


== ファイル構成:

./src/class/:クラス群
./src/class/Base/:基礎クラス群

== ハッシュクラス
./src/class/Base/Hash/Hash.php
./src/class/Base/Hash/ObjectHash.php

配列に以下の機能を追加したもの
- 添字の大文字小文字を無視できる
- 添字への制限管理 (一度しか書けない,読み込み専用,書けない) など
- メソッドチェインでの操作 each, filter, dump など

オブジェクトハッシュ
- 添字にオブジェクトが使える
- foreachで返すのはオブジェクト本体

```php
// フラグの説明
// -----------------------------------------------------------------
// OPT_IGNORE_CASE: 大文字小文字を区別する
// OPT_ALLOW_UNDEFINED_KEY_SET: initValuesされてない値には書き込める
// OPT_ALLOW_UNDEFINED_KEY_GET: セットされていない値も取得できる
// -----------------------------------------------------------------

// 制限が一番強いタイプ
$hash = Hash::newHash(['a'=>1, 'b' => 2])
echo $hash->a; // 1
echo $hash->b; // 2
echo $hash->c; // Hash\Exception\HashKeyNotExists
$hash->c = 123; // Hash\Exception\SetOnNotAllowedKey
$hash->a = 2; 
echo $hash->a; // 2
$hash->set_hash_no_overwrite_keys(['a']);
$hash->a = 3; // Hash\Exception\OverwriteOnNotAllowedKey
$hash->set_hash_readonly_keys(['b']);
$hash->b = 3; // Hash\Exception\SetOnNotAllowedKey

// 制限が一番ゆるいタイプ
$hash = Hash::newHash([], Hash::OPT_IGNORE_CASE|Hash::OPT_ALLOW_UNDEFINED_KEY_SET|Hash::OPT_ALLOW_UNDEFINED_KEY_GET);

// メソッドチェイン
Hash::newHash(['a' => 100, 'b' => 150, 'c' => 200])->filter(function($v, $k) {
        return $v > 180; // 値が180より多ければ残す
})->each(function($v) {
    echo $v; 
}) // 200だけが表示される

// ->dump() // 現在の配列をダンプする
// ->reverse() // 配列を逆順にする
```

== スコープクラス

今までコンポーネントマネージャ、ヘルパ、モジュールと個別に管理していた部分を
ハッシュクラスをベースに使ってシンプルな構成とした。

```php
$root = Scope::createScope('root'); // rootという名前のスコープを作成
$child = $root->newScope('child');  // rootからchildという名前のスコープを作成

$child->getNames(); // root.child

echo $child->a; // Scope\Exception\UndefinedProperty

$child->a = 'hoge';
echo $child->a; // hoge

$child->b = function ( ) {
    return 'xyz';
};

echo $child->b; // Closure
echo $child->b(); // xyz

$root->sayName = ['scope', function ($s) {
    echo $s->getNames();
}];

$child->sayName(); // root.child
$root->sayName(); // root

$root->inj = ['scope', 'DB', function ($s, $db) {
    var_dump($scope);
    var_dump($db);
}];

$root->DB = function ( ) {
    static $db = null;
    if ($db === null) {
        $db = new DB();
    }
    return $db;
};


$child->inj(); //  childスコープとDBのダンプ情報がでる
```

