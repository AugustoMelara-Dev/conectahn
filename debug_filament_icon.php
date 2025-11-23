<?php
require __DIR__ . '/vendor/autoload.php';

try {
    $r = new ReflectionClass('Filament\Resources\Resource');
    $p = $r->getProperty('navigationIcon');
    echo "Type: " . $p->getType() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
