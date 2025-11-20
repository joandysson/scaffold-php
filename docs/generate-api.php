<?php
declare(strict_types=1);

namespace App\Docs;

use OpenApi\Generator;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/OpenApi.php';

$generator = new Generator();
$openApi = $generator->generate([
    __DIR__ . '/../app',
    __DIR__,
]);

file_put_contents(__DIR__ . '/api.json', $openApi->toJson());

echo "docs/api.json generated successfully." . PHP_EOL;
