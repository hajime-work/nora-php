<?php
require_once realpath(dirname(__dir__)).'/src/autoload.php';

$nora = new Nora\Base\NoraEngine();

$nora->help();

$nora->fileSystem()->alias('@root', '/root');
$nora->fileSystem()->status(true);


$c = $nora->Configure();
$c->write('a', 'test');
$c->setValue([
    'b.f.g.h.i' => 'a'
]);

$c->write('b.c', 'testc');
$c->write('b.d', 'testd');
$c->write('b.e', 'testd');

var_Dump(
    $c->read('b')
);

$c->del('b.d');

var_Dump(
    $c->read('b')
);


var_Dump(
    $c->read('b.e')
);
$c->append('b.e', 'hoge');

var_Dump(
    $c->read('b.e')
);

$c->load([
    realpath(__dir__).'/config',
],
[
    'default', 'devel'
]);

$c->dump();

//var_dump($nora->fileSystem());

//$a = $nora->scope()->newScope()->newScope();

//var_dump($a->fileSystem());
