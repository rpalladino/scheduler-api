<?php

use Dotenv\Dotenv;

$dotenv = new Dotenv(__DIR__ . "/../");
$dotenv->load();

require __DIR__ . "/../vendor/autoload.php";
