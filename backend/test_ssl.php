<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $response = \Illuminate\Support\Facades\Http::withHeaders([
        'Content-Type' => 'application/json'
    ])->send('POST', 'https://httpbin.org/post', [
        'body' => '{}'
    ]);
    
    echo "Body sent: " . $response->json()['data'];
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage();
}
