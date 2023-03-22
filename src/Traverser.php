<?php

namespace ClassGraph;

use PhpParser\Error;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\ParserFactory;

/**
 * Traverser is wrapper of PhpParser->parse and PhpParser->traverse
 */
class Traverser
{
    /** @var Parser */
    protected $parser;

    /** @var Visitor */
    protected $tmpVisitor;

    public function __construct()
    {
        $this->parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
    }

    /**
     * do traverse
     *
     * @param string $sourceFile
     * @return Visitor visited file information
     */
    public function traverse(string $sourceFile): Visitor
    {
        if ($this->tmpVisitor === null) {
            $this->getVisitor();
        }
        $visitor = clone $this->tmpVisitor;

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

    public function getVisitor(): Visitor
    {
        if (empty($this->tmpVisitor)) {
            $this->tmpVisitor = new Visitor;
        }

        return $this->tmpVisitor;
    }
}
