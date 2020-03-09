<?php

namespace ClassGraph;

use Composer\Autoload\ClassLoader;
use IteratorAggregate;
use PhpParser\Error;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use ReflectionMethod;

/**
 * SourceList with class name resolver supported by composer
 */
class SourceList implements IteratorAggregate
{
    /** @var string[] */
    protected $targets = [];

    /** @var ClassLoader */
    protected $loader;

    public function registerComposerClassLoader(ClassLoader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * Register a just hacky class name resolver from composer's autoloader.
     *
     * @param string $path Root path. It's finding $path/vendor/composer/autoload_real.php
     * @return void
     */
    public function registerProjectRootDir(string $path)
    {
        $autoloaderFilePath = $path . '/vendor/composer/autoload_real.php';

        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        try {
            $ast = $parser->parse(file_get_contents($autoloaderFilePath));
        } catch (Error $error) {
            return;
        }

        $visitor = new Visitor;
        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor);
        $traverser->traverse($ast);

        require_once($autoloaderFilePath);
        $ref = new ReflectionMethod($visitor->getClass(), 'getLoader');
        /** @var ClassLoader $loader */
        $loader = $ref->invoke(null);
        $this->registerComposerClassLoader($loader);
    }

    /**
     * Add more source file or class
     *
     * @param string $source
     * @return void
     */
    public function add(string $source)
    {
        if (!empty($this->loader)) {
            $file = $this->loader->findFile($source);
            if ($file !== false && file_exists($file)) {
                $source = $file;
            }
        }

        if (!file_exists($source)) {
            return;
        }

        $this->targets[] = $source;
    }

    /**
     * Implement IteratorAggregate
     *
     * @return void
     */
    public function getIterator()
    {
        $check = [];

        while (count($this->targets) > 0) {
            $target = array_pop($this->targets);
            if (!empty($check[$target])) {
                continue;
            }

            $check[$target] = true;

            yield $target;
        }
    }
}
