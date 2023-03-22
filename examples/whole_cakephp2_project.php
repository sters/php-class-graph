<?php
require_once __DIR__ . '/../vendor/autoload.php';

use ClassGraph\Dependency;
use ClassGraph\DependencyListCSVDumper;
use ClassGraph\DependencyListDumper;
use ClassGraph\FilteredRecursiveDependencyChecker;
use ClassGraph\SimpleDotDumper;
use ClassGraph\SimpleUmlDumper;
use ClassGraph\SourceList;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;

function showUsage()
{
    echo 'Usage: php examples/whole_cakephp2_project.php -p PROJECT_PATH [-d list,csv,dot,uml (default: list)]';
}

$projectPath = '';
$dumperName = 'list';
foreach ($argv as $c => $v) {
    switch (strtolower($v)) {
        case '-p':
        case '--project':
            $projectPath = $argv[$c + 1] ?? '';
            break;
        case '-d':
        case '--dumper':
            $dumperName = strtolower($argv[$c + 1] ?? '');
    }
}

if (
    empty($projectPath) ||
    (!in_array($dumperName, ['dot', 'uml', 'list', 'csv']))
) {
    showUsage();
    exit(1);
}
if (!file_exists($projectPath)) {
    echo "${projectPath} is not found.\n\n";
    showUsage();
    exit(1);
}

// register whole project PHP files
$sourceList = new SourceList;
$sourceList->registerProjectRootDir($projectPath);
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($projectPath)) as $file) {
    if ($file->getExtension() === 'php' && strpos($file->getPath(), 'vendor') === false) {
        $sourceList->add($file);
    }
}

// skip vendor
$checker = new FilteredRecursiveDependencyChecker(
    $sourceList,
    /**
     * @param string $target The target file. Also current parsing file name.
     * @param string $baseclass The target file's class. but if have.
     * @param Dependency $dependency The target file is calling this depencend class.
     */
    function (string $target, string $baseclass, Dependency $dependency) {
        $ignored = function (array $filters) use ($target, $baseclass, $dependency): bool {
            foreach ($filters as $filter) {
                if (strpos($target, $filter) !== false ||
                    strpos($baseclass, $filter) !== false ||
                    strpos($dependency->getName(), $filter) !== false) {
                    return false;
                }
            }
            return true;
        };
        $baseIgnored = $ignored(['vendor','Config/Migration','Test']);
        $appIgnored = $ignored(['AppModel','AppController']);

        return $baseIgnored && $appIgnored;
    }
);

$checker->getTraverser()->getVisitor()->addHook(function(Node $node) {
    if ($node instanceof Stmt\PropertyProperty) {
        if ($node->name === 'belongsTo' || $node->name->name === 'belongsTo') {
            foreach ($node->default->items as $item) {
                $this->addUsesForNameParts($item->key->value);
            }
        }

        if ($node->name === 'uses' || $node->name->name === 'uses') {
            foreach ($node->default->items as $item) {
                $this->addUsesForNameParts($item->value->value);
            }
        }

        if ($node->name === 'components' || $node->name->name === 'components') {
            foreach ($node->default->items as $item) {
                $this->addUsesForNameParts($item->value->value . 'Component');
            }
        }
    }

    if ($node instanceof Node\Const_) {
        if ($node->name === 'MODELS' || $node->name->name === 'MODELS') {
            foreach ($node->value->items as $item) {
                $this->addUsesForNameParts($item->value->value);
            }
        }
    }
});

$checker->run();

$dumperClass = DependencyListDumper::class;
switch ($dumperName) {
    case 'csv':
        $dumperClass = DependencyListCSVDumper::class;
        break;
    case 'uml':
        $dumperClass = SimpleUmlDumper::class;
        break;
    case 'dot':
        $dumperClass = SimpleDotDumper::class;
        break;
}
echo (new $dumperClass($checker->getDependencyList()))->dump();
