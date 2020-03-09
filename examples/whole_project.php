<?php
require_once __DIR__ . '/../vendor/autoload.php';

use ClassGraph\Dependency;
use ClassGraph\DependencyListCSVDumper;
use ClassGraph\DependencyListDumper;
use ClassGraph\FilteredRecursiveDependencyChecker;
use ClassGraph\SimpleDotDumper;
use ClassGraph\SimpleUmlDumper;
use ClassGraph\SourceList;

function showUsage()
{
    echo 'Usage: php examples/whole_project.php -p PROJECT_PATH [-d list,csv,dot,uml (default: list)]';
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


$sourceList = new SourceList;
$sourceList->registerProjectRootDir($projectPath);
foreach (glob($projectPath . '/*.php') as $file) {
    $sourceList->add($file);
}
foreach (glob($projectPath . '/**/*.php') as $file) {
    $sourceList->add($file);
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
        return strpos($target, 'vendor') === false;
    }
);
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
