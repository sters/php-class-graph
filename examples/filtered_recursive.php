<?php
require_once __DIR__ . '/../vendor/autoload.php';

use ClassGraph\Dependency;
use ClassGraph\FilteredRecursiveDependencyChecker;
use ClassGraph\SimpleDotDumper;
use ClassGraph\SourceList;

$sourceList = new SourceList;
$sourceList->registerProjectRootDir(__DIR__ . '/../');
$sourceList->add(PhpParser\Lexer::class); // base file

// filter base path
$checker = new FilteredRecursiveDependencyChecker(
    $sourceList,
    /**
     * @param string $target The target file. Also current parsing file name.
     * @param string $baseclass The target file's class. but if have.
     * @param Dependency $dependency The target file is calling this depencend class.
     */
    function (string $target, string $baseclass, Dependency $dependency) {
        return strpos($target, 'vendor') === false;
    }
);
$checker->run();
// echo (new SimpleDotDumper($checker->getDependencyList()))->dump();
/*
Expect Output:

digraph "classes-dependency" {
}
*/


// filter class
$checker = new FilteredRecursiveDependencyChecker(
    $sourceList,
    function (string $target, string $baseclass, Dependency $dependency) {
        return strpos($dependency->getName(), 'Error') !== false;
    }
);
$checker->run();
echo (new SimpleDotDumper($checker->getDependencyList()))->dump();
/*
Expect Output:

digraph "classes-dependency" {
	"PhpParser\\Lexer" -> "PhpParser\\ErrorHandler\\Throwing" [ minlen = 4 ];
	"PhpParser\\Lexer" -> "PhpParser\\Error" [ minlen = 4 ];
	"PhpParser\\ErrorHandler\\Throwing" -> "PhpParser\\Error" [ minlen = 4 ];
	"PhpParser\\ErrorHandler\\Throwing" -> "PhpParser\\ErrorHandler" [ minlen = 4 ];
}
*/
