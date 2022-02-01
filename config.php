<?php
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

global $config;
$config = [
    "dbname"     => $_ENV["DB_DATABASE"],
    "dbhost"     => $_ENV["DB_HOST"],
    "dbuser"     => $_ENV["DB_USERNAME"],
    "dbpass"     => $_ENV["DB_PASSWORD"],
    "jwt_secret" => $_ENV["JWT_SECRET"]
];

global $db;

try {
    $dsn = "mysql:dbname=".$config["dbname"].";host=".$config["dbhost"];
    $db  = new PDO($dsn, $config["dbuser"], $config["dbpass"]);
} catch (PDOException $e) {
    echo "ERROR: ".$e->getMessage();
    exit;
}
