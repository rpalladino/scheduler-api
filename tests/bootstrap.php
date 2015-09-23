<?php

use Dotenv\Dotenv;

$dotenvPath = __DIR__ . "/..";
if (file_exists($dotenvPath . "/.env")) {
    $dotenv = new Dotenv($dotenvPath);
    $dotenv->load();
}

require __DIR__ . "/../vendor/autoload.php";
