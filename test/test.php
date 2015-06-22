<?php
namespace Nora;

require_once realpath(dirname(__dir__)).'/src/autoload.php';


$root = Scope::create('root');
$child = $root->newScope('child');
$gchild = $child->newScope('gchild');


$hash = new Base\Hash\Hash([], 4);
var_dump($hash->isIgnoreCase());

