<?php
require __DIR__ . '/vendor/autoload.php';

try {
    $r = new ReflectionClass('Filament\Resources\Resource');
    $props = ['model', 'navigationIcon', 'navigationLabel', 'modelLabel', 'pluralModelLabel', 'slug', 'navigationGroup', 'navigationSort'];
    
    foreach ($props as $prop) {
        if ($r->hasProperty($prop)) {
            $p = $r->getProperty($prop);
            echo "$prop Type: " . ($p->getType() ? $p->getType() : 'None') . "\n";
        } else {
            echo "$prop: Not found\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
