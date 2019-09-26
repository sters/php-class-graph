<?php

namespace ClassGraph;

use PhpParser\Error;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\ParserFactory;

class Traverser
{
    /** @var Parser */
    protected $parser;

    public function __construct()
    {
        $this->parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
    }

    public function traverse(string $sourceFile): Visitor
    {
        $visitor = new Visitor;

        try {
            $ast = $this->parser->parse(file_get_contents($sourceFile));
        } catch (Error $error) {
            return $visitor;
        }

        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor);
        $traverser->traverse($ast);

        return $visitor;
    }
}
