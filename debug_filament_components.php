<?php
require __DIR__ . '/vendor/autoload.php';

if (class_exists('Filament\Forms\Components\TextInput')) {
    echo "Filament\Forms\Components\TextInput exists\n";
} else {
    echo "Filament\Forms\Components\TextInput NOT found\n";
}

if (class_exists('Filament\Schemas\Components\TextInput')) {
    echo "Filament\Schemas\Components\TextInput exists\n";
} else {
    echo "Filament\Schemas\Components\TextInput NOT found\n";
}
