<?php

if (php_sapi_name() !== 'cli') {
    exit;
}

require __DIR__ . '/vendor/autoload.php';

use Bidvestcli\App;
use Respect\Validation\Validator as v;

$app = new App();

$app->registerCommand('--action=add', function (array $argv) use ($app) {
    
    $app->add();
    
});

$app->registerCommand('--action=edit', function (array $argv) use ($app) {
    list($key, $val) = explode('=', $argv[2]);
    $app->edit($val);
});

$app->runCommand($argv);
?>