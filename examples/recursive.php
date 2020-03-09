<?php
require_once __DIR__ . '/../vendor/autoload.php';

use ClassGraph\RecursiveDependencyChecker;
use ClassGraph\SimpleDotDumper;
use ClassGraph\SourceList;

$sourceList = new SourceList;
$sourceList->registerProjectRootDir(__DIR__ . '/../');
$sourceList->add(PhpParser\Lexer::class);

$checker = new RecursiveDependencyChecker($sourceList);
$checker->run();
echo (new SimpleDotDumper($checker->getDependencyList()))->dump();

/*
Output:

digraph "classes-dependency" {
	"PhpParser\\Lexer" -> "PhpParser\\Parser\\Tokens" [ minlen = 4 ];
	"PhpParser\\Lexer" -> "PhpParser\\ErrorHandler\\Throwing" [ minlen = 4 ];
	"PhpParser\\Lexer" -> "PhpParser\\Error" [ minlen = 4 ];
	"PhpParser\\Lexer" -> "PhpParser\\Comment\\Doc" [ minlen = 4 ];
	"PhpParser\\Lexer" -> "PhpParser\\Comment" [ minlen = 4 ];
	"PhpParser\\Lexer" -> "PhpParser\\RuntimeException" [ minlen = 4 ];
	"PhpParser\\Comment" -> "PhpParser\\JsonSerializable" [ minlen = 4 ];
	"PhpParser\\Comment\\Doc" -> "PhpParser\\Comment\\PhpParser\\Comment" [ minlen = 4 ];
	"PhpParser\\Error" -> "PhpParser\\RuntimeException" [ minlen = 4 ];
	"PhpParser\\ErrorHandler\\Throwing" -> "PhpParser\\Error" [ minlen = 4 ];
	"PhpParser\\ErrorHandler\\Throwing" -> "PhpParser\\ErrorHandler" [ minlen = 4 ];
}
*/
