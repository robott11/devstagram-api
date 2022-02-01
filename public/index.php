<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");

define("BASE_URL", "http://localhost/");

require __DIR__."/../vendor/autoload.php";
require __DIR__."/../config.php";
require __DIR__."/../routes.php";

$core = new Core\Core();
$core->run();
