<?php
require __DIR__ . '/vendor/autoload.php';

try {
    $r = new ReflectionClass('Filament\Resources\Resource');
    $m = $r->getMethod('table');
    echo "Method: " . $m->getName() . "\n";
    foreach ($m->getParameters() as $param) {
        echo "Param: " . $param->getName() . " Type: " . ($param->getType() ? $param->getType() : 'None') . "\n";
    }
    echo "Return Type: " . ($m->getReturnType() ? $m->getReturnType() : 'None') . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
