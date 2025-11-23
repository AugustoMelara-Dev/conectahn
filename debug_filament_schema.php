<?php
require __DIR__ . '/vendor/autoload.php';

try {
    $r = new ReflectionClass('Filament\Schemas\Schema');
    echo "Class: " . $r->getName() . "\n";
    if ($r->hasMethod('schema')) {
        echo "Has schema() method\n";
    } else {
        echo "No schema() method\n";
    }
    if ($r->hasMethod('components')) {
        echo "Has components() method\n";
    } else {
        echo "No components() method\n";
    }
    
    // List all public methods
    foreach ($r->getMethods(ReflectionMethod::IS_PUBLIC) as $m) {
        echo "Method: " . $m->getName() . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
