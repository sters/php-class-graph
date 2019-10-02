<?php
require_once __DIR__ . '/../vendor/autoload.php';

use ClassGraph\Dependency;
use ClassGraph\SourceList;
use ClassGraph\DotDumper;
use ClassGraph\FilteredRecursiveDependencyChecker;

$sourceList = new SourceList;
$sourceList->registerProjectRootDir(__DIR__ . '/../');
$sourceList->add(PhpParser\Lexer::class);

// skip vendor
$checker = new FilteredRecursiveDependencyChecker(
    $sourceList,
    function(string $target, string $baseclass, Dependency $dependency) {
        return strpos($target, 'vendor') === false;
    }
);
$checker->run();
// echo (new DotDumper($checker->getDependencyList()))->dump();
/*
Expect Output:

digraph "classes-dependency" {
}
*/


// skip class
$checker = new FilteredRecursiveDependencyChecker(
    $sourceList,
    function(string $target, string $baseclass, Dependency $dependency) {
        return strpos($dependency->getName(), 'PhpParser\\') === 0;
    }
);
$checker->run();
echo (new DotDumper($checker->getDependencyList()))->dump();
/*
Expect Output:

digraph "classes-dependency" {
	"PhpParser\\Lexer" -> "PhpParser\\Parser\\Tokens" [ minlen = 4 ];
	"PhpParser\\Lexer" -> "ErrorHandler\\Throwing" [ minlen = 4 ];
	"PhpParser\\Lexer" -> "PhpParser\\Error" [ minlen = 4 ];
	"PhpParser\\Lexer" -> "PhpParser\\RuntimeException" [ minlen = 4 ];
	"PhpParser\\Error" -> "PhpParser\\RuntimeException" [ minlen = 4 ];
}
*/

