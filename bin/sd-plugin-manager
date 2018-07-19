#!/usr/bin/env php
<?php

use Symfony\Component\Console\Application;

require_once __DIR__ . '/../vendor/autoload.php';

$application = new Application();
$commandsClassBase = 'sd\\SwPluginManager\\Command\\';

// iterate through commands
$fileIterator = new DirectoryIterator(__DIR__ . '/../src/Command');
foreach ($fileIterator as $file) {
    if (false === $file->isFile()) {
        continue;
    }

    try {
        $commandName = $commandsClassBase . $file->getBasename('.' . $file->getExtension());
        $application->add(new $commandName());
    } catch (RuntimeException $exception) {
        echo 'Could not load command "' . $commandName . '". Is there a class in src/ that does not belong there?' .
            PHP_EOL;
    }
}

$application->run();
