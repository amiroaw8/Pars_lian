<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

foreach (['/', '/catalog'] as $path) {
    $request = Illuminate\Http\Request::create($path, 'GET', [], [], [], [
        'HTTP_HOST' => '127.0.0.1:8000',
        'SERVER_NAME' => '127.0.0.1',
        'REQUEST_URI' => $path,
    ]);

    $response = $kernel->handle($request);
    $content = (string) $response->getContent();
    echo $path.' status='.$response->getStatusCode().' len='.strlen($content).PHP_EOL;
    if ($response->getStatusCode() >= 500) {
        echo substr($content, 0, 300).PHP_EOL;
    }
    $kernel->terminate($request, $response);
}
