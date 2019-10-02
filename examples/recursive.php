<?php
require_once __DIR__ . '/../vendor/autoload.php';

use ClassGraph\SourceList;
use ClassGraph\DotDumper;
use ClassGraph\RecursiveDependencyChecker;

$sourceList = new SourceList;
$sourceList->registerProjectRootDir(__DIR__ . '/../');
$sourceList->add(PhpParser\Lexer::class);

$checker = new RecursiveDependencyChecker($sourceList);
$checker->run();
echo (new DotDumper($checker->getDependencyList()))->dump();

/*
Output:

digraph "classes-dependency" {
	"PhpParser\\Lexer" -> "PhpParser\\Parser\\Tokens" [ minlen = 4 ];
	"PhpParser\\Lexer" -> "ErrorHandler\\Throwing" [ minlen = 4 ];
	"PhpParser\\Lexer" -> "PhpParser\\Error" [ minlen = 4 ];
	"PhpParser\\Lexer" -> "Comment\\Doc" [ minlen = 4 ];
	"PhpParser\\Lexer" -> "PhpParser\\Comment" [ minlen = 4 ];
	"PhpParser\\Lexer" -> "PhpParser\\RuntimeException" [ minlen = 4 ];
	"PhpParser\\Error" -> "PhpParser\\RuntimeException" [ minlen = 4 ];
}
*/
