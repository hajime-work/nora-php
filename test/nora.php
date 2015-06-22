<?php
require_once realpath(dirname(__dir__)).'/src/autoload.php';

Nora::fileSystem()->alias('@root', '/root');
//$nora->fileSystem()->status(true);

$c = Nora::Configure();
$c->write('a', 'test');
$c->setValue([
    'b.f.g.h.i' => 'a'
]);
$c->write('b.c', 'testc');
$c->write('b.d', 'testd');
$c->write('b.e', 'testd');
$c->read('b');
$c->del('b.d');
$c->read('b');
$c->read('b.e');
$c->append('b.e', 'hoge');
$c->read('b.e');
$c->load([
    realpath(__dir__).'/config',
],
[
    'default', 'devel'
]);


var_Dump($c('b'));
var_dump(Nora::fileSystem());

//Nora::help($c);


//var_dump($nora->fileSystem());

//$a = $nora->scope()->newScope()->newScope();

//var_dump($a->fileSystem());
