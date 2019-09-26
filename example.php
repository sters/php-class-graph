<?php
require_once 'vendor/autoload.php';

use ClassGraph\Dependency;
use ClassGraph\DependencyList;
use ClassGraph\SourceList;
use ClassGraph\Traverser;
use ClassGraph\UmlDumper;
use ClassGraph\DotDumper;

$classRelationList = new DependencyList;

$sourceList = new SourceList;
$sourceList->registerProjectRootDir(__DIR__);
$sourceList->add(PhpParser\Lexer::class);

$traverser = new Traverser;

foreach ($sourceList as $target) {
    $visitor = $traverser->traverse($target);
    foreach ($visitor->getUses() as $u) {
        $classRelationList->addDependency($visitor->getFullClassName(), new Dependency($u));
        $sourceList->add($u);
    }
}

// echo (new UmlDumper($classRelationList))->dump();
echo (new DotDumper($classRelationList))->dump();
