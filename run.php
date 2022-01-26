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
    if (isset($argv[2]) ? $argv[2]: null) {
        list($key, $val) = explode('=', $argv[2]);
        $app->edit($val);
    }else{
        $app->getPrinter()->display("Id params not found");
    }
});

$app->registerCommand('--action=delete', function (array $argv) use ($app) {
    if (isset($argv[2]) ? $argv[2]: null) {
        list($key, $val) = explode('=', $argv[2]);
        $app->delete($val);
    }else{
      $app->getPrinter()->display("Id params not found");
    }
});

$app->registerCommand('--action=search', function (array $argv) use ($app) {
    $app->search();
});

$app->runCommand($argv);
?>