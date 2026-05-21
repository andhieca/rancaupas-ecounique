<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach (App\Models\Tourism::all() as $t) {
    echo $t->id . ' | image: ' . $t->image . ' | gallery: ' . json_encode($t->gallery) . "\n";
}
