<?php
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
    $dsn    = "mysql:dbname=".$_ENV["DB_DATABASE"].";host=".$_ENV["DB_HOST"];
    $dbUser = $_ENV["DB_USERNAME"];
    $dbPwd  = $_ENV["DB_PASSWORD"];
    $db     = new PDO($dsn, $dbUser, $dbPwd);
} catch (PDOException $e) {
    echo "ERROR: ".$e->getMessage();
    exit;
}
